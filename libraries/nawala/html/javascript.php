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

abstract class NHtmlJavaScript
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
	public function setAutoRemove($selector = 'alert', $time = 3000)
	{
		$sig = md5(serialize(array($selector, $time)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS frameworks
		NHtml::loadJsFramework();

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery(function($){
				if ($('.$selector').is(':visible')) {
					setTimeout(function () {
						jQuery('.$selector').slideUp('slow');
					}, $time)
				}
			});"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript toggler to extend the view with hidden divs
	 *
	 * @param	string		$selector	Common id for the alerts
	 * @param	int		$time		Time in milliseconds the alert box should remove
	 *
	 * @return  void
	 *
	 * @since   5.0
	 */
	public function setToggle($selector = 'extended', $operator = 'toggle')
	{
		$sig = md5(serialize(array($selector, $operator)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS frameworks
		NHtml::loadJsFramework();

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery(function($)
			{
				$('.$selector').hide();
				$('#$operator').click(function () {
					$('.$selector').slideToggle('fast');
				});
			});"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript toggle function to extend the view with hidden divs in tables or other foreach elements
	 *
	 * @param	string		$jsFunction	Name of the function.
	 * @param	string		$jsVar		Id used in the function to identify the objects.
	 *						Note: the jsVar in the js-function should be the same as the id ot the toggle element
	 *						PHP Example:
	 *						foreach($items as $item) {
	 *							echo '<a onClick="toggleFunction("toggle_' . $item->id . '")">';
	 *							echo '<div id="toggle_23" style="display:none;">Hello World</div>';
	 * 						}
	 *
	 * @return  void
	 *
	 * @since   5.0
	 */
	public function setToggleFunction($jsFunction = 'toggleFunction', $jsVar = 'toggle')
	{
		$sig = md5(serialize(array($jsFunction, $jsVar)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('ui.effects');

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"function $jsFunction($jsVar) {
				jQuery('.'+$jsVar).slideToggle('5000', 'easeInOutCubic', function() {
					// Animation Complete
				});
			}"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript SiteReadyOverlay to prevent clicking until site is full reloaded
	 *
	 * @param	string		$selector	Common id for overlay
	 *
	 * @return  void
	 *
	 * @since   5.0
	 */
	public function setSiteReadyOverlay($selector = 'siteready-overlay')
	{
		$sig = md5(serialize(array($selector)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('devxive.sitereadyoverlay');

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery( window ).load(function() {
				$('#$selector').addClass('hide');
			});\n"
		);

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
	public function dependencies($type, $debug = null)
	{
		$sig = md5(serialize(array($type)));

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

		if($type === 'ui.effects')
		{
			JHtml::_('script', 'nawala/jquery.ui.effects.js', false, true, false, false, $debug);
		}

		if($type === 'devxive.sitereadyoverlay')
		{
			JHtml::_('stylesheet', 'nawala/devxive.sitereadyoverlay.css', false, true);
		}

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}
}