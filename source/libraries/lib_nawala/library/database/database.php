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
 * Nawala Database Class
 * Global Support for Database procedures
 *
 */
abstract class NFWDatabase
{
	/**
	 * Method to get a table/columns object from the current table
	 *
	 * @return  object
	 *
	 * @since   13.7
	 */
	public static function getTableObject($debug = null)
	{
		// Initialise variables.
		$config = JFactory::getConfig();
		$session = JFactory::getSession();
		$dbPrefix = $config->get('dbprefix');
		$db = JFactory::getDbo();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$debug = (boolean) $config->get('debug');
		}

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get a rowlist of all tables
		$db->setQuery('SHOW TABLES');
		$results = $db->loadRowList();

		$tableObject = new stdClass;

		foreach($results as $result) {
			$rowName = str_replace($dbPrefix, '', $result[0]);
			$quer = 'SHOW COLUMNS FROM #__' . $rowName;
			$db->setQuery($quer);
			$columns = $db->loadObjectList();

			foreach($columns as $column) {
				$helper = $column->Field;
				$tableObject->$rowName->$helper = $helper;
			}
		}

		return $tableObject;
	}
}