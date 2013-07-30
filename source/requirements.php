<?php
/**
 * @project		XAP Project - Xive-Application-Platform
 * @subProject	Nawala Framework - A PHP and Javascript framework
 *
 * @package		NFW.Installer
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

$errors = array();
if (version_compare(PHP_VERSION, '5.3.2', '<=')) {
    $errors[] = 'Needs a minimum PHP version of 5.3.2. You are running PHP version ' . PHP_VERSION;
}

$jversion = new JVersion();
if (!$jversion->isCompatible('3.1')) {
	$errors[] = '<i class="icon-warning"></i> XiveIRM will only run on XAP 13.6+, MOOTOMBO 3+ or Joomla! 3.1+ ';
}

if (!defined('NFWVERSION')) {
	$errors[] = '<i class="icon-warning"></i> The Nawala Framework have to be installed first!';
} else {
	$nfwversion = new NFWVersion();
	if (version_compare(nfwversion->getShortVersion(), '6.0.0', 'gt')) {
		$errors[] = '<i class="icon-warning"></i> Please update Nawala Framework to the latest version. You are running ' . nfwversion->getLongVersion();
	}
}

if (!function_exists('gd_info')) {
    $errors[] = '<i class="icon-warning"></i> The PHP GD2 module is needed but not installed.';
}

if (!phpversion('PDO')) {
    $errors[] = '<i class="icon-warning"></i> The PHP PDO module is needed but not installed.';
}

if (!phpversion('pdo_mysql')) {
    $errors[] = '<i class="icon-warning"></i> The PHP MySQL PDO driver is needed but not installed.';
}

if (!empty($errors)) {
	return $errors;
} else {
	return true;
}

return true;
