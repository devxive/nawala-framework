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

/**
 * Nawala HTML Datatables Class
 * Global Support for Datatable procedures
 *
 */
abstract class NFWGeoGmap3
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Add javascript Google Map support
	 *
	 * @param	string		$selector	Common id for the table
	 * @param	array		$params	Common params for the table
	 *
	 * @since   6.0
	 */
	public function initMap($selector = 'gmap-canvas', $params = null)
	{
		$sig = md5(serialize(array($selector, $params)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include dependencies
		self::dependencies();

//		JFactory::getDocument()->addScriptDeclaration(
//			"jQuery(document).ready(function() {
//			});\n"
//		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	 /*
	 * Load dependencies for this class
	 *
	 * @return  void
	 *
	 * @since   5.0
	 */
	public function dependencies($loadCss = false, $debug = null)
	{
		$sig = md5(serialize(array($loadCss)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS frameworks
		NFWHtml::loadJsFramework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug = (boolean) $config->get('debug');
		}

		JHtml::_('script', 'nawala/jquery.gmap3.min.js', false, true, false, false, $debug);
		JHTML::script('//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false');

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}
}