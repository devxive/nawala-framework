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
 * @since		6.0
 */

defined('_NFW_FRAMEWORK') or die();

/**
 * Nawala Module Helper Class
 * Global Support for install procedures
 *
 */
class NFWModuleHelper
{
	/**
	 * Method to add a menu module after a menu type is created
	 *
	 * @param     array    $data    Module properties
	 *
	 * @return    mixed             True on success, false on error
	 *
	 */
	public static function add($data)
	{
		// Check for data
		if ( !isset($data['module']) ) {
			return false;
		}

		$module = array();

		// Check for attribs
		$module['title']	= isset($data['title'])	? $data['title']	: 'Menu';
		$module['note']	= isset($data['note'])	? $data['note']	: '';
		$module['content']	= isset($data['content'])	? $data['content']	: '';
		$module['ordering']	= isset($data['ordering'])	? $data['ordering']	: 1;
		$module['position']	= isset($data['position'])	? $data['position']	: 'sidebar';
		$module['published']	= isset($data['published'])	? $data['published']	: 1;
		$module['module']	= isset($data['module'])	? $data['module']	: 'mod_custom';
		$module['access']	= isset($data['access'])	? $data['access']	: 2;
		$module['showtitle']	= isset($data['showtitle'])	? $data['showtitle']	: 0;
		$module['params']	= isset($data['params'])	? $data['params']	: '{}';
		$module['client_id']	= isset($data['client_id'])	? $data['client_id']	: 1;
		$module['language']	= isset($data['language'])	? $data['language']	: '*';

		// Save the module
		$moduleId = NFWDatabase::save( 'modules', $data );

		if ( isset($moduleId['id']) ) {
			// Link the menus to the module
			NFWDatabase::save( 'modules_menu', array('moduleid' => $moduleId['id'], 'menuid' => 0) );
		} else {
			return false;
		}
	}



    /**
     * Method to set module params such as position, publishing state and title
     *
     * @param     object     $manifest    Instance of the XML manifest
     *
     * @return    boolean                 True on success, False on error
     */
    public static function setModuleParams(&$manifest)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Get module name, position and published state
        $name  = $manifest->name;
        $pos   = (isset($manifest->position) ? $manifest->position : '');
        $pub   = (isset($manifest->published) ? (int) $manifest->published : 0);
        $title = (isset($manifest->show_title) ? (int) $manifest->show_title : 1);

        // Get the module id
        $query->select('id')
              ->from('#__modules')
              ->where('module = ' . $db->quote($name));

        $db->setQuery((string) $query);
        $id = (int) $db->loadResult();

        if (!$id) return false;

        // Update params
        $query->clear();
        $query->update('#__modules');
        if ($pos) $query->set('position = ' . $db->quote($pos));
        if ($pub) $query->set('published = ' . $db->quote($pub));
        $query->set('showtitle = ' . $db->quote($title));
        $query->where('module = ' . $db->quote($name));

        $db->setQuery((string) $query);
        $db->execute();

        return true;
    }
}
