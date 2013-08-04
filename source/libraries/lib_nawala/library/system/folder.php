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

jimport('joomla.filesystem.folder');

abstract class NFWSystemFolder
{
	/*
	 * Stores the path value
	 */
	private static $path = JPATH_ROOT;

	/*
	 * Method to delete a folder
	 *
	 * @param     string    $delete    
	 *                                    copy($src, $dest, ), create, move, copy, delete ]
	 *                                     exist, create, move, copy, delete ]
	 *                                     exist, create, move, copy, delete ]
	 *                                     exist, create, move, copy, delete ]
	 *                                     exist, create, move, copy, delete ]
	 *                                     exist, create, move, copy, delete ]
	 *                                     exist, create, move, copy, delete ]
	 *                                     exist, create, move, copy, delete ]
	 *                                     exist, create, move, copy, delete ]
	 *
	 * @param     string    $path      Path to the folder to delete. The website root is set as base path (may include /var/www/website.com/httpdocs).
	 * @param     string    $config    the config to identicate if we want to overwrite an existing file or to append existing content
	 *
	 * @return
	 */
	public function delete($path = '/', $config = false) {
		// Check the path, after that use the self::$path to work with
		$checkPath = self::checkPath($path);
		if ( !$checkPath ) {
			JError::raiseWarning(500, 'Invalid path:<ul><li>' . self::$path . '</li></ul>');
			return false;
		}

		// Process config
		if ( $config ) {
		}

		if ( JFolder::delete(self::$path) ) {
			return true;
		} else {
			return false;
		}
	}

	/*
	 * Internal function to check the path
	 */
	private static function checkPath($path) {
		// Check if the first char is a slash
		$path = str_replace('..', '', $path);
		if ( ($path[0] == '/' && $path[1] == '/') || ($path[0] == '.' && $path[1] == '/') ) {
			self::$path .= $path;
			return false;
		}

		if ( $path == '' || $path[0] != '/' || ($path[0] == '/' && $path[1] == '') ) {
			self::$path .= '/' . $path;
			return true;
		} else {
			self::$path .= $path;
			return true;
		}
	}
}