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
				if ($('." . $selector . "').is(':visible')) {
					setTimeout(function () {
						jQuery('." . $selector . "').slideUp('slow');
					}, " . $time . ")
				}
			});\n"
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
				$('." . $selector . "').hide();
				$('#" . $operator . "').click(function () {
					$('." . $selector . "').slideToggle('fast');
				});
			});\n"
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
			"function " . $jsFunction . "(" . $jsVar . ") {
				jQuery('.'+" . $jsVar . ").slideToggle('5000', 'easeInOutCubic', function() {
					// Animation Complete
				});
			}\n"
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
				$('#" . $selector . "').addClass('hide');
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript function to show a text limit on textareas
	 *
	 * @param	string		$selector	Common id for textarea
	 *
	 * @return  void
	 *
	 * @since   5.0
	 */
	public function setTextLimit($selector = 'limited', $chars = 100)
	{
		$sig = md5(serialize(array($selector, $chars)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('jquery.inputlimiter');

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery('textarea[class*=" . $selector . "]').each(function() {
				var limit = parseInt($(this).attr('data-maxlength')) || " . $chars . ";
				$(this).inputlimiter({
					'limit': limit,
					remText: '%n character%s remaining...',
					limitText: 'max allowed : %n.'
				});
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript function to auto height the textarea
	 *
	 * @param	string		$selector	Common class for textarea or all textareas
	 *						Example: textarea[class*=myTextClass] -- textarea with class myTextClass
	 *						Example: textarea -- all textarea's
	 *
	 * @return  void
	 *
	 * @since   5.0
	 */
	public function setTextAutosize($selector = 'textarea')
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
		self::dependencies('jquery.autosize');

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery(document).ready(function() {
				$('" . $selector . "').autosize();
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript support for Bootstrap tooltips
	 *
	 * Add a title attribute to any element in the form
	 * title="title::text"
	 *
	 * @param   string  $selector  The ID selector for the tooltip. Can also be .xTooltip, [data-rel=tooltip], #hasMyTip
	 * @param   array   $params    An array of options for the tooltip.
	 *                             Options for the tooltip can be:
	 *                             - animation  boolean          Apply a CSS fade transition to the tooltip
	 *                             - html       boolean          Insert HTML into the tooltip. If false, jQuery's text method will be used to insert
	 *                                                           content into the dom.
	 *                             - placement  string|function  How to position the tooltip - top | bottom | left | right
	 *                             - selector   string           If a selector is provided, tooltip objects will be delegated to the specified targets.
	 *                             - title      string|function  Default title value if `title` tag isn't present
	 *                             - trigger    string           How tooltip is triggered - hover | focus | manual
	 *                             - delay      number           Delay showing and hiding the tooltip (ms) - does not apply to manual trigger type
	 *                                                           If a number is supplied, delay is applied to both hide/show
	 *                                                           Object structure is: delay: { show: 500, hide: 100 }
	 *
	 * @return  void
	 *
	 * @since   13.2
	 */
	public function setTooltip($selector = '.xtooltip', $params = array())
	{
		$sig = md5(serialize(array($selector)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NHtml::loadJsFramework();

		// Setup options object
		$opt['animation'] = (isset($params['animation']) && ($params['animation'])) ? (boolean) $params['animation'] : null;
		$opt['html'] = (isset($params['html']) && ($params['html'])) ? (boolean) $params['html'] : null;
		$opt['placement'] = (isset($params['placement']) && ($params['placement'])) ? (string) $params['placement'] : null;
		$opt['selector'] = (isset($params['selector']) && ($params['selector'])) ? (string) $params['selector'] : null;
		$opt['title'] = (isset($params['title']) && ($params['title'])) ? (string) $params['title'] : null;
		$opt['trigger'] = (isset($params['trigger']) && ($params['trigger'])) ? (string) $params['trigger'] : null;
		$opt['delay'] = (isset($params['delay']) && ($params['delay'])) ? (int) $params['delay'] : null;

		$options = NHtml::getJSObject($opt);

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery(document).ready(function() {
				jQuery('" . $selector . "').tooltip(" . $options . ");
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript support for Bootstrap popovers
	 *
	 * Use element's Title as popover content
	 *
	 * @param   string  $selector  Selector for the tooltip
	 * @param   array   $params    An array of options for the tooltip.
	 *                  Options for the tooltip can be:
	 *                      animation  boolean          apply a css fade transition to the tooltip
	 *                      html       boolean          Insert HTML into the tooltip. If false, jQuery's text method will be used to insert
	 *                                                  content into the dom.
	 *                      placement  string|function  how to position the tooltip - top | bottom | left | right
	 *                      selector   string           If a selector is provided, tooltip objects will be delegated to the specified targets.
	 *                      title      string|function  default title value if `title` tag isn't present
	 *                      trigger    string           how tooltip is triggered - hover | focus | manual
	 *                      content    string|function  default content value if `data-content` attribute isn't present
	 *                      delay      number|object    delay showing and hiding the tooltip (ms) - does not apply to manual trigger type
	 *                                                  If a number is supplied, delay is applied to both hide/show
	 *                                                  Object structure is: delay: { show: 500, hide: 100 }
	 *
	 * @return  void
	 *
	 * @since   13.2
	 */
	public function setPopover($selector = '.xpopover', $params = array())
	{
		$sig = md5(serialize(array($selector)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NHtml::loadJsFramework();

		// Setup options object
		$opt['animation'] = isset($params['animation']) ? $params['animation'] : null;
		$opt['html'] = isset($params['html']) ? $params['html'] : null;
		$opt['placement'] = isset($params['placement']) ? $params['placement'] : null;
		$opt['selector'] = isset($params['selector']) ? $params['selector'] : null;
		$opt['title'] = isset($params['title']) ? $params['title'] : null;
		$opt['trigger'] = isset($params['trigger']) ? $params['trigger'] : 'hover';
		$opt['content'] = isset($params['content']) ? $params['content'] : null;
		$opt['delay'] = isset($params['delay']) ? $params['delay'] : null;

		$options = NHtml::getJSObject($opt);

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery(document).ready(function() {
				jQuery('" . $selector . "').popover(" . $options . ");
			});"
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

		if($type === 'jquery.inputlimiter')
		{
			JHtml::_('script', 'nawala/jquery.inputlimiter.min.js', false, true, false, false, $debug);
		}

		if($type === 'jquery.autosize')
		{
			JHtml::_('script', 'nawala/jquery.autosize.min.js', false, true, false, false, $debug);
		}

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}
}