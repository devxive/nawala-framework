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
	 * Constructor.
	 *
	 * @access protected
	 * @param object $subject The object to observe
	 * @param array   $config  An array that holds the plugin configuration
	 * @since 1.0
	 */
	public function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );

		if (!defined('_NFW_FRAMEWORK')) {
			define('_NFW_FRAMEWORK', 1);
		}

		// Register the library.
		JLoader::registerPrefix('NFW', JPATH_LIBRARIES . '/nawala');

		// Do some extra initialisation in this constructor if required
	}

	/**
	 * return  void
	 */
	public function onAfterInitialise()
	{
	}
}