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

		// Include Bootstrap framework
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

		// Include Bootstrap framework
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
}