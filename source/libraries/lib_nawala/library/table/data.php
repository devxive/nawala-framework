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
	// Set the rowId from the store() if new
	protected static $rowId = 0;

	// Set the related assetId
	protected static $assetId = 0;

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
		// Check for an Id and store it to global
		if ( isset($data['id']) ) {
			if ( $data['id'] > 0 ) {
				self::$rowId = $data['id'];
			}
		} else {
			$data['id'] = self::$rowId;
		}

		// Create the JTable object
		$table = JTable::getInstance($type, $prefix, $config = array());

		// Check for missing values
		$data['created'] = isset($data['created']) ? $data['created'] : NFWDate::getCurrent('MySQL');
		$data['created_by'] = isset($data['created_by']) ? $data['created_by'] : NFWUser::getId();

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
		if( !$table->store() ) {
			NFWHtmlMessage::set('Warning', 500, $table->getError() );
			return false;
		} else {
			if ( self::$rowId > 0 ) {
				return true;
			} else {
				self::$rowId = $table->id;
				return $table->id;
			}
		}
	}
}