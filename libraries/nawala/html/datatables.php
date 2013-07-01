<?php
/**
 * @version     5.0.0
 * @package     NAWALA FRAMEWORK
 * @subPackage  NHtmlJSHelper
 * @copyright   Copyright (C) 1997 - 2013 by devXive - research and development. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      devXive <support@devxive.com> - http://devxive.com
 */

defined('_JEXEC') or die;

abstract class NHtmlDataTables
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Add javascript DataTables support
	 *
	 * @param	string		$selector	Common id for the table
	 * @param	array		$params	Common params for the table
	 *
	 * 						// JSON encoded array's example
	 * 						$params = json_encode(
	 * 							array(
	 * 								'bProcessing' => true,
	 * 								'bPaginate' => false,
	 * 								'aoColumnDefs' => array(
	 * 									array( 'bSortable' => false, 'aTargets' => array(0) ),
	 * 									array( 'bSortable' => false, 'aTargets' => array(7) ),
	 * 									array( 'bSearchable' => false, 'aTargets' => array(0) ),
	 * 									array( 'bSearchable' => false, 'aTargets' => array(6) ),
	 * 									array( 'bSearchable' => false, 'aTargets' => array(7) )
	 * 								)
	 * 							)
	 * 						);
	 *
	 * 						// JSON Format example with ajaxSource
	 * 						$params = "{
	 * 							'bProcessing': true,
	 * 							'bPaginate': false,
	 * 							'aoColumnDefs': [
	 * 								{ 'bSortable': false, 'aTargets': [0] },
	 * 								{ 'bSortable': false, 'aTargets': [7] },
	 * 								{ 'bSearchable': false, 'aTargets': [0] },
	 * 								{ 'bSearchable': false, 'aTargets': [6] },
	 * 								{ 'bSearchable': false, 'aTargets': [7] }
	 * 							]
	 * 						}';
	 *
	 * 						// JSON Format example
	 * 						$params = "{
	 * 							"bProcessing": true,
	 * 							"bServerSide": true,
	 * 							"bStateSave": true, // save the state of the table (i.e. you are on page 15, the icookieduration save this for 15 minutes)
	 * 							"iCookieDuration": 60*15, // Used to save the state for 15 minutes (default 2 hours)
	 * 							"sAjaxSource": "index.php?option=com_mycomponent&task=api.getTableList&' . JFactory::getSession()->get('session.token') . '=1",
	 * 							"aoColumns": [
	 * 								{ "sTitle": "ID", "mData": "id" },
	 * 								{ "sTitle": "Last Name", "mData": "last_name" },
	 * 								{ "sTitle": "First Name", "mData": "first_name" },
	 * 								{ "sTitle": "Gender", "mData": "gender" },
	 * 								{ "sTitle": "Phone", "mData": "phone" },
	 * 								{ "sTitle": "Remarks", "mData": "remarks" }
	 * 							]
	 * 						});
	 *
	 * @return  void
	 *
	 * @see     www.datatables.net/ref		Full list of references
	 * @see     www.datatables.net/usage	Introduction to dataTables
	 * @see     www.datatables.net/examples	Practical examples
	 *
	 * @since   5.0
	 */
	public function loadDataTable($selector = 'table', $params = null)
	{
		$sig = md5(serialize(array($selector, $params)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS frameworks
		NHtml::loadJsFramework();

		// Include DataTables dependencies
		self::dependencies();

		// Check if $params is an array, else json_encode the params
		if(is_array($params)) {
			// Attach the function to the document
			JFactory::getDocument()->addScriptDeclaration(
				"jQuery(document).ready(function() {
					$('#$selector').dataTable(".json_encode($params).");
				});\n"
			);
		} else {
			// Attach the function to the document
			JFactory::getDocument()->addScriptDeclaration(
				"jQuery(document).ready(function() {
					$('#$selector').dataTable($params);
				});\n"
			);
		}

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
		NHtml::loadJsFramework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug = (boolean) $config->get('debug');
		}

		JHtml::_('script', 'nawala/jquery.dataTables.min.js', false, true, false, false, $debug);
		JHtml::_('script', 'nawala/jquery.dataTables.bootstrap.js', false, true, false, false, $debug);

		if($loadCss === true)
		{
			JHtml::_('stylesheet', 'nawala/jquery.dataTables.css', false, true);
		}

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}
}