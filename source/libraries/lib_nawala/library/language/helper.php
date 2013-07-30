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

abstract class NFWLanguageHelper
{
	public static function setLanguage()
	{
		// Get the the currently active component
		$current    = JFactory::getApplication()->input->get('option');
		$lang       = JFactory::getLanguage();
		$is_site    = JFactory::getApplication()->isSite();

		// Set the paths
		$site_path  = JPATH_SITE . '/components/' . $component->element;
		$admin_path = JPATH_ADMINISTRATOR . '/components/' . $component->element;

		// Begin loading language files
		if ($component->element != $current) {
			$lang->load($component->element);
		}

		if ($is_site) {
			// Also load the backend language when in frontend
			$lang->load($component->element, JPATH_ADMINISTRATOR);

			// Load the language from the component frontend directory if it exists
			if (JFolder::exists($site_path . '/language')) {
				$lang->load($component->element, $site_path);
			}
		}

		// Load the language from the component backend directory if it exists
		if (JFolder::exists($admin_path . '/language')) {
			$lang->load($component->element, $admin_path);
		}
	}
}