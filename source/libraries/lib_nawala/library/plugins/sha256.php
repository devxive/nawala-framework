<?php
/**
 * @version     5.0.0
 * @package     NAWALA FRAMEWORK
 * @subPackage  NHtmlJavaScript
 * @copyright   Copyright (C) 1997 - 2013 by devXive - research and development. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      devXive <support@devxive.com> - http://devxive.com
 */

defined('_JEXEC') or die;

class NFWPluginsSha256
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Add javascript functions for Bootstrap alerts (system or own)
	 *
	 * @param	string		$selector	Common class for the alerts
	 * @param	int		$time		Time in milliseconds the alert box should remove
	 *
	 * @return  void
	 *
	 * @since   5.0
	 */
	public function loadSHA256($type = 'js.sha256', $selector = '.sha256', $debug = null)
	{
		$sig = md5(serialize(array($type, $selector)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug = (boolean) $config->get('debug');
		}

		// Include JS frameworks
		NFWHtml::loadJsFramework();

		// Include dependencies
		$initializer_msg = "alertify.error('Initializer not found!');";
		$initializer = '';

		if($type === 'jquery.sha256')
		{
//			JHtml::_('script', '/nawala/plugins/sha256/' . $type . '.js', false, true, false, false, $debug);
			JHtml::_('script', '/nawala/plugins/sha256/' . $type . '.min.js', false, true, false, false, $debug);
			$initializer_msg = "alertify.log('jquery.sha256 initiated');";
			$initializer = "var result = $.sha256(data);";
		}

		if($type === 'jquery.hash.sha256')
		{
			JHtml::_('script', '/nawala/plugins/sha256/' . $type . '.js', false, true, false, false, $debug);
			$initializer_msg = "alertify.log('jquery.hash.sha256 initiated');";
			$initializer = "var result = $.sha256(data);";
		}

		if($type === 'js.sha256')
		{
			JHtml::_('script', 'media/nawala/plugins/sha256/' . $type . '.js', false, false, false, false, $debug);
			$initializer_msg = "alertify.log('js.sha256 initiated');";
			$initializer = "var result = sha256_digest(data);";
		}

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery(document).ready(function() {
				" . $initializer_msg . "
				function sha256(data) {
					" . $initializer . "
					return result;
				};
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}
}