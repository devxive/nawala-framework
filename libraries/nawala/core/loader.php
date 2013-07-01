<?php
/**
  * @info		$Id$ - $Revision$
  * @package		$Nawala.Framework $
  * @subpackage	Framework
  * @check		$Date$ || $Result: devXive AntiMal...OK, nothing found $
  * @author		$Author$ @ devXive - research and development <support@devxive.com>
  * @copyright	Copyright (C) 1997 - 2013 devXive - research and development (http://www.devxive.com)
  * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  * @assetsLicense	devXive Proprietary Use License (http://www.devxive.com/license)
  */

// no direct access
defined('_NFWRA') or die;

jimport('joomla.filesystem.file');

class NLoader
{
	/**
	 * Class loader method
	 *
	 * @param	string		$key	The name of helper method to load, mainClass.subClass
	 * 					The mainClass is also used as folder- and subClass as filename.
	 * 					Both together are build the className which is used in the file.
	 * 					Example:
	 * 			     			Key: NHtml.DataTable
	 * 			     			       |       |
	 * 			     		          mainClass   |
	 * 			     			       |    subClass
	 * 			     			     Folder    |
	 * 			     			            Filename
	 *
	 * @return	mixed			Import the appropriate class or false on error
	 *
	 * @since	13.6
	 */
	public static function _($key)
	{
		$key = preg_replace('#[^A-Z0-9_\.]#i', '', $key);

		// extract the key to see what file we have to import
		$parts = explode('.', $key);

		if(count($parts) == 2) {
			$mainClass	= array_shift($parts); // Could be "NHtml"
			$subClass	= array_shift($parts); // Could be "DataTable"
		} else {
			return sprintf('%s is not a valid key.', $key);
		}

		// Substract the first letter from rawFolder (Makes ie. "Html" from "NHtml")
		$folder = substr($mainClass, 1);

		// Ensure we fit naming conv. to check if the class have already been loaded.
		// Like example above: "NHtmlDataTable" which is the abstract class we have in "html/datatable.php".
		$className = ucfirst($folder) . ucfirst($subClass);

		// Check if className already exist, else load the appropriate file
		if (!class_exists($className))
		{
			$path = NPATH_FRAMEWORK . '/' . strtolower($folder) . '/' . strtolower($subClass) . '.php';

			// Check if file exist, else throw exception
			if (JFile::exists($path))
			{
				require_once $path;

				if (!class_exists($className))
				{
					return sprintf('%s not exist.', $className);
				}
			}
			else
			{
				return sprintf('%s not exist.', $path);
			}
		}
	}
}