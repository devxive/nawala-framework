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
 * Nawala HTML Select2 Class
 * Support for Javascript select procedures
 *
 */
abstract class NFWHtmlSelect2
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();


	/**
	 * Add javascript support for select2 lists
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
	 * @see http://ivaynberg.github.com/select2/ for more informations and options
	 *
	 * @since   6.0
	 */
	public function init($selector = '.select2', $params = array())
	{
		$sig = md5( serialize( array($selector) ) );

		// Only load once
		if ( isset(self::$loaded[__METHOD__][$sig]) )
		{
			return;
		}

		// Include JS framework
		NFWHtml::loadJsFramework();

		// Include dependencies
		self::dependencies('jquery.select2');

		// Setup options object
		$opt['disable_search_threshold'] = isset($params['disable_search_threshold']) ? $params['disable_search_threshold'] : null;
		$opt['disable_search'] = isset($params['disable_search']) ? $params['disable_search'] : null;
		$opt['no_results_text'] = isset($params['no_results_text']) ? $params['no_results_text'] : null;
		$opt['max_selected_options'] = isset($params['max_selected_options']) ? $params['max_selected_options'] : null;
		$opt['allow_single_deselect'] = isset($params['allow_single_deselect']) ? $params['allow_single_deselect'] : null;
		$opt['width'] = isset($params['width']) ? $params['width'] : null;

		$options = NFWHtml::getJSObject($opt);

		// Build the scriptDeclaration
		$srciptDec = 
			"jQuery(document).ready(function() {
				$('" . $selector . "').select2(" . $options . ");
			});\n";

		// Attach the function to the document
		JFactory::getDocument()->addScriptDeclaration($srciptDec);

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

		if($type === 'jquery.select2')
		{
			JHtml::_('stylesheet', 'nawala/jquery.select2.css', false, true);
			JHtml::_('script', 'nawala/jquery.select2.min.js', false, true, false, false, $debug);
			JHtml::_('script', 'nawala/locales/jquery.select2.de.js', false, true, false, false, $debug);
		}

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}
}