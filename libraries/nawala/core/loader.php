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
	 * @param	string		$key		The name of helper method to load, mainClass.subClass
	 * 						The mainClass is also used as folder- and subClass as filename.
	 * 						Both together are build the className which is used in the file.
	 * 						Example:
	 * 				     			Key: NHtml.DataTable
	 * 				     			       |       |
	 * 				     		          mainClass   |
	 * 				     			       |    subClass
	 * 				     			     Folder    |
	 * 				     			            Filename
	 *
	 * @param	bool		$dep		True or false if we should load dependency core/main class
	 *
	 * @return	mixed				Import the appropriate class or false on error
	 *
	 * @since	13.6
	 */
	public static function _($key, $dep = true)
	{
		$key = preg_replace('#[^A-Z0-9_\.]#i', '', $key);

		// extract the key to see what file we have to import
		$parts = explode('.', $key);

		// Build className, file and folder from the key and/or the extracted parts of the key
		// Like: "NHtmlDataTable" which is the abstract class we have in "html/datatable.php".
		if(count($parts) == 2) {
			$mainClass	= array_shift($parts); // Could be "NHtml"
			$subClass	= array_shift($parts); // Could be "DataTable"

			// Substract the first letter from mainClass (Makes ie. "Html" from "NHtml")
			$folder 	= strtolower(substr($mainClass, 1));
			$file 		= strtolower($subClass);

			$depFile	= NPATH_FRAMEWORK . '/' . $folder . '/' . $folder . '.php';
			$classFile	= NPATH_FRAMEWORK . '/' . $folder . '/' . $file . '.php';

			// Ensure we fit naming conv. (uc first 2 letters of the main class) and use the foldername to build the class name
			$className	= 'N' . ucfirst($folder) . ucfirst($subClass);
		} else {
			return sprintf('%s is not a valid key.', $key);
		}

		/*
		 * Check if we need dependency and if classes doesn't exist, else load the appropriate files
		 */

		// Dependency aka mainClass
		if($dep && !class_exists($mainClass)) {
			if(JFile::exists($depFile)) {
				require_once $depFile;
			} else {
				return sprintf('%s not exist.', $depFile);
			}

			if(!class_exists($mainClass))
			{
				return sprintf('%s not exist.', $className);
			}
		}

		// Class aka mainClassSubClass
		if(!class_exists($className))
		{
			if(JFile::exists($depFile)) {
			require_once $classFile;
			} else {
				return sprintf('%s not exist.', $classFile);
			}

			if(!class_exists($className))
			{
				return sprintf('%s not exist.', $className);
			}
		} else {
			return sprintf('%s or %s not exist.', $classFile, $className);
		}
	}
}