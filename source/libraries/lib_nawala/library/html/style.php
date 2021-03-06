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

jimport('joomla.application.component.helper');

/**
 * Utility class for Projectfork style sheets
 *
 */
abstract class PFhtmlStyle
{
    /**
     * Array containing information for loaded files
     *
     * @var    array    $loaded
     */
    protected static $loaded = array();


    /**
     * Method to load bootstrap CSS
     *
     * @return    void
     */
    public static function bootstrap()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        $params = JComponentHelper::getParams('com_projectfork');

        // Load only if doc type is HTML
        if (JFactory::getDocument()->getType() == 'html' && $params->get('bootstrap_css') != '0') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerProjectforkStyleBootstrap');
        }

        self::$loaded[__METHOD__] = true;
    }


    /**
     * Method to load Projectfork CSS
     *
     * @return    void
     */
    public static function projectfork()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        $params = JComponentHelper::getParams('com_projectfork');

        // Load only if doc type is HTML
        if (JFactory::getDocument()->getType() == 'html' && $params->get('projectfork_css', '1') == '1') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerProjectforkStyleCore');
        }

        self::$loaded[__METHOD__] = true;
    }
}


/**
 * Stupid but necessary way of adding bootstrap CSS to the document head.
 * This function is called by the "onCompileHead" system event and makes sure that the CSS is only added if not already found
 *
 */
function triggerProjectforkStyleBootstrap()
{
    $params = JComponentHelper::getParams('com_projectfork');

    $load = $params->get('bootstrap_css');

    // Auto-load
    if ($load == '') {
        $css = (array) array_keys(JFactory::getDocument()->_styleSheets);
        $string  = implode('', $css);

        $isis  = stripos($string, 'isis/css/template.css');
        $proto = stripos($string, 'protostar/css/template.css');
        $strap = stripos($string, 'bootstrap');
        $j3000 = version_compare(JVERSION, '3.0.0', 'ge');

        if ($j3000 || $isis !== false || $proto !== false || $strap !== false) {
            return;
        }

        JHtml::_('stylesheet', 'com_projectfork/bootstrap/component.css', false, true, false, false, false);
    }

    // Force load
    if ($load == '1') {
        JHtml::_('stylesheet', 'com_projectfork/bootstrap/component.css', false, true, false, false, false);
    }
}


/**
 * Stupid but necessary way of adding projectfork CSS to the document head.
 * This function is called by the "onCompileHead" system event and makes sure that the CSS is loader after bootstrap
 *
 */
function triggerProjectforkStyleCore()
{
    JHtml::_('stylesheet', 'com_projectfork/projectfork/styles.css', false, true, false, false, false);
}
