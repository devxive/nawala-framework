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

// Define version
if ( !defined('NFWVERSION') ) {
	$nfwversion = new NFWVersion();
	define( 'NFWVERSION', $nfwversion->getShortVersion() );
}

// Set the Nawala Framework root path as a constant if necessary.
if (!defined('NFWPATH_FRAMEWORK'))
{
	define('NFWPATH_FRAMEWORK', JPATH_SITE . '/libraries/nawala');
}

// Set the Nawala Framework media root path as a constant if necessary.
if (!defined('NFWPATH_MEDIA'))
{
	define('NFWPATH_MEDIA', JPATH_SITE . '/media/nawala');
}

// Define legacy directory separator as a constant if not exist
if(!defined('DS')) {
	define('DS', '/');
}

// Init the factory if necessary.
if (!class_exists('NFactory'))
{
	require_once (NPATH_FRAMEWORK . '/factory.php');
}