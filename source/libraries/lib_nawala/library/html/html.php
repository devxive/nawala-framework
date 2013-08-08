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
 * Utility class for all HTML drawing classes
 *
 */
abstract class NFWHtml
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  13.0
	 */
	protected static $loaded = array();

	/**
	 * Method to load the Bootstrap JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of Bootstrap is included for easier debugging.
	 *
	 * @param   boolean	$noConflict  True to load jQuery in noConflict mode [optional]
	 * @param   mixed	$debug  Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   13.0
	 */
	public static function loadJsFramework($noConflict = false, $debug = null)
	{
		// Only load once
		if (!empty(self::$loaded[__METHOD__]))
		{
			return;
		}

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug = (boolean) $config->get('debug');
		}

		// Load jQuery
		JHtml::_('script', 'nawala/jquery.min.js', false, true, false, false, $debug);

		// Check if we are loading in noConflict
		if ($noConflict)
		{
			JHtml::_('script', 'nawala/jquery-noconflict.js', false, true, false, false, false);
		}

		JHtml::_('script', 'nawala/bootstrap.min.js', false, true, false, false, $debug);

		self::$loaded[__METHOD__] = true;

		return;
	}

	/**
	 * Loads CSS files needed by Bootstrap
	 *
	 * @param   boolean  $includeMainCss  If true, main bootstrap.css files are loaded
	 * @param   string   $direction       rtl or ltr direction. If empty, ltr is assumed
	 * @param   array    $attribs         Optional array of attributes to be passed to JHtml::_('stylesheet')
	 *
	 * @return  void
	 *
	 * @since   13.0
	 */
	public static function loadCssFramework($includeMainCss = true, $direction = 'ltr', $attribs = array())
	{
		// Load Bootstrap main CSS
		if ($includeMainCss)
		{
			JHtml::_('stylesheet', 'nawala/bootstrap.min.css', $attribs, true);
			JHtml::_('stylesheet', 'nawala/bootstrap-responsive.min.css', $attribs, true);
		}

		// Load Bootstrap RTL CSS
		if ($direction === 'rtl')
		{
			JHtml::_('stylesheet', 'nawala/bootstrap-rtl.css', $attribs, true);
		}
	}

	/**
	 * Internal method to get a JavaScript object notation string from an array
	 *
	 * @param   array  $array  The array to convert to JavaScript object notation
	 *
	 * @return  string  JavaScript object notation representation of the array
	 *
	 * @since   13.6
	 */
	public static function getJSObject(array $array = array())
	{
		$elements = array();

		foreach ($array as $k => $v)
		{
			// Don't encode either of these types
			if (is_null($v) || is_resource($v))
			{
				continue;
			}

			// Safely encode as a Javascript string
			$key = json_encode((string) $k);

			if (is_bool($v))
			{
				$elements[] = $key . ': ' . ($v ? 'true' : 'false');
			}
			elseif (is_numeric($v))
			{
				$elements[] = $key . ': ' . ($v + 0);
			}
			elseif (is_string($v))
			{
				if (strpos($v, '\\') === 0)
				{
					// Items such as functions and JSON objects are prefixed with \, strip the prefix and don't encode them
					$elements[] = $key . ': ' . substr($v, 1);
				}
				else
				{
					// The safest way to insert a string
					$elements[] = $key . ': ' . json_encode((string) $v);
				}
			}
			else
			{
				$elements[] = $key . ': ' . self::getJSObject(is_object($v) ? get_object_vars($v) : $v);
			}
		}

		return '{' . implode(',', $elements) . '}';

	}


	/**
	 * Method to create a checkbox for a grid row.
	 *
	 * @param     integer    $row_num        The row index
	 * @param     integer    $rec_id         The record id
	 * @param     boolean    $checked_out    True if item is checke out
	 * @param     string     $name           The name of the form element
	 *
	 * @return    mixed                      String of html with a checkbox if item is not checked out, null if checked out.
	*/
	public static function id($row_num, $rec_id, $checked_out = false, $name = 'cid')
	{
		if ($checked_out) {
			return '';
		}
		else {
			return '<input type="checkbox" id="cb' . $row_num . '" name="' . $name . '[]" value="' . $rec_id
				. '" onclick="Joomla.isChecked(this.checked); PFlist.toggleBulkButton();" title="'
				. JText::sprintf('JGRID_CHECKBOX_ROW_N', ($row_num + 1)) . '" />';
		}
	}


	/**
	 * Returns a truncated text. Also strips html tags
	 *
	 * @param     string    $text     The text to truncate
	 * @param     int       $chars    The new length of the string
	 *
	 * @return    string              The truncated string
	 */
	public static function truncate($text = '', $chars = 40)
	{
		$truncated = strip_tags($text);
		$length    = strlen($truncated);

		if (($length + 3) < $chars || $chars <= 0) return $truncated;

		return substr($truncated, 0, ($chars - 3)) . '...';
	}


	/**
	 * Method to remove the JUI
	 *
	 * @return    void
	 */
	public static function removeJUI($site = true, $admin = false)
	{
		$app = JFactory::getApplication();

		if ( $site && $app->isSite() ) {
			// Get the _scripts from HeadData
			$document = JFactory::getDocument();
			$headData = $document->getHeadData();
			$scripts = $headData['scripts'];

			// Unset JUI scripts
			unset($scripts['/media/jui/js/bootstrap.min.js']);
			unset($scripts['/media/jui/js/jquery-noconflict.js']);
			unset($scripts['/media/jui/js/jquery.min.js']);

			// Pull back the modified HeadData
			$headData['scripts'] = $scripts;
			$document->setHeadData($headData);
		}

		if ( $admin && $app->isAdmin() ) {
			// Get the _scripts from HeadData
			$document = JFactory::getDocument();
			$headData = $document->getHeadData();
			$scripts = $headData['scripts'];

			// Unset JUI scripts
			unset($scripts['/media/jui/js/bootstrap.min.js']);
			unset($scripts['/media/jui/js/jquery-noconflict.js']);
			unset($scripts['/media/jui/js/jquery.min.js']);

			// Pull back the modified HeadData
			$headData['scripts'] = $scripts;
			$document->setHeadData($headData);
		}
	}
}