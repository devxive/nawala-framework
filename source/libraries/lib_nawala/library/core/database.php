<?php
/**
  * @info		$Id$ - $Revision$
  * @package		$Nawala.Framework $
  * @subpackage	NCoreDatabase
  * @check		$Date$ || $Result: devXive AntiMal...OK, nothing found $
  * @author		$Author$ @ devXive - research and development <support@devxive.com>
  * @copyright	Copyright (C) 1997 - 2013 devXive - research and development (http://www.devxive.com)
  * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  * @assetsLicense	devXive Proprietary Use License (http://www.devxive.com/license)
  */

// no direct access
defined('_NFWRA') or die;

/**
 * Utility class for all HTML drawing classes
 *
 * @package     Nawala.Framework
 * @subpackage  HTML
 * @since       13.0
 */
abstract class NCoreDatabase
{
	/**
	 * Method to get a table/columns object from the current table
	 *
	 * @return  object
	 *
	 * @since   13.7
	 */
	public function getTableObject($debug = null)
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