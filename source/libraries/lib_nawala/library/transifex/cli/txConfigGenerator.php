<?php
/**
 * Build a transifex config file wthin seconds.
 *
 * @project		XAP Project - Xive-Application-Platform
 * @subProject	Nawala Framework - A PHP and Javascript framework
 *
 * @package		NFW.Library
 * @subPackage	CLI.Transifex
 * @date		06 June 2013
 * @version		1.3
 *
 * @author		devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright		Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense	devXive Proprietary Use License (http://www.devxive.com/license)
 *
 * @since		3.2
 *
 *
 * NOTE:
 * This file contains $vars as an example. It reflect xap and joomla file structures, which have to be set to your project settings!
 *
 *
 * Requirements:
 * - You need an existing project on transifex
 * - You need the transifex command line client
 *
 *
 * Usage:
 * Copy this file at your webroot and open this file with your favourite browser, or use a php command line tool
 *
 *
 * Transifex settings:
 * - $txHost			= The transifex host. If you do not use your own, the url is: https://www.transifex.com
 * - $txLangMap		= The language mapping setting. First is used on transifex, second is what you use in your project.
 *				  Example: "es" is used on transifex, but you need in your project "es-ES" the tag is "es: es-ES"
 *				  It's easy => "REMOTE_CODE: LOCAL_CODE, es: es-ES, de_DE: de-DE" and so on 
 * - $txType			= Type of the file identification depending on your files you have in your projects. INI, POT, PHP, etc...
 * - $txProjectSlug		= The project slug as seen in your url ".../p/projects/yourProjectSlug/....."
 * - $txSourceLang 		= source language ie. "en_GB" - tip: hover your mouse over a source language on www.transifex.com
 * - $txMinimumPerc 		= Use minimum percentage for translations to download? Enter the percentage without trailing %.
 *
 *
 * Local project settings:
 * - $sourceDirectories 	= Array with all directories we're looking in for files. Set up all directories with a slug to identify files
 *				  identically named for example in admin and site folders array("admin" => "administrator/language", "....)
 * - $sourceLang		= source language ie. in joomla "en-GB"
 * - $sourceWhitelistExt	= Only files containing ie: all .ini files or all files ending with .sys.pot
 * - $finalSourceFileDir 	= Array of directories where the tx client will be look for the source files.
 *				  This might be not the same directory as the "$sourceDirectories array"
 *				  You have to use the same keys/slugs as you used in the $sourceDirectories array - the directory may be different
 * - $finalFilterFileDir	= Array of directories where the tx client will be stored the downloaded translations
 *				  This might be not the same directory as the "$sourceDirectories array"
 *				  You have to use the same keys/slugs as you used in the $sourceDirectories array - the directory may be different
 * - $forceDownload		= Want to download the file after generating, set $forceDownload to true, otherwise to false.
 *				  Don't forget to remove the .txt from downloaded file and copy into your .tx folder!!!
 */

$txHost = "https://www.transifex.com";
$txLangMap = "de_DE: de-DE";
$txType = "INI";
$txProjectSlug = "xive-application-platform";
$txSourceLang = "en_GB";
$txMinimumPerc = false;
$sourceDirectories = array(
		"admin" => "administrator/language",
		"site" => "language",
);
$sourceLang = "en-GB";
$sourceWhitelistExt = ".ini";
$finalSourceFileDir = array(
		"admin" => "source/administrator/language",
		"site" => "source/language"
);
$finalFilterFileDir = array(
		"admin" => "source/administrator/language",
		"site" => "source/language"
);
$forceDownload = false;

/************************* DO NOT EDIT BELOW CODE - ALL SETTINGS ARE IN THE SECTION ABOVE *************************/
if($forceDownload) {
	header('Content-disposition: attachment; filename=config');
}
header('Content-type: text/plain');

$filesArray = array();

// reading the directories and store all files in an array
foreach($sourceDirectories as $slug => $dir) {
	if ($handle = opendir($dir . '/' . $sourceLang . '/')) {
		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..' && preg_match("/$sourceWhitelistExt/i", $file)) {
				$filesArray[] = $slug . ',' . $file;
			}
		}

		closedir($handle);
	}
}

sort($filesArray);

// build the tx config file head [main] section
echo "[main]\n";
echo "host = $txHost\n";
echo "lang_map = $txLangMap\n";
echo "type = $txType\n";
echo "\n";

// build the tx config file files section
foreach($filesArray as $file) {
	$fileArray = explode(",", $file);

	$key = $fileArray[0];
	$sourceLangFile = $fileArray[1];
	$stripSourceLang = str_replace($sourceLang . '.', '', $fileArray[1]);

	$txFileSlug = $key . '-' . str_replace('.', '_', $stripSourceLang);

	if($fileArray[1] == $sourceWhitelistExt) {
		$txFileSlug = $key . '-main_' . str_replace('.', '_', $stripSourceLang);
	}

	echo "[$txProjectSlug.$txFileSlug]\n";
	echo "source_file = $finalSourceFileDir[$key]/$sourceLang/$sourceLang.$stripSourceLang\n";
	echo "file_filter = $finalFilterFileDir[$key]/<lang>/<lang>.$stripSourceLang\n";
	echo "source_lang = $txSourceLang\n";
	echo $txMinimumPerc ? "minimum_perc = $txMinimumPerc\n" : '';
	echo "\n";
}
?>