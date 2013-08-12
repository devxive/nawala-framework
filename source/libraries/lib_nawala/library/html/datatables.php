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
abstract class NFWHtmlDatatables
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Add javascript Datatables support
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
	 * Xive-TODO:					// JSON Format example, dataTable as var
	 * 						jQuery(function() {
	 * 							var oTable = $("#table_contacts").dataTable( {
	 * 								"aoColumns": [
	 * 									{ "bSortable": false },
	 * 									null, null, null, null, null, null,
	 * 									{ "bSortable": false }
	 * 								]
	 * 							});
	 * 						});
	 *
	 * Xive-TODO:					// JSON Format example with checkboxes
	 * 						jQuery(function() {
	 * 							$(\'table th input:checkbox\').on(\'click\' , function(){
	 * 								var that = this;
	 * 								$(this).closest(\'table\').find(\'tr > td:first-child input:checkbox\')
	 * 									.each(function(){
	 * 										this.checked = that.checked;
	 * 										$(this).closest(\'tr\').toggleClass(\'selected\');
	 * 									});
	 * 							});
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
	public function loadDataTable($selector = 'table', $params = null, $grouping = null)
	{
		$sig = md5(serialize(array($selector, $params)));

		// Only load once
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Include JS frameworks
		NFWHtml::loadJsFramework();

		// Include DataTables dependencies
		self::dependencies();
		NFWHtmlJavascript::dependencies('ui.effects');

		if($grouping) {
			$grouping = '.rowGrouping()';
		}

		// Check if $params is an array, else json_encode the params
		if(is_array($params)) {
			// Attach the function to the document
			JFactory::getDocument()->addScriptDeclaration(
				"jQuery(document).ready(function() {
					var oTable = $('#" . $selector . "').dataTable(" . json_encode($params) . ")" . $grouping . ";
				});\n"
			);
		} else {
			// Attach the function to the document
			JFactory::getDocument()->addScriptDeclaration(
				"jQuery(document).ready(function() {
					var oTable = $('#" . $selector . "').dataTable(" . $params . ")" . $grouping . ";

					/* Add event listener for opening and closing details
					 * Note that the indicator for showing which row is open is not controlled by DataTables,
					 * rather it is done here
					 * Bootstrap, FontAwesome version 1.0
					 */
					$('#" . $selector . " tbody td a.rowToggle').on('click', function () {
						var nTr = $(this).parents('tr')[0];
						if ( oTable.fnIsOpen(nTr) )
						{
							/* This row is already open - close it */
							$(this).children('i')[0].className = 'icon-eye-close icon-only';
							$(this).children('i')[0].removeClass('red');
							$('div.innerDetails', $(nTr).next()[0]).slideUp('slow', 'easeInOutCubic', function() {
								oTable.fnClose( nTr );
							});
						}
						else
						{
							/* Open this row */
							$(this).eq('i').className = 'icon-eye-open icon-only';
							$(this).eq('i').addClass('red');
							var nDetailsRow = oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
							$('div.innerDetails', nDetailsRow).slideDown('slow', 'easeInOutCubic', function() {} );
						}
					});
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
		NFWHtml::loadJsFramework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug = (boolean) $config->get('debug');
		}

		JHtml::_('script', 'nawala/jquery.dataTables.min.js', false, true, false, false, $debug);
		JHtml::_('script', 'nawala/jquery.dataTables.bootstrap.js', false, true, false, false, $debug);
		JHtml::_('script', 'nawala/jquery.dataTables.rowGrouping.js', false, true, false, false, $debug);

		if($loadCss === true)
		{
			JHtml::_('stylesheet', 'nawala/jquery.dataTables.css', false, true);
		}

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}
}