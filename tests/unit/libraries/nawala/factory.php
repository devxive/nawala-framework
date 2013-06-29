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
 * Nawala Framework Factory class
 *
 * @package  Nawala.Framework
 * @since    13.6
 */
abstract class NFactory
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
	 * Method to extract a key
	 *
	 * @param   string  $key  The name of helper method to load, (prefix).(class).function
	 *                        prefix and class are optional and can be used to load custom html helpers.
	 *
	 * @return  array  Contains lowercase key, prefix, file, function.
	 *
	 * @since   11.1
	 */
	protected static function extract($key)
	{
		$key = preg_replace('#[^A-Z0-9_\.]#i', '', $key);

		// Check to see whether we need to load a helper file
		$parts = explode('.', $key);

		$prefix = (count($parts) == 3 ? array_shift($parts) : 'JHtml');
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
	 * @since   11.1
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
	 * Compute the files to be included
	 *
	 * @param   string   $folder          folder name to search into (images, css, js, ...)
	 * @param   string   $file            path to file
	 * @param   boolean  $relative        path to file is relative to /media folder  (and searches in template)
	 * @param   boolean  $detect_browser  detect browser to include specific browser files
	 * @param   boolean  $detect_debug    detect debug to include compressed files if debug is on
	 *
	 * @return  array    files to be included
	 *
	 * @see     JBrowser
	 * @since   11.1
	 */
	protected static function includeRelativeFiles($folder, $file, $relative, $detect_browser, $detect_debug)
	{
		// If http is present in filename
		if (strpos($file, 'http') === 0)
		{
			$includes = array($file);
		}
		else
		{
			// Extract extension and strip the file
			$strip		= JFile::stripExt($file);
			$ext		= JFile::getExt($file);

			// Prepare array of files
			$includes = array();

			// Detect browser and compute potential files
			if ($detect_browser)
			{
				$navigator = JBrowser::getInstance();
				$browser = $navigator->getBrowser();
				$major = $navigator->getMajor();
				$minor = $navigator->getMinor();

				// Try to include files named filename.ext, filename_browser.ext, filename_browser_major.ext, filename_browser_major_minor.ext
				// where major and minor are the browser version names
				$potential = array($strip, $strip . '_' . $browser,  $strip . '_' . $browser . '_' . $major,
					$strip . '_' . $browser . '_' . $major . '_' . $minor);
			}
			else
			{
				$potential = array($strip);
			}

			// If relative search in template directory or media directory
			if ($relative)
			{
				// Get the template
				$app = JFactory::getApplication();
				$template = $app->getTemplate();

				// For each potential files
				foreach ($potential as $strip)
				{
					$files = array();

					// Detect debug mode
					if ($detect_debug && JFactory::getConfig()->get('debug'))
					{
						/*
						 * Detect if we received a file in the format name.min.ext
						 * If so, strip the .min part out, otherwise append -uncompressed
						 */
						if (strrpos($strip, '.min', '-4'))
						{
							$position = strrpos($strip, '.min', '-4');
							$filename = str_replace('.min', '.', $strip, $position);
							$files[]  = $filename . $ext;
						}
						else
						{
							$files[] = $strip . '-uncompressed.' . $ext;
						}
					}
					$files[] = $strip . '.' . $ext;

					/*
					 * Loop on 1 or 2 files and break on first found.
					 * Add the content of the MD5SUM file located in the same folder to url to ensure cache browser refresh
					 * This MD5SUM file must represent the signature of the folder content
					 */
					foreach ($files as $file)
					{
						// If the file is in the template folder
						$path = JPATH_THEMES . "/$template/$folder/$file";

						if (file_exists($path))
						{
							$md5 = dirname($path) . '/MD5SUM';
							$includes[] = JURI::base(true) . "/templates/$template/$folder/$file" .
								(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');
							break;
						}
						else
						{
							// If the file contains any /: it can be in an media extension subfolder
							if (strpos($file, '/'))
							{
								// Divide the file extracting the extension as the first part before /
								list($extension, $file) = explode('/', $file, 2);

								// If the file yet contains any /: it can be a plugin
								if (strpos($file, '/'))
								{
									// Divide the file extracting the element as the first part before /
									list($element, $file) = explode('/', $file, 2);

									// Try to deal with plugins group in the media folder
									$path = JPATH_ROOT . "/media/$extension/$element/$folder/$file";

									if (file_exists($path))
									{
										$md5 = dirname($path) . '/MD5SUM';
										$includes[] = JURI::root(true) . "/media/$extension/$element/$folder/$file" .
											(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');
										break;
									}

									// Try to deal with classical file in a a media subfolder called element
									$path = JPATH_ROOT . "/media/$extension/$folder/$element/$file";

									if (file_exists($path))
									{
										$md5 = dirname($path) . '/MD5SUM';
										$includes[] = JURI::root(true) . "/media/$extension/$folder/$element/$file" .
											(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');
										break;
									}

									// Try to deal with system files in the template folder
									$path = JPATH_THEMES . "/$template/$folder/system/$element/$file";

									if (file_exists($path))
									{
										$md5 = dirname($path) . '/MD5SUM';
										$includes[] = JURI::root(true) . "/templates/$template/$folder/system/$element/$file" .
											(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');
										break;
									}

									// Try to deal with system files in the media folder
									$path = JPATH_ROOT . "/media/system/$folder/$element/$file";

									if (file_exists($path))
									{
										$md5 = dirname($path) . '/MD5SUM';
										$includes[] = JURI::root(true) . "/media/system/$folder/$element/$file" .
											(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');
										break;
									}
								}
								else
								{
									// Try to deals in the extension media folder
									$path = JPATH_ROOT . "/media/$extension/$folder/$file";

									if (file_exists($path))
									{
										$md5 = dirname($path) . '/MD5SUM';
										$includes[] = JURI::root(true) . "/media/$extension/$folder/$file" .
											(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');
										break;
									}

									// Try to deal with system files in the template folder
									$path = JPATH_THEMES . "/$template/$folder/system/$file";

									if (file_exists($path))
									{
										$md5 = dirname($path) . '/MD5SUM';
										$includes[] = JURI::root(true) . "/templates/$template/$folder/system/$file" .
											(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');
										break;
									}

									// Try to deal with system files in the media folder
									$path = JPATH_ROOT . "/media/system/$folder/$file";

									if (file_exists($path))
									{
										$md5 = dirname($path) . '/MD5SUM';
										$includes[] = JURI::root(true) . "/media/system/$folder/$file" .
											(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');
										break;
									}
								}
							}
							// Try to deal with system files in the media folder
							else
							{
								$path = JPATH_ROOT . "/media/system/$folder/$file";

								if (file_exists($path))
								{
									$md5 = dirname($path) . '/MD5SUM';
									$includes[] = JURI::root(true) . "/media/system/$folder/$file" .
											(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');
									break;
								}
							}
						}
					}
				}
			}
			// If not relative and http is not present in filename
			else
			{
				foreach ($potential as $strip)
				{
					$files = array();

					// Detect debug mode
					if ($detect_debug && JFactory::getConfig()->get('debug'))
					{
						/*
						 * Detect if we received a file in the format name.min.ext
						 * If so, strip the .min part out, otherwise append -uncompressed
						 */
						if (strrpos($strip, '.min', '-4'))
						{
							$position = strrpos($strip, '.min', '-4');
							$filename = str_replace('.min', '.', $strip, $position);
							$files[]  = $filename . $ext;
						}
						else
						{
							$files[] = $strip . '-uncompressed.' . $ext;
						}
					}
					$files[] = $strip . '.' . $ext;

					/*
					 * Loop on 1 or 2 files and break on first found.
					 * Add the content of the MD5SUM file located in the same folder to url to ensure cache browser refresh
					 * This MD5SUM file must represent the signature of the folder content
					 */
					foreach ($files as $file)
					{
						$path = JPATH_ROOT . "/$file";

						if (file_exists($path))
						{
							$md5 = dirname($path) . '/MD5SUM';
							$includes[] = JURI::root(true) . "/$file" .
								(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');
							break;
						}
					}
				}
			}
		}
		return $includes;
	}

	/**
	 * Set format related options.
	 *
	 * Updates the formatOptions array with all valid values in the passed
	 * array. See {@see JHtml::$formatOptions} for details.
	 *
	 * @param   array  $options  Option key/value pairs.
	 *
	 * @return  void
	 *
	 * @since   11.1
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

	/**
	 * Add a directory where JHtml should search for helpers. You may
	 * either pass a string or an array of directories.
	 *
	 * @param   string  $path  A path to search.
	 *
	 * @return  array  An array with directory elements
	 *
	 * @since   11.1
	 */
	public static function addIncludePath($path = '')
	{
		// Force path to array
		settype($path, 'array');

		// Loop through the path directories
		foreach ($path as $dir)
		{
			if (!empty($dir) && !in_array($dir, self::$includePaths))
			{
				jimport('joomla.filesystem.path');
				array_unshift(self::$includePaths, JPath::clean($dir));
			}
		}

		return self::$includePaths;
	}
}
