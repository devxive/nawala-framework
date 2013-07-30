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
 * Version information class for the Nawala Framework package.
 *
 */
final class NFWVersion
{
    /** @var  string  Product name. */
    public $PRODUCT = 'Nawala Framework';

    /** @var  string  Release version. */
    public $RELEASE = '6.0';

    /** @var  string  Maintenance version. */
    public $DEV_LEVEL = '0';

    /** @var  string  Development status. */
    public $DEV_STATUS = 'Beta';

    /** @var  string  Build number. */
    public $BUILD = '0';

    /** @var  string  Code name. */
    public $CODENAME = 'Konglang';

    /** @var  string  Release date. */
    public $RELDATE = '2013-07-29';

    /** @var  string  Release time. */
    public $RELTIME = '10:00';

    /** @var  string  Release timezone. */
    public $RELTZ = 'CET';

    /** @var  string  Copyright Notice. */
    public $COPYRIGHT = 'Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.';

    /** @var  string  Link text. */
    public $URL = 'The <a href="http://devxive.com/nfw">Nawala Framework</a> is Free Software released under the GNU General Public License.';


    /**
     * Compares two a "PHP standardized" version number against the current Projectfork version.
     *
     * @param     string    $minimum    The minimum version of Projectfork which is compatible.
     *
     * @return    bool                  True if the version is compatible.
     */
    public function isCompatible($minimum)
    {
        return version_compare(NFWVERSION, $minimum, 'ge');
    }


    /**
     * Gets a "PHP standardized" version string for the current Projectfork.
     *
     * @return    string    Version string.
     */
    public function getShortVersion()
    {
        return $this->RELEASE . '.' . $this->DEV_LEVEL;
    }


    /**
     * Gets a version string for the current Projectfork with all release information.
     *
     * @return    string    Complete version string.
     */
    public function getLongVersion()
    {
        return $this->PRODUCT . ' ' . $this->RELEASE . '.' . $this->DEV_LEVEL . ' '
                . $this->DEV_STATUS . ' [ ' . $this->CODENAME . ' ] ' . $this->RELDATE . ' '
                . $this->RELTIME . ' ' . $this->RELTZ;
    }
}