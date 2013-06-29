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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.path');

class NLoader
{
	/**
	 * Option values related to the generation of HTML output. Recognized
	 * options are:
	 *     fmtDepth, integer. The current indent depth.
	 *     fmtEol, string. The end of line string, default is linefeed.
	 *     fmtIndent, string. The string to use for indentation, default is
	 *     tab.
	 *
	 * @var    array
	 * @since  11.1
	 */
	public static $formatOptions = array('format.depth' => 0, 'format.eol' => "\n", 'format.indent' => "\t");

	/**
	 * An array to hold included paths
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $includePaths = array();

	/**
	 * An array to hold method references
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $registry = array();

	/**
	 * Loads a class from specified directories.
	 *
	 * @param string $filepath  Split the class name and path to look for ( dot notation ).
	 * @param string $config    'once' = include_once, else include
	 *
	 * @return void
	 */
	public static function import($filePath, $config = null)
	{
		static $paths, $base;

		if (!isset($paths)) {
			$paths = array();
		}

		if (!isset($base)) {
			$base = realpath(dirname(__FILE__) . '/..');
		}

		if (!isset($paths[$filePath])) {
			$parts            = explode('.', $filePath);
			$classname        = array_pop($parts);
			$path             = str_replace('.', '/', $filePath);
			if ($config == 'once') {
				$rs        = include_once($base . '/' . $path . '.php');
			} else {
				$rs        = include($base . '/' . $path . '.php');
			}
			$paths[$filePath] = $rs;
		}
		return $paths[$filePath];
	}

	/**
	 * Method to extract a key
	 *
	 * @param   string  $key  The name of helper method to load, (prefix).(class).function
	 *                        prefix and class are optional and can be used to load custom html helpers.
	 *
	 * @return  array  Contains lowercase key, prefix, file, function.
	 *
	 * @since   13.6
	 */
	protected static function extract($key)
	{
		$key = preg_replace('#[^A-Z0-9_\.]#i', '', $key);

		// Check to see whether we need to load a helper file
		$parts = explode('.', $key);

		$prefix = (count($parts) == 3 ? array_shift($parts) : 'NLoader');
		$file = (count($parts) == 2 ? array_shift($parts) : '');
		$func = array_shift($parts);

		return array(strtolower($prefix . '.' . $file . '.' . $func), $prefix, $file, $func);
	}

	/**
	 * Class loader method
	 *
	 * Additional arguments may be supplied and are passed to the sub-class.
	 * Additional include paths are also able to be specified for third-party use
	 *
	 * @param   string  $key  The name of helper method to load, (prefix).(class).function
	 *                        prefix and class are optional and can be used to load custom
	 *                        html helpers.
	 *
	 * @return  mixed  JHtml::call($function, $args) or False on error
	 *
	 * @since   13.6
	 * @throws  InvalidArgumentException
	 */
	public static function _($key)
	{
		list($key, $prefix, $file, $func) = self::extract($key);

		if (array_key_exists($key, self::$registry))
		{
			$function = self::$registry[$key];
			$args = func_get_args();

			// Remove function name from arguments
			array_shift($args);

			return self::call($function, $args);
		}

		$className = $prefix . ucfirst($file);

		if (!class_exists($className))
		{
			$path = JPath::find(self::$includePaths, strtolower($file) . '.php');

			if ($path)
			{
				require_once $path;

				if (!class_exists($className))
				{
					throw new InvalidArgumentException(sprintf('%s not found.', $className), 500);
				}
			}
			else
			{
				throw new InvalidArgumentException(sprintf('%s %s not found.', $prefix, $file), 500);
			}
		}

		$toCall = array($className, $func);

		if (is_callable($toCall))
		{
			self::register($key, $toCall);
			$args = func_get_args();

			// Remove function name from arguments
			array_shift($args);

			return self::call($toCall, $args);
		}
		else
		{
			throw new InvalidArgumentException(sprintf('%s::%s not found.', $className, $func), 500);
		}
	}

	/**
	 * Registers a function to be called with a specific key
	 *
	 * @param   string  $key       The name of the key
	 * @param   string  $function  Function or method
	 *
	 * @return  boolean  True if the function is callable
	 *
	 * @since   11.1
	 */
	public static function register($key, $function)
	{
		list($key) = self::extract($key);

		if (is_callable($function))
		{
			self::$registry[$key] = $function;

			return true;
		}
		return false;
	}

	/**
	 * Removes a key for a method from registry.
	 *
	 * @param   string  $key  The name of the key
	 *
	 * @return  boolean  True if a set key is unset
	 *
	 * @since   11.1
	 */
	public static function unregister($key)
	{
		list($key) = self::extract($key);

		if (isset(self::$registry[$key]))
		{
			unset(self::$registry[$key]);

			return true;
		}

		return false;
	}

	/**
	 * Test if the key is registered.
	 *
	 * @param   string  $key  The name of the key
	 *
	 * @return  boolean  True if the key is registered.
	 *
	 * @since   11.1
	 */
	public static function isRegistered($key)
	{
		list($key) = self::extract($key);

		return isset(self::$registry[$key]);
	}

	/**
	 * Function caller method
	 *
	 * @param   callable  $function  Function or method to call
	 * @param   array     $args      Arguments to be passed to function
	 *
	 * @return  mixed   Function result or false on error.
	 *
	 * @see     http://php.net/manual/en/function.call-user-func-array.php
	 * @since   11.1
	 * @throws  InvalidArgumentException
	 */
	protected static function call($function, $args)
	{
		if (!is_callable($function))
		{
			throw new InvalidArgumentException('Function not supported', 500);
		}

		// PHP 5.3 workaround
		$temp = array();

		foreach ($args as &$arg)
		{
			$temp[] = &$arg;
		}
		return call_user_func_array($function, $temp);
	}

	/**
	 * Set format related options.
	 *
	 * Updates the formatOptions array with all valid values in the passed
	 * array. See {@see NLoader::$formatOptions} for details.
	 *
	 * @param   array  $options  Option key/value pairs.
	 *
	 * @return  void
	 *
	 * @since   0.0
	 */
	public static function setFormatOptions($options)
	{
		foreach ($options as $key => $val)
		{
			if (isset(self::$formatOptions[$key]))
			{
				self::$formatOptions[$key] = $val;
			}
		}
	}
}