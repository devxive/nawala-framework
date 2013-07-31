<?php
/**
 * @project		XAP Project - Xive-Application-Platform
 * @subProject	Nawala Framework - A PHP and Javascript framework
 *
 * @package		NFW.Installer
 * @subPackage	Framework
 * @version		6.0
 *
 * @author		devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright		Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense	devXive Proprietary Use License (http://www.devxive.com/license)
 *
 * @since		5.0
 */

if (!class_exists('PlgSystemNawalaFrameworkinstallerInstallerScript')) {

	/**
	 *
	 */
	class PlgSystemNawalaFrameworkinstallerInstallerScript
	{
		/**
		 * @var array
		 */
		protected $packages = array();
		/**
		 * @var
		 */
		protected $sourcedir;
		/**
		 * @var
		 */
		protected $installerdir;
		/**
		 * @var
		 */
		protected $manifest;

		/**
		 * NFWInstaller
		 */
		protected $parent;

		/**
		 * @param $parent
		 */
		protected function setup($parent)
		{
			$this->parent       = $parent;
			$this->sourcedir    = $parent->getParent()->getPath('source');
			$this->manifest     = $parent->getParent()->getManifest();
			$this->installerdir = $this->sourcedir . '/' . 'installer';
		}

		/**
		 * @param $parent
		 *
		 * @return bool
		 */
		public function install($parent)
		{

			$this->cleanBogusError();

			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');


			$retval = true;
			$buffer = '';


			$buffer .= ob_get_clean();

			$run_installer = true;


			// Cycle through cogs and install each

			if ($run_installer) {
				if (count($this->manifest->cogs->children())) {
					if (!class_exists('NFWInstaller')) {
						require_once($this->installerdir . '/' . 'NFWInstaller.php');
					}

					foreach ($this->manifest->cogs->children() as $cog) {
						$folder_found = false;
						$folder = $this->sourcedir . '/' . trim($cog);

						jimport('joomla.installer.helper');
						if (is_dir($folder)) {
							// if its actually a directory then fill it up
							$package                = Array();
							$package['dir']         = $folder;
							$package['type']        = JInstallerHelper::detectType($folder);
							$package['installer']   = new NFWInstaller();
							$package['name']        = (string)$cog->name;
							$package['state']       = 'Success';
							$package['description'] = (string)$cog->description;
							$package['msg']         = '';
							$package['type']        = ucfirst((string)$cog['type']);

							$package['installer']->setCogInfo($cog);
							// add installer to static for possible rollback
							$this->packages[] = $package;
							if (!@$package['installer']->install($package['dir'])) {
								while ($error = JError::getError(true)) {
									$package['msg'] .= $error;
								}
								NFWInstallerEvents::addMessage($package, NFWInstallerEvents::STATUS_ERROR, $package['msg']);
								break;
							}
							if ($package['installer']->getInstallType() == 'install') {
								NFWInstallerEvents::addMessage($package, NFWInstallerEvents::STATUS_INSTALLED);
							} else {
								NFWInstallerEvents::addMessage($package, NFWInstallerEvents::STATUS_UPDATED);
							}
							if (is_file($folder . '/setup.php')) {
								// check if something to do after installation
								if (($loadsetup = require_once($folder . '/setup.php')) == true) {
									NFWInstallerEvents::addMessage($package, NFWInstallerEvents::STATUS_PREPARED, implode('<br>', $loadsetup));
								} else {
									NFWInstallerEvents::addMessage($package, NFWInstallerEvents::STATUS_ERROR, implode('<br>', $loadsetup));
								}
							}
						} else {
							$package                = Array();
							$package['dir']         = $folder;
							$package['name']        = (string)$cog->name;
							$package['state']       = 'Failed';
							$package['description'] = (string)$cog->description;
							$package['msg']         = '';
							$package['type']        = ucfirst((string)$cog['type']);
							NFWInstallerEvents::addMessage($package, NFWInstallerEvents::STATUS_ERROR, JText::_('JLIB_INSTALLER_ABORT_NOINSTALLPATH'));
							break;
						}
					}
				} else {
					$parent->getParent()->abort(JText::sprintf('JLIB_INSTALLER_ABORT_PACK_INSTALL_NO_FILES', JText::_('JLIB_INSTALLER_' . strtoupper($this->route))));
				}
			}
			return $retval;
		}

		/**
		 * @param $parent
		 */
		public function uninstall($parent)
		{

		}

		/**
		 * @param $parent
		 *
		 * @return bool
		 */
		public function update($parent)
		{
			return $this->install($parent);
		}

		/**
		 * @param $type
		 * @param $parent
		 *
		 * @return bool
		 */
		public function preflight($type, $parent)
		{
			$this->setup($parent);

			//Load Event Handler
			if (!class_exists('NFWInstallerEvents')) {
				$event_handler_file = $this->installerdir . '/NFWInstallerEvents.php';
				require_once($event_handler_file);
				$dispatcher = JDispatcher::getInstance();
				$plugin = new NFWInstallerEvents($dispatcher);
				$plugin->setTopInstaller($this->parent->getParent());
			}

			if (is_file(dirname(__FILE__) . '/requirements.php')) {
				// check to see if requierments are met
				if (($loaderrors = require_once(dirname(__FILE__) . '/requirements.php')) !== true) {
					$manifest = $parent->get('manifest');
					$package['name'] = (string)$manifest->description;
					NFWInstallerEvents::addMessage($package, NFWInstallerEvents::STATUS_ERROR, implode('<br />', $loaderrors));
					return false;
				}
			}

			// Set the core description from manifest for rendering at the bottom of the installer
			$coreDescription = (string)$parent->get('manifest')->coreDescription;
			NFWInstallerEvents::addCoreDescription($coreDescription);
		}

		/**
		 * @param $type
		 * @param $parent
		 */
		public function postflight($type, $parent)
		{
			if (is_file(dirname(__FILE__) . '/setup.php')) {
				// check if something to do after installation
				$manifest = $parent->get('manifest');
				$package['name'] = (string)$manifest->description;
				if (($loadsetup = require_once(dirname(__FILE__) . '/setup.php')) == true) {
					NFWInstallerEvents::addMessage($package, NFWInstallerEvents::STATUS_PREPARED, implode('<br>', $loadsetup));
				} else {
					NFWInstallerEvents::addMessage($package, NFWInstallerEvents::STATUS_ERROR, implode('<br>', $loadsetup));
				}
			}

			$conf = JFactory::getConfig();
			$conf->set('debug', false);
			$parent->getParent()->abort();
		}

		/**
		 * @param null $msg
		 * @param null $type
		 */
		public function abort($msg = null, $type = null)
		{
			if ($msg) {
				JError::raiseWarning(100, $msg);
			}
			foreach ($this->packages as $package) {
				$package['installer']->abort(null, $type);
			}
		}

		/**
		 *
		 */
		protected function cleanBogusError()
		{
			$errors = array();
			while (($error = JError::getError(true)) !== false) {
				if (!($error->get('code') == 1 && $error->get('level') == 2 && $error->get('message') == JText::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE'))) {
					$errors[] = $error;
				}
			}
			foreach ($errors as $error) {
				JError::addToStack($error);
			}

			$app               = new NFWInstallerJAdministratorWrapper(JFactory::getApplication());
			$enqueued_messages = $app->getMessageQueue();
			$other_messages    = array();
			if (!empty($enqueued_messages) && is_array($enqueued_messages)) {
				foreach ($enqueued_messages as $enqueued_message) {
					if (!($enqueued_message['message'] == JText::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE') && $enqueued_message['type']) == 'error') {
						$other_messages[] = $enqueued_message;
					}
				}
			}
			$app->setMessageQueue($other_messages);
		}
	}

	if (!class_exists('NFWInstallerJAdministratorWrapper')) {
		/**
		 *
		 */
		class NFWInstallerJAdministratorWrapper extends JAdministrator
		{
			/**
			 * @var JAdministrator
			 */
			protected $app;

			/**
			 * @param JAdministrator $app
			 */
			public function __construct(JAdministrator $app)
			{
				$this->app =& $app;
			}

			/**
			 * @return array
			 */
			public function getMessageQueue()
			{
				return $this->app->getMessageQueue();
			}


			/**
			 * @param $messages
			 */
			public function setMessageQueue($messages)
			{
				$this->app->_messageQueue = $messages;
			}

		}
	}
}