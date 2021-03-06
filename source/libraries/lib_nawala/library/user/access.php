<?php
/**
 * @version     5.0.0
 * @package     NAWALA FRAMEWORK
 * @subPackage  NUserAccess
 * @copyright   Copyright (C) 1997 - 2013 by devXive - research and development. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      devXive <support@devxive.com> - http://devxive.com
 */

defined('_JEXEC') or die;

abstract class NUserAccess
{
	var $user;

	public function __construct($id = false)
	{
		if (!$id) {
			self::$user = JFactory::getUser();
		} else {
			self::$user = JFactory::getUser($id);
		}
	}

	/*
	 * Global Method to get authorised actions for the current user based on either the components global settings or the component / sections (as set in access.xml in admin) and an itemId.
	 *	- See @itemId desc below
	 *	So we can check against this object if the user can do the action or not
	 *	Simply use "if(NFactory::getPermissions('com_mycomponent')->core.create) { do something }
	 *
	 * @component		string		The name of the component we which to check the permissions for.
	 * @section		string		The name of the secion (component, category, special section from custom component, like tabapps in xiveirm).
	 * @sectionsRowId	int		The id of the item with its own acl and therefore its own assets entry in the #__assets table.
	 *					For XiveIRM i.e. it is the id of the tabapp config entry.
	 * @itemId		string		The table.id of the item itself. Used for check if edit.own is possible. If not it overrides the core.edit.own with "null" to get a clear object to work with!!!
	 *
	 * @return 		jobject	Returned JObject with all ACL informations (no viewing access level).
	 *
	 */
	public function getPermissions($component, $section = false, $sectionsRowId = 0, $item = 0)
	{
		$user = JFactory::getUser();
		$permissionsObject = new JObject;

		if (!$section && empty($sectionsRowId)) {
			$assetName = $component;
		} else {
			$assetName = $component . '.' . $section . '.' . (int) $sectionsRowId;
		}

		if(!$section) {
			$actions = JAccess::getActions($component);
		} else {
			$actions = JAccess::getActions($component, $section);
		}

		foreach ($actions as $action) {
			// Check if we have enough informations, to check the edit.own action. Override the action if condition is not set!
			if( $action->name == 'core.edit.own' && !empty($item) ) {
				$itemArray = explode('.', $item);
				$table = '#__' . $itemArray[0];

				$db = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query
					->select('created_by')
					->from($db->quoteName($table))
					->where('id = ' . $db->quote($itemArray[1]) . '');

				$db->setQuery($query);

				$result = $db->loadResult();

				if($result != $user->id) {
					$canEditOwn = null;
				} else {
					$canEditOwn = $user->authorise($action->name, $assetName);
				}

				$permissionsObject->set($action->name, $canEditOwn);
			} else {
				$permissionsObject->set($action->name, $user->authorise($action->name, $assetName));
			}
		} // End foreach $actions

		return $permissionsObject;
	}
}