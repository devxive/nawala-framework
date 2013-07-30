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

abstract class NFWRegister
{
	public static function add()
	{
		$site_path  = JPATH_SITE . '/components/' . $component->element;
		$admin_path = JPATH_ADMINISTRATOR . '/components/' . $component->element;

		$com_name = str_replace('com_', '', $component->element);

		if (substr($com_name, 0, 2) == 'pf') {
			$com_name = 'PF' . substr($com_name, 2);
		}
		else {
			$com_name = ucfirst($com_name);
		}

		// Register backend helper class
		if (JFile::exists($admin_path . '/helpers/' . strtolower($com_name) . '.php')) {
			JLoader::register($com_name . 'Helper', $admin_path . '/helpers/' . strtolower($com_name) . '.php');
		}

		// Register the routing helper class
		if ($is_site) {
			if (JFile::exists($site_path . '/helpers/route.php')) {
				JLoader::register($com_name . 'HelperRoute', $site_path . '/helpers/route.php');
			}
		}

		if ($component->element != $current || $is_site) {
			// Register backend table classes
			if (JFolder::exists($admin_path . '/tables')) {
				JTable::addIncludePath($admin_path . '/tables');
			}

			// Register backend model classes
			if (JFolder::exists($admin_path . '/models')) {
				if ($is_site && JFolder::exists($site_path . '/models')) {
					// Give frontend models a priority over admin models
					JModelLegacy::addIncludePath($admin_path . '/models', $com_name . 'Model');
					JModelLegacy::addIncludePath($site_path . '/models', $com_name . 'Model');
				}
				else {
					JModelLegacy::addIncludePath($admin_path . '/models', $com_name . 'Model');
				}
			}

			// Register backend html classes
			if (JFolder::exists($admin_path . '/helpers/html')) {
				JHtml::addIncludePath($admin_path . '/helpers/html');
			}

			// Register backend forms
			if (JFolder::exists($admin_path . '/models/forms')) {
				JForm::addFormPath($admin_path . '/models/forms');
			}

			// Register backend form fields
			if (JFolder::exists($admin_path . '/models/fields')) {
				JForm::addFieldPath($admin_path . '/models/fields');
			}

			// Register backend form rules
			if (JFolder::exists($admin_path . '/models/rules')) {
				JForm::addRulePath($admin_path . '/models/rules');
			}
		}

		if ($component->element != $current && $is_site) {
			// Register frontend model classes
			if (JFolder::exists($site_path . '/models')) {
				JModelLegacy::addIncludePath($site_path . '/models', $com_name . 'Model');
			}

			// Register frontend html classes
			if (JFolder::exists($site_path . '/helpers/html')) {
				JHtml::addIncludePath($site_path . '/helpers/html');
			}
		}
	}
}