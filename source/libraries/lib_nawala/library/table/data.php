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
	public static $returnMsg = array();

	/**
	 * Method to store values with related assets based on array $config.
	 *
	 * @param     string        $type      Should be the filename in the /admin/table folder of the appropriate table to load. It should be also the second part of the classname.
	 * @param     string        $prefix    Should be the first part of the classname as described above.
	 *                                     Example:
	 *                                         "class MycomponentTableOptions extends JTable"
	 *                                                |               |
	 *                                                first Part      second Part
	 *                                                |               |
	 *                                                prefix          type
	 *                                     
	 * @param     mixed         $data      Array or object of fields or arrays to use as columns and values ( eg. array('id' => 1, 'title' => 'The Title') ).
	 *                                     To save multiple rows at one, you have to put all single arrays in an object
	 * @param     array         $config    Don't know ;)    
	 * @param     bool          $debug     If true it will return the data arrays with more informations instead of only false
	 *
	 * @return    mixed/bool               Returns a message with all datas set if true, else return false
	 */
	public static function store($type, $prefix = '', $data = false, $config = array(), $debug = false)
	{
		if ( is_object($data) ) {
			foreach($data as $data) {
				// Create the JTable object
				$table = JTable::getInstance($type, $prefix, $config);

				// Check for an id and store it to global, else we use the init global (0)
				// Note: If id is > 0, JTable performs an update
				if ( isset($data['id']) ) {
					self::$rowId = (int) $data['id'];
				} else {
					$data['id'] = self::$rowId;
				}

				// Check for missing values and add them
				if ( $data['id'] > 0 ) {
					$data['modified'] = isset($data['modified']) ? $data['modified'] : NFWDate::getCurrent('MySQL');
					$data['modified_by'] = isset($data['modified_by']) ? $data['modified_by'] : NFWUser::getId();
				} else {
					$data['created'] = isset($data['created']) ? $data['created'] : NFWDate::getCurrent('MySQL');
					$data['created_by'] = isset($data['created_by']) ? $data['created_by'] : NFWUser::getId();
				}

				// Bind data
				if ( !$table->bind( $data ) ) {
					$data['ErrorBind'] = $table->getError();
					$returnMsg[] = $data;
					if($debug) {
						return $returnMsg;
					} else {
						return false;
					}
				}
				
				// Check data
				if ( !$table->check() ) {
					$data['ErrorCheck'] = $table->getError();
					$returnMsg[] = $data;
					if($debug) {
						return $returnMsg;
					} else {
						return false;
					}
				}

				// Store data
				if( !$table->store() ) {
					$data['ErrorStore'] = $table->getError();
					$returnMsg[] = $data;
					if($debug) {
						return $returnMsg;
					} else {
						return false;
					}
				} else {
					$data['id'] = $table->id;
					$returnMsg[] = $data;
				}
			}

			return $returnMsg;
		} else if ( is_array($data) ) {
			// Create the JTable object
			$table = JTable::getInstance($type, $prefix, $config);

			// Check for an id and store it to global, else we use the init global (0)
			// Note: If id is > 0, JTable performs an update
			if ( isset($data['id']) ) {
				self::$rowId = (int) $data['id'];
			} else {
				$data['id'] = self::$rowId;
			}

			// Check for missing values and add them
			if ( $data['id'] > 0 ) {
				$data['modified'] = isset($data['modified']) ? $data['modified'] : NFWDate::getCurrent('MySQL');
				$data['modified_by'] = isset($data['modified_by']) ? $data['modified_by'] : NFWUser::getId();
			} else {
				$data['created'] = isset($data['created']) ? $data['created'] : NFWDate::getCurrent('MySQL');
				$data['created_by'] = isset($data['created_by']) ? $data['created_by'] : NFWUser::getId();
			}

			// Bind data
			if ( !$table->bind( $data ) ) {
				$data['NFWTableBindError'] = $table->getError();
				$returnMsg[] = $data;
					if($debug) {
						return $returnMsg;
					} else {
						return false;
					}
			}
				
			// Check data
			if ( !$table->check() ) {
				$data['NFWTableCheckError'] = $table->getError();
				$returnMsg[] = $data;
					if($debug) {
						return $returnMsg;
					} else {
						return false;
					}
			}

			// Store data
			if( !$table->store() ) {
				$data['NFWTableStoreError'] = $table->getError();
				$returnMsg[] = $data;
					if($debug) {
						return $returnMsg;
					} else {
						return false;
					}
			} else {
				$data['id'] = $table->id;
				$returnMsg[] = $data;
			}

			return $returnMsg;
		}
	}
}