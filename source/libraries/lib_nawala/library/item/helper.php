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
 * Nawala Item Helper Class
 * Support for helper procedures
 *
 */
abstract class NFWItemHelper
{
	public function __construct()
	{
	}

	/*
	 * Global Checkin Method to check in an item for the current user
	 * return true if success, else return false
	 * $table without prefix
	 */
	public function checkIn($table, $id)
	{
		if(!$table && !$id && (int) $id) {
			return false;
		}

		$user = 0;
		$datetime = '0000-00-00 00:00:00';

		// Init database object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Set the fields
		$fields = array(
			'checked_out = ' . $db->quote($user) . '',
			'checked_out_time = ' . $db->quote($datetime) . '');

		$dbTable = '#__' . $table;

		$query
			->update($db->quoteName($dbTable))
			->set($fields)
			->where('id = ' . $db->quote($id) . '');

		$db->setQuery($query);

		// Try to store or get the error code for debugging
		try
		{
			$db->execute();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	/*
	 * Global Checkout Method to check out an item for the current user based on users timezone
	 * return true if success, else return false
	 * $table without prefix, id must integer, datetime in sql format, user who checked out (int of the user db id)
	 */
	public function checkOut($table, $id, $datetime, $user)
	{
		if($user != 0 && (int) $user && $datetime && $user)
		{
			// Init database object.
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			// Set the fields
			$fields = array(
				'checked_out = ' . $db->quote($user) . '',
				'checked_out_time = ' . $db->quote($datetime) . '');

			$dbTable = '#__' . $table;

			$query
				->update($db->quoteName($dbTable))
				->set($fields)
				->where('id = ' . $db->quote($id) . '');

			$db->setQuery($query);

			// Try to store or get the error code for debugging
			try
			{
				$db->execute();
				return true;
			} catch (Exception $e) {
				return false;
			}
		} else {
			return false;
		}
	}


	/*
	 * return the current date, based on the timezone, given either in the user or the system config object.
	 * @format	switch the format, sql datetime format, unix timestamp, date, datetime
	 * @value	default now, other formats not supportet at this time
	 * @mode	switch the mode (default: USER_UTC): SERVER_UTC, USER_UTC (USER_UTC with fallback to system, if the timezone is set to Global)
	 *
	 * @DEPRECATED IN 6.0	use NFWDate::getCurrent instead
	 */
	public function getDate($format = 'UNIX', $value = 'now', $mode = 'USER_UTC')
	{
		// Get some system objects.
		$config = JFactory::getConfig();
		$user = JFactory::getUser();

		$date = JFactory::getDate($value, 'UTC');

		// Set the timezone
		switch ($mode)
		{
			case 'SERVER_UTC':
				// Convert a date to UTC based on the server timezone.
				$date->setTimezone(new DateTimeZone($config->get('offset')));
				break;

			case 'USER_UTC':
				// Convert a date to UTC based on the user timezone (Fallback, system config timezome, if user tz is set to global).
				$date->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));
				break;
		}

		// Transform the date string
		switch ($format)
		{
			case 'MySQL':
				$value = $date->format('Y-m-d H:i:s', true, false);
				break;

			case 'UNIX':
				$value = strtotime($date->format('Y-m-d H:i:s', true, false));
				break;

			case 'TIME':
				$value = $date->format('H:i', true, false);
				break;

			case 'TIMES':
				$value = $date->format('H:i:s', true, false);
				break;

			case 'LC':
			case 'LC1':
			case 'JLC':
			case 'JLC1': // Wednesday, 12 June 2013 
				$value = $date->format('l, d F Y', true, false);
				break;

			case 'LC2':
			case 'JLC2': // Wednesday, 12 June 2013 15:20
				$value = $date->format('l, d F Y H:i', true, false);
				break;

			case 'LC3':
			case 'JLC3':
				$value = $date->format('d F Y', true, false); // 12 June 2013
				break;

			case 'DATE':
			case 'LC4':
			case 'JLC4':
				$value = $date->format('Y-m-d', true, false); // 2013-06-12
				break;
		}

		return $value;
	}

	/*
	 * Global Method to get a title by an id (Currently only usergroups and viewlevels)
	 * return the title if success else return $id
	 */
	public function getTitleById($type, $id, $table = false, $row = false)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		if ($type == 'usergroup') {
			$query
				->select('title')
				->from('#__usergroups')
				->where('id = ' . $db->quote($id) . '');
		} else if ($type == 'viewlevel') {
			$query
				->select('title')
				->from('#__viewlevels')
				->where('id = ' . $db->quote($id) . '');
		} else if ($type == 'category') {
			$query
				->select('title')
				->from('#__categories')
				->where('id = ' . $db->quote($id) . '');
		} else if ($type == 'custom' && $table && $row) {
			$query
				->select($row)
				->from('#__' . $table . '')
				->where('id = ' . $db->quote($id) . '');
		} else {
			return $id;
		}

		$db->setQuery($query);

		if ($result = $db->loadResult()) {
			return $result;
		} else {
			return $id;
		}
	}

	/*
	 * Global Method to get name fields by an id (such as last_name, first_name, company name, etc...)
	 * return the rows if success else return false
	 */
	public function getNameById($id = false, $table = false, $row = false, $breakLongName = false)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		if ($id && $table && $row != true && $row != false) {
			$query
				->select($row)
				->from('#__' . $table . '')
				->where('id = ' . $db->quote($id) . '');
		} else if ($id && $table && $row == true) {
			$query
				->select(array('last_name', 'first_name', 'company'))
				->from('#__' . $table . '')
				->where('id = ' . $db->quote($id) . '');
		} else {
			return false;
		}

		$db->setQuery($query);

		if($row == true) {
			$result = $db->loadObjectList();

			$name = '';
			if($result[0]->last_name && $result[0]->first_name && $result[0]->company) {
				if($breakLongName) {
					$name .= $result[0]->company . '<br><small>' . $result[0]->last_name . ', ' . $result[0]->first_name . '</small>';
				} else {
					$name .= $result[0]->company . ' <small>' . $result[0]->last_name . ', ' . $result[0]->first_name . '</small>';
				}
			} else if(!$result[0]->last_name && !$result[0]->first_name && $result[0]->company) {
				$name .= $result[0]->company;
			} else if($result[0]->last_name && $result[0]->first_name && !$result[0]->company) {
				$name .= $result[0]->last_name . ', ' . $result[0]->first_name;
			} else if($result[0]->last_name && !$result[0]->first_name && !$result[0]->company) {
				$name .= $result[0]->last_name;
			} else if(!$result[0]->last_name && $result[0]->first_name && !$result[0]->company) {
				$name .= $result[0]->first_name;
			} else if($result[0]->last_name && !$result[0]->first_name && $result[0]->company) {
				if($breakLongName) {
					$name .= $result[0]->company . '<br><small>' . $result[0]->last_name . '</small>';
				} else {
					$name .= $result[0]->company . ' <small>' . $result[0]->last_name . '</small>';
				}
			} else if(!$result[0]->last_name && $result[0]->first_name && $result[0]->company) {
				if($breakLongName) {
					$name .= $result[0]->company . '<br><small>' . $result[0]->first_name . '</small>';
				} else {
					$name .= $result[0]->company . ' <small>' . $result[0]->first_name . '</small>';
				}
			} else {
				$name .= NFW_UNKNOWN_PERSON_OR_COMPANY;
			}

			return $name;
		} else {
			$result = $db->loadResult();

			return $result;
		}
	}
}