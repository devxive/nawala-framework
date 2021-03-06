﻿<?php
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


jimport('joomla.database.tableasset');


/**
 * Project Label table
 *
 */
class PFTableLabel extends JTable
{
    /**
     * Constructor
     *
     * @param    database    &    $db    A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__pf_labels', 'id', $db);
    }


    /**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	public function delete($pk = null)
	{
		// Initialise variables.
		$k  = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// If no primary key is given, return false.
		if ($pk === null) {
			$e = new JException(JText::_('JLIB_DATABASE_ERROR_NULL_PRIMARY_KEY'));
			$this->setError($e);
			return false;
		}

		// Delete the row by primary key.
		$query = $this->_db->getQuery(true);

		$query->delete()
		      ->from($this->_tbl)
		      ->where($this->_tbl_key . ' = ' . $this->_db->quote($pk));

		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->execute()) {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_DELETE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

        // Delete the references
        $query->clear();
        $query->delete('#__pf_ref_labels')
              ->where('label_id = ' . $this->_db->quote($pk));

        $this->_db->setQuery($query);

        // Check for a database error.
		if (!$this->_db->execute()) {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_DELETE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		return true;
	}
}
