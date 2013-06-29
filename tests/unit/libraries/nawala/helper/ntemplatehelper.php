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

class NTemplateHelper {

	/**
	 * Stores the JDocument class
	 * @var	JDocument
	 * @since	11.3
	 */
//	public $doc = JFactory::getDocument();
	var $doc;

	/**
	 * Stores the headdata from the JDocument class
	 * @var	headData
	 * @since	11.3
	 */
//	public $headData = $doc->getHeadData();
	var $headData;

	/**
	 * Stores the JSession class
	 * @var	Session
	 * @since	11.3
	 */
//	public $sessionData = JFactory::getSession();
	var $sessionData;

	/**
	 * INITIATE THE CONSTRUCTOR
	 */
	public function __construct() {
		$docInit = JFactory::getDocument();
		$this->doc = $docInit;
//		$this->headData = $docInit->getHeadData();
		$this->sessionData = JFactory::getSession();
	}

	/**
	 * remove selected javascripts from head
	 * @param	string		$fnJs		the filenames of the scriptfiles - comma separated (file1,file2)
	 * @param	self		$doc
	 * @param	self		$headData
	 * @since	11.3
	 */
	public function removeScript($fnJs) {
		if(trim($fnJs) != '') {
			$filesJs=explode(',', $fnJs);
			$head = (array) $this->headData['scripts'];
			$newHead = array();
			foreach($head as $key => $elm) {
				$add = true;
				foreach ($filesJs as $dis) {
					if (strpos($key,$dis) !== false) {
						$add=false;
						break;
					}
				}
				if($add) {
					$newHead[$key] = $elm;
				}
			}
			$headData['scripts'] = $newHead;
			return $this->doc->setHeadData($headData);
		} else {
			return false;
		}
	}

	/**
	 * remove script declarations from the core
	 * @since	11.3
	 */
	public function removeScriptDec() {
	}

	/**
	 * remove selected stylesheets from head
	 * @param	string		$fnCss		the filenames of the stylefiles - comma separated (file1,file2)
	 * @param	self		$doc
	 * @param	self		$headData
	 * @since	11.3
	 */
	public function removeStyle($fnCss) {
		if(trim($fnCss) != '') {
			$filesCss=explode(',', $fnCss);
			$head = (array) $this->headData['styleSheets'];
			$newHead = array();
			foreach($head as $key => $elm) {
				$add = true;
				foreach ($filesCss as $dis) {
					if(strpos($key,$dis) !== false) {
						$add=false;
						break;
					}
				}
				if($add) {
					$newHead[$key] = $elm;
				}
			}
			$headData['styleSheets'] = $newHead;
			return $this->doc->setHeadData($headData);
		} else {
			return false;
		}
	}

	/**
	 * remove style declarations from the core
	 * @since	11.3
	 */
	public function removeStyleDec() {
	}

	/**
	 * remove the generator tag from head
	 * @param	string		$setGen	nothing, its always null
	 * @param	self		$doc
	 * @since	11.3
	 */
	public function removeGenerator() {
		return $this->doc->setGenerator(null);
	}

	/**
	 * force latest IE & chrome frame
	 * @param	string		$forceIeCf	nothing, its always true
	 * @param	self		$doc
	 *
	 */
	public function forceIeChromeFrame() {
		return $this->doc->setMetadata('x-ua-compatible', 'IE=edge,chrome=1');
	}

	/**
	 * add new javascript files to head
	 * $param	string		$type		file or custom
	 * @param	string		$string	can be either the filename of the scriptfile or the custom content of the script
	 * @param	string		$dirName	load from framework or template assets/js ("framework" force load from framework, else the templatename)
	 * @param	self		$doc
	 * @since	11.3
	 */
	public function addNewJsHead($type, $string, $dirName = 'framework') {
		if($type == 'file'):
			if($direction == 'framework') {
				$baseurl = '/media/nawala/js/' . $string;
			} else {
				$baseurl = '/templates/' . $dirName . '/assets/js/' . $string;
			}
			return $this->doc->addScript($url);
		else:
			return $this->doc->addScriptDeclaration($string);
		endif;
	}

