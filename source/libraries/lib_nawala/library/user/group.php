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

abstract class NFWUserGroup
{
	/*
	 * Method to get the parent usergroup(s) for presenting eiter a hidden form with the groupid as client id or a select list, if the user is in more than one parent group
	 *
	 * About the logic:
	 * In near any case, we want to protect our data! That's why we set a group in the user manager that all new registered will be assigned to.
	 * That is our top (parent) usergroup. Not the public in this case!
	 * We should create a standard process and a clear and logical standard.
	 *
	 * Case 1: We have flat groups (Registered; Registered -> Group 1; Registered -> Group 2; and so on...)
	 *         In this case, the parent group is registered, because this is a typical set for a single company
	 *
	 * Case 2: We have complex nested groups (Registered; Registered -> Company 1; Registered -> Company 2; ...
	 *                                                    Company 1 -> Group 1; Company 1 -> Group 2; Company 2 -> Group 1; Company 2 -> Group 2; .....)
	 *         In this case, the user is either in one of the companies, voila we could have a hidden form with the usergroup id in it as value
	 *         or, if the user is in more than one company => we have a select list with all companies the user is assigned to. (NO Childs, only the companies)
	 *
	 * Possible Solution:
	 * All Companies should follow naming conventions eg.: "company_Company 1" or "comp#_Company 4457, Inc."
	 *               to get a clear name we could strip the "company_" or "comp#_" to get a clear Name of the Usergroup for the select lists
	 *  ==> The title could have up to 100 chars and is only to identify the usergroup in the backend/frontend. We don't need such nice names, because to have all informations for companies, we have to add additional tables in the database.
	 *      Latest if we want to work with documents and pdf outputs we need to have nice documents with all company informations on it. It requies only the groupid to identify a company!
	 *
	 * SO WE COME TO ONE SOLUTION TO DO SO:
	 *
	 * We add a small identifier at the beginning of each parent: "#" (Let's say we're hash tagging the parents!)
	 *
	 * No Solution:
	 * A profile plugin where the user or the company could set the parent. This only works if the user is absolutely in one company!!!
	 *
	 * @return    int    Returned id of the current user. 0 will be returned if no user is logged in.
	 *
	 */
	public function getParents()
	{
		$db = JFactory::getDbo();
		$userId = (int) JFactory::getUser()->id;

		$query = $db->getQuery(true);

		$query
			->select( array('a.id', 'a.title') )
			->from('#__usergroups AS a')
			->join('LEFT', '#__user_usergroup_map AS b ON (a.id = b.group_id)')
			->where('b.user_id = ' . $userId)
			->where('a.title LIKE \'#%\'');

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}
}