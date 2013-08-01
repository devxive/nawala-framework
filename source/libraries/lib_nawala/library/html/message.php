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

abstract class NFWHtmlMessage
{
	/**
	 * Method to set a system message
	 *
	 * @param     int    $code       The error code
	 * @param     int    $message    The error message
	 *
	 * @return    bool               False on error
	 */
	public static function set($type = '', $code = '', $message = '')
	{
		// Transform the date string
		switch ($type)
		{
			case 'Message':
				JFactory::getApplication()->enqueueMessage($message);
				break;

			case 'Notice':
				JError::raiseNotice( $code, $message );
				break;

			case 'Warning':
				JError::raiseWarning( $code, $message );
				break;

			case 'Error':
				JError::raiseError( $code, $message );
				break;

			default:
				return json_encode(array('code' => $code, 'message' => $message));
				break;
		}

		return false;
	}
}