	/**
	 * add new javascript files to body bottom - just before the ending body tag
	 * 	add new javascript files and/or script declarations at the bottom of the template (increase pageload and YSlow), need to be load AFTER jQuery and bootstrap
	 * 	add new javascript files and/or script declarations AFTER "addNewJsBottom" such as custom scripts, applications, JsChecks or anything else from components
	 * $param	string		$type		file or custom
	 * @param	string		$string	can be either the filename of the scriptfile or the custom content of the script
	 * @param	string		$dirName	load from framework or template assets/js ("framework" force load from framework, else the templatename)
	 * @param	int+		$order		ensure that the scripts loaded in correct order - note: 1 - 99 is reserved for system
	 * @param	self		$doc
	 * @since	11.3
	 * @output					stores scripts in session to be load in template NFWS = (N)awala (F)rame(W)ork (S)ession
	 */
	public function addNewJsBodyBottom($type, $string, $dirName = 'framework', $order) {
		$sessionScripts = $this->sessionData->get('NFWSScriptBodyBottom');
		if($type == 'file')
		{
			if($dirName == 'framework') {
				$url = '/media/nawala/js/' . $string;
				$sessionScripts[] = '<!--' . $order . '--><script type="text/javascript" src="' . $url . '"></script>';
			} else {
				$url = '/templates/' . $dirName . '/assets/js/' . $string;
				$sessionScripts[] = '<!--' . $order . '--><script type="text/javascript" src="' . $url . '"></script>';
			}
		}
		else
		{
			$sessionScripts[] = '<!--' . $order . '--><script type="text/javascript">' . $string . '</script>';
		}

		return $this->sessionData->set('NFWSScriptBodyBottom', $sessionScripts);
	}

	/**
	 * add new stylesheet files to head
	 * $param	string		$type		file or custom
	 * @param	string		$string	can be either the filename of the stylesheet or the custom content of the style
	 * @param	string		$dirName	load from framework or template assets/css ("framework" force load from framework, else the templatename)
	 * @param	self		$doc
	 * @since	11.3
	 */
	public function addNewCssHead($type, $string, $dirName = 'framework') {
		if($type == 'file')
		{
			if($dirName == 'framework') {
				$url = '/media/nawala/css/' . $string;
			} else {
				$url = '/templates/' . $dirName . '/assets/css/' . $string;
			}
			return $this->doc->addStyleSheet($url);
		}
		else
		{
			return $this->doc->addStyleDeclaration($string);
		}
	}

	/**
	 * load the style and js framework if nessesary
	 * @param	string		$type		type of what framework should be load ("core" or "nawala")
	 * @param	boolean	$noConflict	should noconflict load? true or false
	 */
	public function loadFramework($type, $noConflict) {
		if($type == 'core')
		{
			return JHtml::_('bootstrap.framework');
		}
		else if($type == 'nawala')
		{
			$baseurl = '/media/nawala/js/';
			$scriptDec[] = '<!--0001--><script type="text/javascript" src="' . $baseurl . 'jquery.min.js"></script>';
			if($noConflict) {
				$scriptDec[] = '<!--0002--><script type="text/javascript" src="' . $baseurl . 'jquery-noconflict.js"></script>';
			}
			$scriptDec[] = '<!--0003--><script type="text/javascript" src="' . $baseurl . 'bootstrap.min.js"></script>';
			return $this->sessionData->set('NFWSScriptBodyBottom', $scriptDec);
		}
		else
		{
			$baseurl = '/templates/' . $type . '/assets/js/';
			$scriptDec[] = '<!--0001--><script type="text/javascript" src="' . $baseurl . 'jquery.min.js"></script>';
			if($noConflict) {
				$scriptDec[] = '<!--0002--><script type="text/javascript" src="' . $baseurl . 'jquery-noconflict.js"></script>';
			}
			$scriptDec[] = '<!--0003--><script type="text/javascript" src="' . $baseurl . 'bootstrap.min.js"></script>';
			return $this->sessionData->set('NFWSScriptBodyBottom', $scriptDec);
		}
	}

	/**
	 * use this only in template body bottom just before the ending body tag
	 */
	public function loadJsBodyBottom() {
		$sessionScripts = $this->sessionData->get('NFWSScriptBodyBottom');
		if($sessionScripts)
		{
			sort($sessionScripts);
			$html = '<!-- NFWSScriptBodyBottom -->' . "\n";
			foreach($sessionScripts as $script)
			{
				$html .= $script . "\n";
			}
			$html .= '</div>';

			echo $html;
		}
	}
}