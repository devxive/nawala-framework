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
		self::dependencies('nawala.sitereadyoverlay');

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
	public function setTextLimit($selector = '.limited', $chars = 100)
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
			"jQuery(document).ready(function() {
				$('" . $selector . "').each(function() {
					var limit = parseInt($(this).attr('data-maxlength')) || " . $chars . ";
					$(this).inputlimiter({
						'limit': limit,
						remText: '%n character%s remaining...',
						limitText: 'max allowed : %n.'
					});
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

	/**
	 * Add javascript support for bootstrap loading buttons
	 *
	 * @param	string		$selector	Common id for the button
	 * @param	string		$time		time in milliseconds
	 *
	 * @return  void
	 *
	 * @since   5.0
	 */
	public function setLoadingButton($selector = '#loading-btn', $time = 5000)
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
			"jQuery('" . $selector . "').on('click', function () {
				$('" . $selector . "').addClass('btn-warning');
				var btn = $(this);
				btn.button('loading')
				setTimeout(function () {
					$('" . $selector . "').removeClass('btn-warning'),
					btn.button('reset')
				}, " . $time . ")
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript support to prevent enter keypress in forms or form elements
	 * 	Prevent submit on enter (keycode 13) event in input fields. To use all form fields, use the form id (ie: #form-contact)
	 *
	 * @param	string		$selector	Common id for the form or the formfields
	 *
	 * @return  void
	 *
	 * @since   5.0
	 */
	public function setPreventFormSubmitByKey($selector = 'input[type]', $key = 13)
	{
		$sig = md5(serialize(array($selector, $key)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS frameworks
		NHtml::loadJsFramework();

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery(document).ready(function() {
				jQuery('" . $selector . "').bind('keypress keydown keyup', function(e) {
					if(e.keyCode == " . $key . ") { e.preventDefault(); }
				});
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript support to prevent to leave the site, if anything in the form has changed and is not saved at present!
	 *
	 * @param	string		$selector	Common id for the form or the formfields. Standard: "form" as the form element
	 *
	 * @return  void
	 *
	 * @since   10.9
	 */
	public function setPreventFormLeaveIfChanged($selector = 'form')
	{
		$sig = md5(serialize(array($selector)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS frameworks
		NHtml::loadJsFramework();

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"var catcher = function() {
				var changed = false;

				$('" . $selector . "').each(function() {
					if ($(this).data('initialForm') != $(this).serialize()) {
						changed = true;
						$(this).addClass('changed');
					} else {
						$(this).removeClass('changed');
					}
				});

				if (changed) {
					return '" . JText::_('COM_XIVEIRM_PREVENT_LEAVE_SITE') . "';
				}
			};

			jQuery(function() {
				$('" . $selector . "').each(function() {
					$(this).data('initialForm', $(this).serialize());
				}).submit(function(e) {
					var formEl = this;
					var changed = false;

					$('" . $selector . "').each(function() {
						if (this != formEl && $(this).data('initialForm') != $(this).serialize()) {
							changed = true;
							$(this).addClass('changed');
						} else {
							$(this).removeClass('changed');
						}
					});

					// If we have 2 or more forms on this page - Be careful with hidden forms!!
					if (changed && !confirm('" . JText::_('COM_XIVEIRM_PREVENT_FORM_SUBMISSION') . "')) {
						e.preventDefault();
					} else {
						$(window).unbind('beforeunload', catcher);
					}
				});

				$(window).bind('beforeunload', catcher);
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript support for growl like notification messages via Gritter
	 *
	 * @param	string		$selector	
	 *
	 * @return  void
	 *
	 * @since   8.0
	 */
	public function loadGritter($selector = 'gritter')
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
		self::dependencies('nawala.gritter');

		// Attach the function to the document
//		JFactory::getDocument()->addScriptDeclaration(
//			"jQuery(document).ready(function() {
//				$('" . $selector . "').autosize();
//			});\n"
//		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript support for checked out by another user messages
	 * Shows a predesigned messagebox with informations about the checkout process and the user who checked out the item
	 *
	 * @param   int     $checkoutUserId  The ID of the user who checked out the item as set in table, row checked_out.
	 * @param   int     $checkoutTime    The ID of the user who checked out the item as set in table, row checked_out.
	 * @param   string  $selector        The ID selector for the tooltip. Can also be .xTooltip, [data-rel=tooltip], #hasMyTip
	 * @param   array   $params          An array of options for the tooltip.
	 *                                   Options for the tooltip can be:
	 *                                   - animation  boolean          A jQuery function. Standard: "slideDown"
	 *                       - checkoutByOtherTitle   string           Title if the item is checked out by another user for JText::_('')
	 *                     - checkoutByOtherMessage   string           Message value for JText::sprintf('',)
	 *                                                                      1. Value is the name of the user.
	 *                                                                      2. Time as set in checked_out_time row of appropriate table.
	 *                                                                      3. [Optional] Time in minutes if the item might be checked in.
	 *                       - checkoutByUserTitle    string           Title if the item is checked out by user itself for JText::_('')
	 *                     - checkoutByUserMessage    string           Message value for JText::sprintf('',)
	 *                                                                      1. Time as set in checked_out_time row of appropriate table.
	 *                            - checkinMessage    string           Message to display if a time is set when the item is checked in again
	 *                               - checkinTime    string           Provide a time in minutes when the item is chekced in again (may with an external script or something else)
	 *                                   - duration   string           Duration of the animation - slow | fast
	 *                                   - delay      number           Delay after the checkout is message showing (ms)
	 *
	 * @return  false                                                  If nothing is checked out
	 * ...@return  mixed, array                                        Load scriptDeclaration for showing the message container
	 *                                  - $results['by']               Checked out by - other | user | false
	 *                                  - $results['message']          The html message to set wherever you want in the doc
	 *
	 * @since   13.4
	 */
	public function getCheckoutMessage($checkoutUserId, $checkoutTime, $selector = '#checkout-message', $params = array())
	{
		// Check if it is checked out
		if($checkoutUserId == 0)
		{
			return false;
		}

		$sig = md5(serialize(array($selector)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NHtml::loadJsFramework();

		// Get id from the user object
		$currentUserId = JFactory::getUser()->id;
		$checkedOutTime = date(JText::_('DATE_FORMAT_LC2'), strtotime($checkoutTime));
		$checkinIn = '10';

		// Setup options object
		$opt['checkoutByOtherTitle']	= (isset($params['checkoutByOtherTitle']) && ($params['checkoutByOtherTitle'])) ? (string) $params['checkoutByOtherTitle'] : 'This item has been checked out by another user!';
		$opt['checkoutByOtherMessage']	= (isset($params['checkoutByOtherMessage']) && ($params['checkoutByOtherMessage'])) ? (string) $params['checkoutByOtherMessage'] : 'This Item has been checked out by %s at %s. You can\'t edit this until this is checked in.';
		$opt['checkoutByUserTitle']		= (isset($params['checkoutByUserTitle']) && ($params['checkoutByUserTitle'])) ? (string) $params['checkoutByUserTitle'] : 'This Item has been checked out by yourself!';
		$opt['checkoutByUserMessage']	= (isset($params['checkoutByUserMessage']) && ($params['checkoutByUserMessage'])) ? (string) $params['checkoutByUserMessage'] : 'You have checked out this item at %s';
		$opt['checkinMessage']		= (isset($params['checkinMessage']) && ($params['checkinMessage'])) ? (string) $params['checkinMessage'] : 'A checkin is attempted within the next ~%s minutes';
		$opt['checkinTime']	= (isset($params['checkinTime']) && ($params['checkinTime'])) ? (string) $params['checkinTime'] : null;
		$opt['userlink']	= (isset($params['userlink']) && ($params['userlink'])) ? (string) $params['userlink'] : null;
		$opt['animation']	= (isset($params['animation']) && ($params['animation'])) ? (int) $params['animation'] : 'slideDown';
		$opt['duration']	= (isset($params['duration']) && ($params['duration'])) ? (int) $params['duration'] : 'slow';
		$opt['delay']		= (isset($params['delay']) && ($params['delay'])) ? (int) $params['delay'] : 2000;

//		$options = NHtml::getJSObject($opt);

		// Build the user who has checked out the item
		if($opt['userlink']) {
			$checkedOutUser = '<a href="" target="_blank">' . JFactory::getUser($checkoutUserId)->name . '</a>';
		} else {
			$checkedOutUser = JFactory::getUser($checkoutUserId)->name;
		}

		// Based on the above check, we're in active check out. Now identify who has checked out and return html
		$html_out = '';
		$results = array();

		$cssSelector = preg_replace('#[^A-Z0-9-_]#i', '', $selector);

		if($checkoutUserId != $currentUserId)
		{
			$results['by'] = 'other';

			$html_out .= '<div id="' . $cssSelector . '" class="alert alert-error" style="display: none;">';
			$html_out .= '<button type="button" class="close" data-dismiss="alert">';
			$html_out .= '<i class="icon-remove"></i>';
			$html_out .= '</button>';
			$html_out .= '<h1><i class="icon-signout"></i> ' . JText::_($opt['checkoutByOtherTitle']) . '</h1>';
			$html_out .= '<p>' . JText::sprintf($opt['checkoutByOtherMessage'], $checkedOutUser, $checkedOutTime) . '</p>';
			if($opt['checkinMessage']) {
				$html_out .= '<p>' . JText::sprintf($opt['checkinMessage'], $opt['checkinTime']) . '</p>';
			}
			$html_out .= '</div>';
		}
		else
		{
			$results['by'] = 'user';

			$html_out .= '<div id="' . $cssSelector . '" class="alert alert-notice" style="display: none;">';
			$html_out .= '<button type="button" class="close" data-dismiss="alert">';
			$html_out .= '<i class="icon-remove"></i>';
			$html_out .= '</button>';
			$html_out .= '<h1><i class="icon-signout"></i> ' . JText::_($opt['checkoutByUserTitle']) . '</h1>';
			$html_out .= '<p>' . JText::sprintf($opt['checkoutByUserMessage'], $checkedOutTime) . '</p>';
			$html_out .= '</div>';
		}

		$results['message'] = $html_out;

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery(window).load(function() {
				if ($('" . $selector . "').is(':hidden')) {
					setTimeout(function () {
						jQuery('" . $selector . "')." . $opt['animation'] . "('" . $opt['duration'] . "');
					}, " . $opt['delay'] . ");
				}
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return $results;
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

		if($type === 'nawala.sitereadyoverlay')
		{
			JHtml::_('stylesheet', 'nawala/nawala.sitereadyoverlay.css', false, true);
		}

		if($type === 'jquery.inputlimiter')
		{
			JHtml::_('script', 'nawala/jquery.inputlimiter.min.js', false, true, false, false, $debug);
		}

		if($type === 'jquery.autosize')
		{
			JHtml::_('script', 'nawala/jquery.autosize.min.js', false, true, false, false, $debug);
		}

		if($type === 'nawala.gritter')
		{
			JHtml::_('stylesheet', 'nawala/nawala.gritter.bootstrap.css', false, true);
			JHtml::_('script', 'nawala/nawala.gritter.bootstrap.js', false, true, false, false, $debug);
		}

		if($type === 'jquery.gritter')
		{
			JHtml::_('stylesheet', 'nawala/jquery.gritter.css', false, true);
			JHtml::_('script', 'nawala/jquery.gritter.min.js', false, true, false, false, $debug);
		}

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

// TODO:	function bindWindowAlertMessages()
//	{
//		// if (_alert) return;
//		// var _alert = window.alert;
//		jQuery(window).load(function() {
//			alert('Hilfe');
//		});
//		
//		jQuery(function(){
//			window.alert = function(message) {
//				$.gritter.add({
//					title: 'Alert',
//					text: message,
//					icon: 'icon-warning-sign',
//					class_name: 'alert-error'
//				});
//			};
//			window.confirm = function(message) {
//				$.gritter.add({
//					title: 'Alert',
//					text: message,
//					icon: 'icon-warning-sign',
//					class_name: 'alert-error'
//				});
//			};
//			window.prompt = function(message) {
//				$.gritter.add({
//					title: 'Alert',
//					text: message,
//					icon: 'icon-warning-sign',
//					class_name: 'alert-error'
//				});
//			};
//		});
//	}
}