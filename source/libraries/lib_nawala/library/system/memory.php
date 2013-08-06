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
 * @since		6.0
 */

defined('_NFW_FRAMEWORK') or die();

class NFWSystemMemory
{
	/*
	 * Method to check the memory size
	 *
	 * @param     int       $minMemory    Type in the minimum memory size in byte that is required to check again the system. Standard ~ 6291456Byte (6MB)
	 * @param     string    $sizeOut      Recalculate to given size
	 *
	 * @return    mixed                   True if check pass, else calculated difference of the memory size
	 *
	 * @usage                             $mem = new pkg_projectforkMemory();
	 *                                    $check = $mem->check(10485760, 'KB');
	 *                                    if ($check !== true) {
	 *                                        $msg = 'Not enough memory available: Missing ' . $check;
	 *                                    }
	 */
	public function check($minMemory = 6291456, $sizeOut = 'B')
	{
		$usage     = $this->getUsage();
		$available = $this->getAvailable();

		if (empty($usage) || empty($available)) {
			return true;
		}

		$remaining = $available - $usage;

		$needed = $minMemory;

		$size = $remaining - $needed;

		if ( $size <= 0 ) {
			switch($sizeOut)
			{
				case 'GB':
					$result = round($size / 1024 / 1024 / 1024, 1) . 'GB';
					break;

				case 'MB':
					$result = round($size / 1024 / 1024, 2) . 'MB';
					break;

				case 'KB':
					$result = round($size / 1024, 3) . 'KB';
					break;

				case 'B':
					$result = $size . 'B';
					break;
			}

			return $result;
		}

		return true;
	}


	protected function getUsage()
	{
		return JProfiler::getInstance('Application')->getMemory();
	}


	protected function getAvailable()
	{
		$mem = ini_get('memory_limit');

		if (empty($mem)) {
			return false;
		}

		$mem   = trim($mem);
		$short = strtolower($mem[strlen($mem)-1]);

		switch($short)
		{
			case 'g':
				$mem *= 1024;

			case 'm':
				$mem *= 1024;

			case 'k':
				$mem *= 1024;
				break;
		}

		return $mem;
	}
}