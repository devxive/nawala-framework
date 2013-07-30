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

jimport('joomla.filesystem.folder');

class NFile {
	/**
	 * Write content to a local file
	 * @param  string $path      the path to the local file, if path = null, try to save file to current folder
	 * @param  string $filename  the filename
	 * @param  string $content   the content to write in file
	 * @param  string $config    the config to identicate if we want to overwrite an existing file or to append existing content
	 *
	 */
	public function write($content, $filename, $path = null, $config = null) {
//		if (!$path) {
//			$path = realpath(dirname(__FILE__);
//		}
		if (!$config) {
			JFile::write($path . $filename, $content);
		} else {
			if (JFile::exists($path . $filename)) {
				//TODO: OUTPUT JError
			} else {
				JFile::write($path . $filename, $content);
			}
	}
}