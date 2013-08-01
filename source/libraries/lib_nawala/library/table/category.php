<?php
/**
 * @project		XAP Project - Xive-Application-Platform
 * @subProject	Nawala Framework - A PHP and Javascript framework
 *
 * @package		XiveIRM.Library
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

defined('_JEXEC') or die();


abstract class NFWTableCategory
{
	/**
	 * Method to store a category with related assets based on array $config
	 *
	 * @fields	$data    array of fields to use as columns and values ( eg. array('id' => 1, 'title' => 'The Title') )
	 *
	 * @return	         true if success, else false
	 */
	public static function store($data = array())
	{
		// Set the id array if none exist
		if ( !isset($data['id']) ) {
			$data['id'] = '';
		}

		// Create the JTable Category object
		$table = JTable::getInstance('Category', 'JTable');

		// Check for missing values
		$data['parent_id'] = isset($data['parent_id']) ? $data['parent_id'] : 1;
		$data['level'] = isset($data['level']) ? $data['level'] : 1;
		$data['alias'] = isset($data['alias']) ? $data['alias'] : strtolower($table->title);
		$data['published'] = isset($data['published']) ? $data['published'] : 1;
		$data['access'] = isset($data['access']) ? $data['access'] : 1;
		$data['created_user_id'] = isset($data['created_user_id']) ? $data['created_user_id'] : NFWUser::getId();

		// Bind data
		if ( !$table->bind( $data ) ) {
			NFWHtmlMessage::set('Warning', 500, $table->getError() );
			return false;
		}

		// Check data
		if ( !$table->check() ) {
			NFWHtmlMessage::set('Warning', 500, $table->getError() );
			return false;
		}

		// Store data
		if( $table->store() ) {
			// Rebuild the path immediately
			if ( !$table->rebuildPath($table->id) ) {
				NFWHtmlMessage::set('Warning', 500, $table->getError() );
				return false;
			}

			// Check if we have a correct table id, else we wont return yet, and fwd to db update method
			if($table->parent_id > 0) {
				return $table;
			}
		} else {
			NFWHtmlMessage::set('Warning', 500, $table->getError() );
			return false;
		}

		// Check if the parent id is set not to 0 (dont knowing problems why the parent and level is set to 0 if store() method
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Prepare the query
		$query
			->update($db->quoteName('#__categories'))
			->set('parent_id = ' . $db->quote($data['parent_id']))
			->set('level = ' . $db->quote($data['level']))
			->where('id = ' . $db->quote($table->id));

		$db->setQuery($query);
		try {
			$result = $db->execute();
			return $table->id;
		} catch (Exception $e) {
			NFWHtmlMessage::set('Warning', 500, $e->getMessage() );
			return false;
		}
	}
}