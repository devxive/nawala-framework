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

/**
 * Version information class for the Nawala Framework.
 */
final class NFramework
{
	// Product name.
	const NPRODUCT = 'Nawala Framework';

	// Release version.
	const NRELEASE = '13.6';

	// Maintenance version.
	const NMAINTENANCE = '0';

	// Development STATUS.
	const NSTATUS = 'Alpha';

	// Build number.
	const NBUILD = 0;

	// Code name.
	const NCODE_NAME = 'Bagong Simula';

	// Release date.
	const NRELEASE_DATE = '01-Jul-2013';

	// Release time.
	const NRELEASE_TIME = '00:00';

	// Release timezone.
	const NRELEASE_TIME_ZONE = 'GMT';

	// Copyright Notice.
	const NCOPYRIGHT = 'Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.';

	// Link text.
	const NLINK_TEXT = '<a href="http://devxive.com/nawala">The Nawala Framework</a> is Free Software released under the GNU General Public License.';

	/**
	 * Compares two a "PHP standardized" version number against the current Nawala Framework version.
	 *
	 * @param   string  $minimum  The minimum version of the Nawala Framework which is compatible.
	 *
	 * @return  boolean  True if the version is compatible.
	 *
	 * @see     http://www.php.net/version_compare
	 */
	public static function isCompatible($minimum)
	{
		return (version_compare(self::getShortVersion(), $minimum, 'eq') == 1);
	}

	/**
	 * Gets a "PHP standardized" version string for the current Nawala Framework.
	 *
	 * @return  string  Version string.
	 */
	public static function getShortVersion()
	{
		return self::NRELEASE . '.' . self::NMAINTENANCE;
	}

	/**
	 * Gets a version string for the current Nawala Framework with all release information.
	 *
	 * @return  string  Complete version string.
	 */
	public static function getLongVersion()
	{
		return self::NPRODUCT . ' ' . self::NRELEASE . '.' . self::NMAINTENANCE . ' ' . self::NSTATUS . ' [ ' . self::NCODE_NAME . ' ] '
			. self::NRELEASE_DATE . ' ' . self::NRELEASE_TIME . ' ' . self::NRELEASE_TIME_ZONE;
	}
}
