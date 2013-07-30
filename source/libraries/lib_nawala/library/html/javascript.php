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
 * Nawala HTML Javascript Class
 * Global Support for Javascript procedures
 *
 */
abstract class NFWHtmlJavaScript
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
		NFWHtml::loadJsFramework();

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
		NFWHtml::loadJsFramework();

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery(function($)
			{
				$('." . $selector . "').hide();
				$('#" . $operator . "').click(function () {
					$('.inverse-" . $selector . "').slideToggle('fast');
					$('.inverse-" . $selector . "').hide();
					$('." . $selector . "').slideToggle('fast');
					if ($('#" . $operator . "').hasClass('active')) {
						$('#" . $operator . "').removeClass('active');
						$('.inverse-" . $selector . "').slideToggle('fast');
					} else {
						$('#" . $operator . "').addClass('active');
					}
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
		NFWHtml::loadJsFramework();

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
		NFWHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('nawala.sitereadyoverlay');

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery( window ).load(function() {
				$('#" . $selector . "').hide();
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
		NFWHtml::loadJsFramework();

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
		NFWHtml::loadJsFramework();

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
		NFWHtml::loadJsFramework();

		// Setup options object
		$opt['animation'] = (isset($params['animation']) && ($params['animation'])) ? (boolean) $params['animation'] : null;
		$opt['html'] = (isset($params['html']) && ($params['html'])) ? (boolean) $params['html'] : null;
		$opt['placement'] = (isset($params['placement']) && ($params['placement'])) ? (string) $params['placement'] : null;
		$opt['selector'] = (isset($params['selector']) && ($params['selector'])) ? (string) $params['selector'] : null;
		$opt['title'] = (isset($params['title']) && ($params['title'])) ? (string) $params['title'] : null;
		$opt['trigger'] = (isset($params['trigger']) && ($params['trigger'])) ? (string) $params['trigger'] : null;
		$opt['delay'] = (isset($params['delay']) && ($params['delay'])) ? (int) $params['delay'] : null;

		$options = NFWHtml::getJSObject($opt);

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
		NFWHtml::loadJsFramework();

		// Setup options object
		$opt['animation'] = isset($params['animation']) ? $params['animation'] : null;
		$opt['html'] = isset($params['html']) ? $params['html'] : null;
		$opt['placement'] = isset($params['placement']) ? $params['placement'] : null;
		$opt['selector'] = isset($params['selector']) ? $params['selector'] : null;
		$opt['title'] = isset($params['title']) ? $params['title'] : null;
		$opt['trigger'] = isset($params['trigger']) ? $params['trigger'] : 'hover';
		$opt['content'] = isset($params['content']) ? $params['content'] : null;
		$opt['delay'] = isset($params['delay']) ? $params['delay'] : null;

		$options = NFWHtml::getJSObject($opt);

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
		NFWHtml::loadJsFramework();

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
		NFWHtml::loadJsFramework();

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
		NFWHtml::loadJsFramework();

		// Include dependencies
		self::loadAlertify();

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
		NFWHtml::loadJsFramework();

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
	 * Add javascript support for growl like notification messages and dialog, alert and prompt via alertify
	 *
	 * @param	string		$selector	
	 *
	 * @return  void
	 *
	 * @since   13.7
	 */
	public function loadAlertify($selector = 'alertify')
	{
		$sig = md5(serialize(array($selector)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NFWHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('alertify');

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
	 * Add javascript support for bootbox. Bootstraped like browser notification messages and dialog, alert and prompt
	 *
	 * @param	string		$selector	
	 *
	 * @return  void
	 *
	 * @since   13.7
	 */
	public function loadBootbox($selector = 'bootbox')
	{
		$sig = md5(serialize(array($selector)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NFWHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('bootstrap.bootbox');

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
	 * Add javascript support for jquery.easy-pie-chart
	 *
	 * @param	string		$selector	
	 *
	 * @return  void
	 *
	 * @since   13.7
	 */
	public function loadEasyPie($selector = '.ep-chart', $setPercent = true, $fadeInOnTrigger = true)
	{
		$sig = md5(serialize(array($selector)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NFWHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('jquery.easy-pie-chart');

		if($setPercent) {
			$percentSpan = "$('" . $selector . "').html('<span class=\"percent\">' + $(this).data('percent') + '%</span>');";
		} else {
			$percentSpan = "";
		}

		if($fadeInOnTrigger) {
			$setFadeIn = "$('" . $selector . "').fadeIn('slow').each(function() {";
		} else {
			$setFadeIn = "$('" . $selector . "').each(function() {";
		}

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery(document).ready(function() {
				$('" . $selector . "').hide();
				setTimeout(function() {
					" . $setFadeIn . "
						" . $percentSpan . "

						$(this).easyPieChart({
							barColor: $(this).data('color') ? $(this).data('color') : '#87B87F',
							trackColor: '#EEEEEE',
							scaleColor: false,
							lineCap: 'butt',
							lineWidth: $(this).data('line-width') ? $(this).data('line-width') : 8,
							animate: $(this).data('animate') ? $(this).data('animate') : 1000,
							size: $(this).data('size') ? $(this).data('size') : 75
						}).css('color', $(this).data('color'));
					});
				}, 10);
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript support for jquery.auto-geocoder to automatically geocode and display a location entered in a text field
	 *
	 * @param	string		$selector	ID of the div element (where to show the map)
	 *
	 * @return  void
	 *
	 * @since   13.7
	 */
	public function loadAutoGeocoder($selector = '#location', $defaultGeocoder = true, $dir = '')
	{
		$sig = md5(serialize(array($selector)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NFWHtml::loadJsFramework();

		// Include dependencies
		if($defaultGeocoder) {
			self::dependencies('jquery.auto-geocoder');
		} else {
			self::dependencies('custom.auto-geocoder', $dir);
		}

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration(
			"jQuery(document).ready(function() {
				$('" . $selector . "').autoGeocoder();
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript support for chosen select lists
	 *
	 * @param   string  $selector  Selector for the tooltip
	 * @param   string  $trigger   ID of the form field for updating chosen dynamically (without the id selector: #). If you dont want it, set trigger to false.
	 *                             Desc: If you need to update the options in your select field and want Chosen to pick up the changes, you'll need
	 *                                   to trigger the "liszt:updated" event on the field. Chosen will re-build itself based on the updated content.
	 * @param   array   $params    An array of options for the tooltip.
	 *
	 *                  Options for the tooltip can be:
	 *                      disable_search_threshold  int              Option to specify to hide the search input on single selects if there are fewer than (n) options.
	 *                      disable_search            boolean          Option to disable the search. true | false (standard)
	 *                      no_results_text           string           Setting the "No results" search text - Example: Oops, nothing found! or JText::_('PLACEHOLDER')
	 *                      max_selected_options      int|function     Limit how many options can user select
	 *                      allow_single_deselect     boolean          When a single select box isn't a required field, you can set allow_single_deselect: true
	 *                                                                 Chosen will add a UI element for option deselection. This will only work if the first option has blank text.
	 *                      width                     string           Using a custom width with chosen. Example: '95%'
	 *
	 *                                          Note: On single selects, the first element is assumed to be selected by the browser.
	 *                                                To take advantage of the default text support, you will need to include a blank option as the first element of your select list.
	 *
	 *                                                Example:
	 *                                                        <?php NFWHtmlJavascript::setChosen('.chzn-select', 'form_gender_field', array('allow_single_deselect' => true, 'width' => '95%')); ?>
	 *                                                        <select id="form_gender_field" class="chzn-select" name="form_gender" data-placeholder="Choose a gender...">
	 *                                                            <option value></option>
	 *                                                            <option value="female">Female</option>
	 *                                                            <option value="male">Male</option>
	 *                                                        </select>
	 *
	 * @return  void
	 *
	 * @see http://harvesthq.github.io/chosen/ for more informations and options
	 *
	 * @since   13.7
	 */
	public function setChosen($selector = '.chosen-select', $trigger = false, $params = array())
	{
		$sig = md5(serialize(array($selector, $trigger)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NFWHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('jquery.chosen');

		// Setup options object
		$opt['disable_search_threshold'] = isset($params['disable_search_threshold']) ? $params['disable_search_threshold'] : null;
		$opt['disable_search'] = isset($params['disable_search']) ? $params['disable_search'] : null;
		$opt['no_results_text'] = isset($params['no_results_text']) ? $params['no_results_text'] : null;
		$opt['max_selected_options'] = isset($params['max_selected_options']) ? $params['max_selected_options'] : null;
		$opt['allow_single_deselect'] = isset($params['allow_single_deselect']) ? $params['allow_single_deselect'] : null;
		$opt['width'] = isset($params['width']) ? $params['width'] : null;

		$options = NFWHtml::getJSObject($opt);

		// Build the scriptDeclaration
		if(!$trigger) {
			$srciptDec = 
				"jQuery(document).ready(function() {
					$('" . $selector . "').chosen(" . $options . ");
				});\n";
		} else {
			$srciptDec = 
				"jQuery(document).ready(function() {
					$('" . $selector . "').chosen(" . $options . ");
					$('#" . $trigger . "').trigger('liszt:updated');
				});\n";
		}

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration($srciptDec);

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
		NFWHtml::loadJsFramework();

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

//		$options = NFWHtml::getJSObject($opt);

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

			$html_out .= '<div id="' . $cssSelector . '" class="alert alert-error fade in" style="display: none;">';
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

	/**
	 * Add javascript support for moment, a js date library for parsing, validating, manipulating and formatting dates
	 *
	 * @param	string		$selector	
	 *
	 * @return  void
	 *
	 * @use		data-timestamp="2013-07-22 16:45:00
	 *			data-time=""
	 *			data-calendar=""
	 *
	 *
	 *					<?php $testDate = '07/15/2013 10:38'; ?>
	 *					<abbr class="xtooltip ntime-fromnow" data-calendar="<?php echo $testDate; ?>" title="<?php echo date(JText::_('DATE_FORMAT_LC2'), strtotime($testDate)); ?>" data-content-prefix="Termin:" data-icon-class="icon-calendar">WIRD UEBERSCHRIEBEN</abbr>
	 *
	 *					<pre class="prettify">
	 *						echo htmlentities(" <?php \$testDate = '07/15/2013 10:38'; ?> ");
	 *
	 *						echo '<strong><em>abbr tag => timeago (javascript supported)</em></strong>';
	 *						echo htmlentities('
	 *							<abbr
	 *								class="xtooltip ntime-fromnow"
	 *								data-time="2013-07-15 10:38"
	 *								title="Monday, 15 July 2013 10:38"
	 *								data-content-prefix="Termin:"
	 *								data-icon-class="icon-calendar">
	 *									Will be override by function
	 *							</abbr>
	 *						');
	 *
	 *						echo '<strong><em>abbr tag => calendar (javascript supported)</em></strong>';
	 *						echo htmlentities('
	 *							<abbr
	 *								class="xtooltip ntime-fromnow"
	 *								data-calendar="2013-07-15 10:38"
	 *								title="Monday, 15 July 2013 10:38"
	 *								data-content-prefix="Termin:"
	 *								data-icon-class="icon-calendar">
	 *									Will be override by function
	 *							</abbr>
	 *						');
	 *
	 *						echo '<strong><em>span tag => none (bootstrap supported)</em></strong>';
	 *							echo htmlentities('
	 *							<span class="xtooltip" data-original-title="Monday, 15 July 2013 10:38">
	 *								<i class="icon-clock"></i> 2013-07-15 10:38
	 *							</span>
	 *						');
	 *
	 *						UNIX TIMESTAMP
	 *
	 *						<?php echo htmlentities('<abbr class="ntime-fromnow" data-timestamp="2013-07-22 16:45:00"></abbr>'); ?>
	 *						<abbr class="ntime-fromnow" data-timestamp="2013-07-22 16:45:00"></abbr>
	 *					</pre>
	 *
	 * @since   8.0
	 */
	public function loadMoment($selector = '.ntime-fromnow', $debug = false)
	{
		$sig = md5(serialize(array($selector)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NFWHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('jquery.moment');
		self::dependencies('locales.de');

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration("
			jQuery(document).ready(function() {
				var dataTime;
				var dataCalender;
				var dataContentPrefix;
				var dataContentSuffix;
				var dataIconClass;

				initTableDataTime();

				function initTableDataTime() {
					$('" . $selector . "').each(function() {
						dataTime = $(this).data('time');
						dataCalendar = $(this).data('calendar');
						dataTimestamp = $(this).data('timestamp');

						dataContentPrefix = $(this).data('content-prefix') ? '<b>' + $(this).data('content-prefix') + '</b>' : '';
						dataContentSuffix = $(this).data('content-suffix') ? $(this).data('content-suffix') : '';
						dataIconClass = $(this).data('icon-class') ? '<i class=\'' + $(this).data('icon-class') + '\'></i> ' : '';

						if (dataTime) {
							dateTime = moment( dataTime ).fromNow();
						} else if (dataCalendar) {
							dateTime = moment( dataCalendar ).calendar();
						} else if (dataTimestamp) {
							dateTime = moment( dataTimestamp ).format('X');
						} else {
							dateTime = 'N/A';
						}

						$(this).html( dataIconClass + dataContentPrefix + ' <span>' + dateTime + '</span> ' + dataContentSuffix );

						console.log( dateTime );
					});
				}

				setInterval(initTableDataTime, 60000);

				$('a').on( 'click', function() {
					initTableDataTime();
					console.log( 'clicky :)' );
				});
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript support for moment, a js date library for parsing, validating, manipulating and formatting dates
	 *
	 * @param	string		$selector	
	 *
	 * @return  void
	 *
	 * @see loadMoment
	 * @since   8.0
	 */
	public function loadMomentOnly($selector = '.moment-function', $debug = false)
	{
		$sig = md5(serialize(array($selector)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NFWHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('jquery.moment');
		self::dependencies('locales.de');

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration("
			jQuery(document).ready(function() {
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript support for typeahead like search
	 *
	 * @param	string		$selector	
	 *
	 * @return  void
	 *
	 * @since   8.0
	 */
	public function loadTypeahead($selector = '#typeahead-search', $output = '#search-query', $url = 'index.php', $debug = false)
	{
		$sig = md5(serialize(array($selector, $output, $url)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NFWHtml::loadJsFramework();

		// Debug?
		if($debug) {
			$jsdebug = 'console.log (search_query);';
		} else {
			$jsdebug = null;
		}

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration("
			jQuery(document).ready(function() {
				$('" . $selector . "').keyup(function() {
					var search_query = $(this).val();

					" . $jsdebug . "

					$.post('" . $url . "', {typeahead_search : search_query}, function(searchq) {
						$('" . $output . "').html(searchq);
					});
				});
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript support for bootstrap timepicker
	 *
	 * @param	string		$selector	
	 *
	 * @return  void
	 *
	 * @since   13.7
	 */
	public function loadTimepicker($selector = '#timepicker', $step = 1, $seconds = true, $meridian = false)
	{
		$sig = md5(serialize(array($selector, $step, $seconds, $meridian)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NFWHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('bootstrap.timepicker');

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration("
			jQuery(document).ready(function() {
				$('" . $selector . "').timepicker({
					minuteStep: " . $step . ",
					showSeconds: '" . $seconds . "',
					showMeridian: '" . $meridian . "'
				});
			});\n"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add javascript support for bootstrap timepicker
	 *
	 * @param	string		$selector	
	 *
	 * @return  void
	 *
	 * @since   13.7
	 */
	public function loadDateRangePicker($selector = '#datepicker', $step = 1, $seconds = true, $meridian = false)
	{
		$sig = md5(serialize(array($selector, $step, $seconds, $meridian)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS framework
		NFWHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('bootstrap.datepicker');
		self::dependencies('jquery.moment');
		self::dependencies('locales.de');

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration("
			jQuery(document).ready(function() {





                  $('" . $selector . "').daterangepicker(
                     {
                        ranges: {
                           'Today': [new Date(), new Date()],
                           'Tomorrow': [moment().add('days', 1), moment().add('days', 1)],
                           'Last 7 Days': [moment().subtract('days', 6), new Date()],
                           'Last 30 Days': [moment().subtract('days', 29), new Date()],
                           'This Month': [moment().startOf('month'), moment().endOf('month')],
                           'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                        },
                        opens: 'left',
                        format: 'DD.MM.YYYY',
                        separator: ' to ',
                        startDate: moment().subtract('days', 29),
                        endDate: new Date(),
                        minDate: '01.01.2012',
                        maxDate: '31.12.2013',
                        locale: {
                            applyLabel: 'Submit',
                            fromLabel: 'From',
                            toLabel: 'To',
                            customRangeLabel: 'Custom Range',
                            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
                            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                            firstDay: 1
                        },
                        showWeekNumbers: true,
                        buttonClasses: ['btn-danger'],
                        dateLimit: false
                     },
                     function(start, end) {
                        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                     }
                  );
                  //Set the initial state of the picker label
                  $('#reportrange span').html(moment().subtract('days', 29).format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));







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
	public function dependencies($type, $dirHelper = '', $debug = null)
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

		if($type === 'jquery.chosen')
		{
			JHtml::_('stylesheet', 'nawala/jquery.chosen.min.css', false, true);
			JHtml::_('stylesheet', 'nawala/nawala.chosen.css', false, true);
			JHtml::_('script', 'nawala/jquery.chosen.min.js', false, true, false, false, $debug);
		}

		if($type === 'jquery.moment')
		{
			JHtml::_('script', 'nawala/jquery.moment.min.js', false, true, false, false, $debug);
		}

		if($type === 'locales.de')
		{
			JHtml::_('script', 'nawala/locales/jquery.moment.de.min.js', false, true, false, false, $debug);
		}

		if($type === 'bootstrap.timepicker')
		{
			JHtml::_('stylesheet', 'nawala/bootstrap.timepicker.css', false, true);
			JHtml::_('script', 'nawala/bootstrap.timepicker.min.js', false, true, false, false, $debug);
		}

		if($type === 'bootstrap.datepicker')
		{
			JHtml::_('stylesheet', 'nawala/bootstrap.daterangepicker.css', false, true);
			JHtml::_('script', 'nawala/bootstrap.daterangepicker.js', false, true, false, false, $debug);
		}

		if($type === 'alertify')
		{
			JHtml::_('stylesheet', 'nawala/alertify.core.css', false, true);
//			JHtml::_('stylesheet', 'nawala/alertify.default.css', false, true);
			JHtml::_('stylesheet', 'nawala/alertify.bootstrap.css', false, true);
			JHtml::_('script', 'nawala/alertify.min.js', false, true, false, false, $debug);
		}

		if($type === 'jquery.auto-geocoder')
		{
			JHtml::_('stylesheet', 'nawala/jquery.auto-geocoder.css', false, true);
			JFactory::getDocument()->addScript('//maps.google.com/maps/api/js?sensor=false');
			JHtml::_('script', 'nawala/jquery.auto-geocoder.min.js', false, true, false, false, $debug);
		}
		if($type === 'custom.auto-geocoder')
		{
			JHtml::_('stylesheet', 'nawala/jquery.auto-geocoder.css', false, true);
			JFactory::getDocument()->addScript('//maps.google.com/maps/api/js?sensor=false');
			JHtml::_('script', $dirHelper . 'custom.auto-geocoder.js', false, false, false, false, $debug); // customized version in direction
		}

		if($type === 'bootstrap.bootbox')
		{
			JHtml::_('script', 'nawala/bootstrap.bootbox.min.js', false, true, false, false, $debug);
		}

		if($type === 'jquery.easy-pie-chart')
		{
			JHtml::_('stylesheet', 'nawala/easy-pie-chart.css', false, true);
			JFactory::getDocument()->addScript('//maps.google.com/maps/api/js?sensor=false');
			JHtml::_('script', 'nawala/jquery.easy-pie-chart.js', false, true, false, false, $debug);
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