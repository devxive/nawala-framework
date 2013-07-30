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

abstract class NFWObjectHelper
{
    /**
     * Method to get the property changes between two item objects
     *
     * @param     object    $old        The old object
     * @param     object    $new        The new/updated object
     * @param     array     $props      The property/comparison method pairs
     *
     * @return    array     $changes    The changed property values
     */
    public static function getDiff($old, $new, $props)
    {
        $changes = array();

        if ($old instanceof JTable) {
            $old_props = $old->getProperties(true);
        }
        else {
            $old_props = get_object_vars($old);
        }

        if ($new instanceof JTable) {
            $new_props = $new->getProperties(true);
        }
        else {
            $new_props = get_object_vars($new);
        }

        foreach($props AS $prop)
        {
            if (!is_array($prop)) {
                $prop = array($prop, 'NE');
            }

            if (count($prop) != 2) continue;

            list($name, $cmp) = $prop;

            if (!array_key_exists($name, $new_props) || !array_key_exists($name, $old_props)) {
                continue;
            }

            switch (strtoupper($cmp))
            {
                case 'NE-SQLDATE':
                    // Not equal, not sql null date
                    if ($new->$name != $old->$name && $new->$name != JFactory::getDbo()->getNullDate()) {
                        $changes[$name] = $new->$name;
                    }
                    break;

                case 'NE':
                default:
                    // Default, not equal
                    if ($new->$name != $old->$name) {
                        $changes[$name] = $new->$name;
                    }
                    break;
            }
        }

        return $changes;
    }


    public static function toContentItem(&$item)
    {
        static $content;

        if (is_object($item)) {
            if (!$content) {
                $content_table = JTable::getInstance('Content');
                $content = $content_table->getProperties(true);
            }

            $item_props    = get_object_vars($item);
            $content_props = array_keys($content);

            foreach ($content_props AS $prop)
            {
                if (!array_key_exists($prop, $item_props)) {
                    $item->$prop = $content[$prop];
                }
            }
        }
    }
}