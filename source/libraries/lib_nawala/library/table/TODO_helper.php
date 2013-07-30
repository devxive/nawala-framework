<?php
/**
 * @project		XAP Project - Xive-Application-Platform
 * @subProject	Nawala Framework - A PHP and Javascript framework
 *
 * @package		XiveIRM.Library
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

defined('_JEXEC') or die();


abstract class PFTableHelper
{
    /**
     * Table instance cache
     *
     * @var    array
     */
    protected static $table_cache = array();

    /**
     * Table methods list cache
     *
     * @var    array
     */
    protected static $methods_cache = array();


    /**
     * Table context list cache
     *
     * @var    array
     */
    protected static $context_cache = array();



    public static function getInstance($context)
    {
        if (isset(self::$table_cache[$context])) {
            return self::$table_cache[$context];
        }

        return null;
    }


    public static function getMethods($context)
    {
        if (isset(self::$methods_cache[$context])) {
            return self::$methods_cache[$context];
        }

        return array();
    }


    public static function getContexts()
    {
        return self::$context_cache;
    }


    /**
     * Discovers the table classes in all Projectfork related components.
     * Stores an instance in $table_cache.
     *
     * @return    void
     */
    public static function discover()
    {
        static $loaded = false;

        if ($loaded) return;

        $coms = PFApplicationHelper::getComponents();

        foreach ($coms AS $com)
        {
            $path     = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $com->element . '/tables');
            $prefixes = array('PFtable', 'JTable', ucfirst(substr($com->element, 3)) . 'Table');

            if (!is_dir($path)) continue;

            $files = JFolder::files($path, '.php$');

            if (!count($files)) continue;

            // Discover the table class names with some guessing about the prefix
            foreach ($prefixes AS $prefix)
            {
                JLoader::discover($prefix, $path, false);

                foreach ($files AS $file)
                {
                    $name    = JFile::stripExt($file);
                    $class   = $prefix . $name;
                    $context = strtolower($com->element . '.' . $name);

                    if (class_exists($class)) {
                        // Class found, try to get an instance
                        $instance = JTable::getInstance($name, $prefix);

                        if (!$instance) continue;

                        self::$context_cache[] = $context;

                        self::$table_cache[$context]   = $instance;
                        self::$methods_cache[$context] = array();

                        $methods = get_class_methods($instance);

                        foreach ($methods AS $method)
                        {
                            self::$methods_cache[$context][] = strtolower($method);
                        }
                    }
                }
            }
        }

        $loaded = true;
    }
}