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

jimport('joomla.filesystem.file');

abstract class NFWSystemFile
{
	/*
	 * Stores the path value
	 */
	private static $path = JPATH_ROOT;

	/**
	 * Write content to a local file
	 * @param  string $path      the path to the local file, if path = null, try to save file to current folder
	 * @param  string $filename  the filename
	 * @param  string $content   the content to write in file
	 * @param  string $config    the config to identicate if we want to overwrite an existing file or to append existing content
	 *
	 */
	public function write($content, $filename, $path = null, $config = null) {
//		if (!$path) {
//			$path = realpath(dirname(__FILE__);
//		}
		if (!$config) {
			JFile::write($path . $filename, $content);
		} else {
			if (JFile::exists($path . $filename)) {
				//TODO: OUTPUT JError
			} else {
				JFile::write($path . $filename, $content);
			}
	}
}