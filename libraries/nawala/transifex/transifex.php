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

class NTransifex {

	/**
	 * @param	string $projectslug	var	Projects slug/alias used on transifex.com (transifex, projectfork-languages, etc.)
	 * @param	string $lang		var	language string used on transifex.com (de_DE, da_DK, etc.)
	 * @param	string $username	var	the username
	 * @param	string $password	var	the password
	 * @param	string $timeout	int	standard 5
	 * @param	string $debug		int	standard null, use "1" or TRUE to return debug informations
	 * @param	string $type		var	standard null, use "json" or TRUE to return a json string
	 *
	 * @example	nawala_import('addon.ntransifex', 'once');
	 * @example	$tx = NTransifex::getLangStats('projectfork_languages', 'de_DE', 'MyUsername', 'MyPassword');
	 * @example	echo 'Language Statistics for de_DE: ' . $tx->translated_segments . ' from ' . $tx->total_segments . ' translated';
	 */
	public function getLangStats($projectslug, $lang, $username, $password, $timeout = 5, $debug = null, $type = null) {
		$apiUrl = 'https://transifex.com/api/2/project/' . $projectslug . '/language/' . $lang . '/?details';

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $apiUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
//		curl_setopt($ch, CURLOPT_HTTPHEADERS, array('Content-Type: application/json'));

		$data = curl_exec($ch);
		if($debug)
		{
			$response->code = 200;
			$response->body = $data;
			$response->debug = curl_getinfo($ch);
		}
		else
		{
			$response = $data;
		}

		curl_close($ch);

		if($type)
		{
			return $response;
		}
		else
		{
			return json_decode($response);
		}
	}

	/**
	 * get back a percentage of translated strings
	 *
	 * @param	string $total		int	total strings in project/language
	 * @param	string $translated	int	translated strings in project/language
	 * @param	string $dec		int	number of decimals
	 *
	 * @example	$translated = NTransifex::getPercTranslated($tx->total_segments, $tx->translated_segments);
	 * @example	echo 'Language Statistics for de_DE: ' .  . ' from ' .  . ' translated';
	 */
	public function getPercTranslated($total, $translated, $dec = 0) {
		$raw_response = 100 / $total * $translated;
		$response = round($raw_response, $dec);

		return $response;
	}

	/**
	 * get back a colored bootstrap progress bar with language tag and percentage in full width
	 *
	 * @param	string $langTag	var	language string used on transifex.com (de_DE, da_DK, etc.)
	 * @param	string $perc		var	percentage state language is translated
	 *
	 */
	public function getProgressBar($langTag, $perc) {
		if($perc == 100)
		{
			$color = 'success';
		}
		else if ($perc < 100 && $perc >= 80)
		{
			$color = 'info';
		}
		else if ($perc < 80 && $perc >= 40)
		{
			$color = 'warning';
		}
		else
		{
			$color = 'danger';
		}

		$response = 'Translation: <strong>' . $langTag . '</strong>';
		$response .= '<span class="pull-right">' . $perc . '%</span>';
		$response .= '<div class="progress progress-' . $color . ' active">';
		$response .= '<div class="bar" style="width: ' . $perc . '%"></div>';
		$response .= '</div>';

		return $response;
	}
}


// TODO __CONSTRUCT the api connect, use functions for output informations