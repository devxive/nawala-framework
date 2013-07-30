<?php
/**
 * @project		XAP Project - Xive-Application-Platform
 * @subProject	Nawala Framework - A PHP and Javascript framework
 *
 * @package		NFW.library
 * @subPackage	Framework
 * @version		6.0
 *
 * @author		devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright		Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense	devXive Proprietary Use License (http://www.devxive.com/license)
 *
 * @since		5.0
 */

defined('_JEXEC') or die();
 
/**
 * Nawala plugin class.
 *
 * @package     XAP.plugin
 * @subpackage  System.nawala
 */
class plgSystemNawala extends JPlugin
{
	/**
	 * Method to register the library.
	 *
	 * return  void
	 */
	public function onAfterInitialise()
	{
		if (!defined('_NFW_FRAMEWORK')) {
			define('_NFW_FRAMEWORK', 1);
		}

		JLoader::registerPrefix('NFW', JPATH_LIBRARIES . '/nawala');
    }
}