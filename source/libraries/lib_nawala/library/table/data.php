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
 * @since		6.0
 */

defined('_JEXEC') or die();

/*
 * Global Class to check, store and update values in the database
 *
 * @return the values of the row if success, else false // if create the id will be pushed automatically to the object by JTable. In case of $db, we have to use $db->insertedId to get the id
 *
 */
abstract class NFWTableData
{
	// Stores the unique values for the ifExist check
	protected static $existValues = '';
	
	/**
	 * Method to store values with related assets based on array $config
	 *
	 * @fields	$data		array of fields to use as columns and values ( eg. array('id' => 1, 'title' => 'The Title') )
	 *
	 * @return			true if update, return id if new is successfull, else false
	 */
	public static function store($type, $prefix = '', $data = array(), $config = array())
	{
		// Check for an Id
		if ( isset($data['id']) ) {
			if( $data['id'] > 0 && (int) $data['id'] ) {
				$doAction = 'update';
			} else if ( $data['id'] == 0 || $data['id'] == '') {
				$doAction = 'new';
			} else {
				return false;
			}
		} else {
			$data['id'] = '';
			$doAction = 'new';
		}

		// Create the JTable object
		$table = JTable::getInstance($type, $prefix, $config = array());

		// Bind values to the object to check for missing defaults
		if ($table->bind( $data ) === true) {
			$table->created = isset($data['created']) ? $data['created'] : NFWDate::getCurrent('MySQL');
			$table->created_by = isset($data['created_by']) ? $data['created_by'] : NFWUser::getId;
		} else {
			return false;
		}

		// Check and store the values // check if we should use if its true or if it is not false
		if ($table->check() === true) {
			$table->store();
			if ( $doAction == 'new' ) {
				return $table->id;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
}