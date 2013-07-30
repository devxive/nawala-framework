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

class NConnect {
	/**
	 * Connect to an external url and load file content
	 * @param  string $url    the full url to the file (incl. http://)
	 *
	 * Workaround: check if we have to use http(s), adding more methods instead of curl (fopen maybe)
	 */
	public function __construct($url) {
		try {
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$data = curl_exec($ch);
			curl_close($ch);
			$response->code = 200;
			$response->body = $data;
		} catch (Exception $e) {
			$response->code = 900;
			$response->body = 'An Error has occured, please try again later!';
			$this->setError($e->getMessage());
			return false;
		}
		return $response;
	}
}	
