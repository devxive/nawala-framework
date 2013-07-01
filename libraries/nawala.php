<?php
/**
  * @info		$Id$ - $Revision$
  * @package		$Nawala.Framework $
  * @subpackage	Framework
  * @check		$Date$ || $Result: devXive AntiMal...OK, nothing found $
  * @author		$Author$ @ devXive - research and development <support@devxive.com>
  * @copyright	Copyright (C) 1997 - 2013 devXive - research and development (http://www.devxive.com)
  * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  * @assetsLicense	devXive Proprietary Use License (http://www.devxive.com/license)
  */

// no direct access
defined('_NFWRA') or die;

// Set the Nawala Framework root path as a constant if necessary.
if (!defined('NPATH_FRAMEWORK'))
{
	define('NPATH_FRAMEWORK', JPATH_BASE . '/libraries/nawala');
}

// Set the Nawala Framework root path as a constant if necessary.
if (!defined('NPATH_MEDIA'))
{
	define('NPATH_MEDIA', JPATH_BASE . '/media/nawala');
}

// Define legacy directory separator as a constant if not exist
if(!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

// Init NLoader to work in nimport function if necessary.
if (!class_exists('NLoader'))
{
	require_once (NPATH_FRAMEWORK . '/core/loader.php');
}

// Init the framework version class if necessary.
if (!class_exists('NFramework'))
{
	require_once (NPATH_FRAMEWORK . '/core/version.php');
}

/**
 * @param  string $path the nawala path to the class to import
 *
 * @return void
 */
function nimport($key)
{
	return NLoader::_($key);
}

// Init the factory if necessary.
if (!class_exists('NFactory'))
{
	require_once (NPATH_FRAMEWORK . '/factory.php');
}