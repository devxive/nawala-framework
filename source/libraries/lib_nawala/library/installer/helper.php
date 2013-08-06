<?php
/**
 * @project		XAP Project - Xive-Application-Platform
 * @subProject	Nawala Framework - A PHP and Javascript framework
 *
 * @package		NFW.Library
 * @subPackage	Framework
 * @version		6.0
 *
 * @author		devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright		Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense	devXive Proprietary Use License (http://www.devxive.com/license)
 *
 * @since		3.2
 */

defined('_NFW_FRAMEWORK') or die();


/**
 * Nawala Installer Helper Class
 * Global Support for install procedures
 *
 */
abstract class NFWInstallerHelper
{
	/**
	 * Method to restore the assets from a previous component install
	 *
	 * @param     string     $element    The name of the component to restore
	 *
	 * @return    boolean                True on success, False on error
	 */
	static function restoreAssets($element)
	{
		$asset_bak = JTable::getInstance('Asset');
		$asset_new = JTable::getInstance('Asset');

		// Check if we have a backup asset container from a previous install
		if ($asset_bak->loadByName($element . '_bak')) {
			// Yes, then try to load the current (new) one
			if ($asset_new->loadByName($element)) {
				// Delete the current asset
				if ($asset_new->delete()) {
					// And make the old one the current again
					$asset_bak->name = $element;

					if (!$asset_bak->store()) {
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Method to set the admin component menu item as child of a st.s parent
	 *
	 * @param     string     $component The component name
	 * @param     string     $parent    The component name of the parent menu item
	 *
	 * @return    boolean                True on success, False on error
	 */
	static function setComponentChildMenuItem($component, $parent)
	{
		static $parent_menu_item = null;

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Find the parent component admin menu item
		if (is_null($parent_menu_item)) {
			$query->select('id')
				->from('#__menu')
				->where('menutype = ' . $db->quote('main'))
				->where('title = ' . $db->quote( strtoupper($parent) ))
				->where('client_id = 1');

			$db->setQuery($query);
			$parent_menu_item = (int) $db->loadResult();
		}

		if (!$parent_menu_item) {
			return false;
		}

		// Find the menu item id of this component
		$query->clear();
		$query->select('id')
			->from('#__menu')
			->where('menutype = ' . $db->quote('main'))
			->where('title = ' . $db->quote( strtoupper($component) ))
			->where('client_id = 1');

		$db->setQuery($query);
		$menu_item = (int) $db->loadResult();

		if (!$menu_item) {
			return false;
		}

		$menu = JTable::getInstance('menu');

		// Set the new parent item
		if ($menu->load($menu_item)) {
			$menu->setLocation($parent_menu_item, 'last-child');

			if (!$menu->store()) {
				return false;
			}
		}
		else {
			return false;

		}

		return true;
	}


	/**
	 * Method to add a menu type
	 *
	 * @param     array      $data      Menutype properties
	 * @param     array      $module    Add a menu module based on the datas given to create a menu_type
	 *
	 * @return    mixed                 Return id of the inserted menutype if success, false on error
	 *
	 * @example                         // Create a menutype in the #__menu_type table and add a related menu module
	 *                                  $returnedId = NFWInstallerHelper::addMenuType( array('menutype' => 'example', 'title' => 'Example', 'description' => 'Example Menu'), true );
	 */
	public static function addMenuType($data = false, $module = false)
	{
		// Check for data
		if ( !$data ) {
			return false;
		}

		$result = NFWDatabase::save('menu_types', $data);

		if ( $module ) {
			$moduleData = array('module' => 'mod_menu', 'title' => $data['title'], 'note' => $data['description'], 'params' => '{"menutype":"' . $data['menutype'] . '"}');
			NFWModuleHelper::add($moduleData);
		}

		if ( $result['status'] == true ) {
			return $result['id'];
		} else {
			return false;
		}
	}


	/**
	 * Method to add a menu item in the XiveIRM site navigation menu
	 *
	 * @param     array      $data        Menu item properties
	 * @param     string     $menutype    Type of the menu, eiter mainmenu (standard) or any other menu
	 *
	 * @return    mixed                   Return inserted id on success, false on error
	 *
	 * @example                           // Create a Example menu item in the mainmenu menu
	 *                                    $component = 'com_example'
	 *                                    $com = JComponentHelper::getComponent($component);
	 *                                    $eid = (is_object($com) && isset($com->id)) ? $com->id : 0;
	 *
	 *                                    if ($eid) {
	 *                                        $item = array(
	 *                                            'title' => 'Example',
	 *                                            'alias' => 'example',
	 *                                            'link' => 'index.php?option=' . $component . '&view=example',
	 *                                            'component_id' => $eid
	 *                                        );
	 *
	 *                                        NFWInstallerHelper::addMenuItem($item, 'examplemenu');
	 *                                    }
	 */
	public static function addMenuItem($data, $menutype = 'mainmenu')
	{
		// Add any missing default properties
		if (!isset($data['menutype']))     $data['menutype']     = $menutype;
		if (!isset($data['parent_id']))    $data['parent_id']    = '1';
		if (!isset($data['level']))        $data['level']        = '1';
		if (!isset($data['published']))    $data['published']    = '1';
		if (!isset($data['type']))         $data['type']         = 'component';
		if (!isset($data['component_id'])) $data['component_id'] = 0;
		if (!isset($data['language']))     $data['language']     = '*';
		if (!isset($data['access']))       $data['access']       = '1';
		if (!isset($data['img']))          $data['img']          = 'class:none';
		if (!isset($data['params']))       $data['params']       = '{}';
		if (!isset($data['ordering']))     $data['ordering']     = 0;

		$data['id'] = null;

		// Save the menu item
		$row = JTable::getInstance('menu');

		$row->setLocation($data['parent_id'], 'last-child');

		if (!$row->bind($data)) {
			return false;
		}

		if (!$row->check()) {
			return false;
		}

		if (!$row->store()) {
			return false;
		}

		$dataHelper = array(
			'id' => (int) $row->id,
			'parent_id' => (int) $data['parent_id'],
			'level' => (int) $data['level']
		);

		NFWDatabase::save('menu', $dataHelper);

		$row->parent_id = $data['parent_id'];
		$row->level = $data['level'];

		$row->setLocation($data['parent_id'], 'last-child');

		if (!$row->rebuildPath($row->id)) {
			return false;
		}

		return $row->id;
	}



    /**
     * Method to set the publishing state of a plugin
     *
     * @param     string     $name     The name of the plugin
     * @param     integer    $state    The new state of the plugin
     *
     * @return    boolean              True on success, False on error
     */
    public static function publishPlugin($name, $state = 0)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Get the plugin id
        $query->select('extension_id')
              ->from('#__extensions')
              ->where('name = ' . $db->quote($name))
              ->where('type = ' . $db->quote('plugin'));

        $db->setQuery((string) $query);
        $id = (int) $db->loadResult();

        if (!$id) return false;

        // Update params
        $query->clear();
        $query->update('#__extensions')
              ->set('enabled = ' . $db->quote($state))
              ->where('extension_id = ' . $db->quote($id));

        $db->setQuery((string) $query);
        $db->execute();

        return true;
    }
}
