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
 * Nawala Database Class
 * Global Support for Database procedures
 *
 * TODO: DEBUGGING         -- Means that the returned object has an additional error tree including the fields which were NOT inserted
 * TODO: OBJECTS           -- Support object with arrays to perform multi inserts at one call, where all single array is checked against the appropriate single methods
 * TODO: SANITY CHECKS     -- Force to override the sanity check. No check is done so we get an error if we call columns that doesn't exist.
 * TODO: check() method    -- Check for existing item in database. Should be integrated in save method
 */
abstract class NFWDatabase
{
	/*
	 * Stores the table name
	 */
	protected static $tableName;

	/*
	 * Stores the database object
	 */
	protected static $db;

	/*
	 * Stores the query object
	 */
	protected static $query;

	/*
	 * Stores the current user id
	 */
	protected static $userId;

	/*
	 * Stores the current user id
	 */
	protected static $currentTime;

	/**
	 * Initialise
	 *
	 */
	protected function init($table)
	{
		self::$tableName = $table;
		self::$db = JFactory::getDbo();
		self::$query = self::$db->getQuery(true);

		self::$userId = NFWUser::getId();
		self::$currentTime = NFWDate::getCurrent('MySQL');
	}


	/**
	 * Simple method to get data from database by an id. This method has an integrated sanity check: Only fields selected from database which were found. Unknown fields will be ignored.
	 *
	 * @param     string     $table     Name of the table where to insert data without prefix or #__
	 * @param     mixed      $data      Array with the datas we want to get
	 * @param     int        $id        The row id to select data from
	 * @param     string     $output    Define an output, standard = ARRAY ( ARRAY|OBJECT|JSON|XML_UTF-8 )
	 * @param     boolean    $debug     Debuggin options true|false
	 *
	 * @return    mixed                 Return data based on output with the results or false.
	 *                                  If debug is set:
	 *                                      Status = (boolean) true|false -- True if success else false
	 *                                      Code = (int) true|false       -- If true the inserted id, if false the error code is returned
	 *                                      Message                       -- Empty message
	 *                                      Debug                         -- Error message
	 *
	 * @since                           6.0
	 *
	 * @usage                           $data = array('last_name', 'first_name' ,'fieldDoesNotExistInDatabase');
	 *                                  $result = NFWDatabase::get('database_table', $data, 5); // From #__database_table
	 *
	 *                                  if( $result ) {
	 *                                      // The datas
	 *                                  } else {
	 *                                      // false and raiseErrorMessage
	 *                                  }
	 */
	public function get($table, $data = '*', $id = false, $output = 'ARRAY', $debug = false)
	{
		// Check for an id
		if (!$id || !(int) $id) {
			return false;
		}

		// Initialise variables.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get available columns from database based on $table
		$tableColumns = self::getTableObject($table);

		// Format the select clause
		if ( is_array($data) )
		{
			$columns = array();
			$dataFlip = array_flip($data);

			if ( isset($dataFlip['*']) ) {
				$select = '*';
			} else {
				foreach($tableColumns as $tableColumn)
				{
					if( isset($dataFlip[$tableColumn]) )
					{
						$columns[] = $tableColumn;
					}
				}

				$select = implode(',', $columns);
			}
		}
		else
		{
			$select = $data;
		}

		$query
			->select( $select )
			->from( $db->quoteName('#__' . $table) )
			->where( 'id = ' . $id );

		$db->setQuery($query);

		try {
			$dbResult = $db->loadObjectList();
		} catch (Exception $e) {
			if($debug) {
				$returnCode = (int)$e->getCode();
				$returnMessage = $e->getMessage();
				$dbResult = array('status' => (boolean) false, 'code' => (int) $returnCode, 'message' => '');
				$dbResult['debug'] = $returnMessage;
			} else {
				return false;
			}
		}

		// Format the dbResult and return
		if ( isset($dbResult[0]) ) {
			return self::formatOutput($dbResult[0], $output, $debug);
		} else {
			return self::formatOutput($dbResult, $output, $debug);
		}
	}


	/**
	 * Method to select data from database by conditions. This method has an integrated sanity check: Only fields selected from database which were found. Unknown fields will be ignored.
	 *
	 * @param     string     $table         Name of the table where to insert data without prefix or #__
	 * @param     mixed      $data          Array with the datas to select
	 * @param     array      $conditions    The row id to select data from
	 * @param     string     $output        Define an output, standard = ARRAY ( ARRAY|OBJECT|JSON|XML_UTF-8 )
	 * @param     boolean    $debug         Debuggin options true|false
	 *
	 * @return    mixed                     Return data based on output with the results or false.
	 *                                      If debug is set:
	 *                                          Status = (boolean) true|false -- True if success else false
	 *                                          Code = (int) true|false       -- If true the inserted id, if false the error code is returned
	 *                                          Message                       -- Empty message
	 *                                          Debug                         -- Error message
	 *
	 * @since                               6.0
	 *
	 * @usage                               $data = array('last_name', 'first_name' ,'fieldDoesNotExistInDatabase');
	 *                                      $result = NFWDatabase::get('database_table', $data, 5); // From #__database_table
	 *
	 *                                      if( $result ) {
	 *                                          // The datas
	 *                                      } else {
	 *                                          // false and raiseErrorMessage
	 *                                      }
	 */
	public function select($table, $data = '*', $conditions = false, $output = 'ARRAY', $debug = false)
	{
		// Initialise variables.
		self::init($table);

		// Build the where clause from $conditions array
		$fieldHelper = self::buildDataClause($conditions, 'CONDITIONS');

		// Format the select clause
		if ( is_array($data) )
		{
			$columns = array();
			$dataFlip = array_flip($data);

			if ( isset($dataFlip['*']) ) {
				$select = '*';
			} else {
				foreach($tableColumns as $tableColumn)
				{
					if( isset($dataFlip[$tableColumn]) )
					{
						$columns[] = $tableColumn;
					}
				}

				$select = implode(',', $columns);
			}
		}
		else
		{
			$select = $data;
		}

		self::$query
			->select( $select )
			->from( self::$db->quoteName('#__' . $table) )
			->where( $fieldHelper->conditions );

		self::$db->setQuery(self::$query);

		try {
			$result = self::$db->loadObjectList();
			if ( empty($result) ) {
				$result = array('status' => (boolean) false, 'code' => 0, 'message' => 'selected');
			}			
		} catch (Exception $e) {
			if($debug) {
				$result = array('status' => (boolean) false, 'code' => (int) $e->getCode(), 'message' => $e->getMessage());
			} else {
				$result = array('status' => (boolean) false, 'code' => (int) $e->getCode(), 'message' => $e->getMessage());
			}
		}

		// Format the result and return
		if ( isset($dbResult[0]) ) {
			return self::formatOutput($result[0], $output, $debug);
		} else {
			return self::formatOutput($result, $output, $debug);
		}
	}


	/**
	 * Method to insert data into the database. This method has an integrated sanity check: Only fields inserted into the database which were found. Unknown fields will be ignored.
	 *
	 * @param     string     $table     Name of the table where to insert data without prefix or #__
	 * @param     array      $data      Array with the datas
	 *
	 * @return    array                 Status = (boolean) true|false -- True if success else false
	 *                                  Id = (int)                    -- If true the inserted id, if false the error code is returned
	 *                                  Code = (int)                  -- If true integer 1, if false the error code is returned
	 *                                  Message                       -- Empty message if true or error message if false
	 *
	 * @since                           6.0
	 *
	 * @usage                           $data = array(
	 *                                      'last_name' => 'Doe',
	 *                                      'first_name' => 'John'
	 *                                      'fieldDoesNotExistInDatabase' => 'whatever'
	 *                                  );
	 *                                  $result = NFWDatabase::insert('database_table', $data); // From #__database_table
	 *
	 *                                  if($result['status']) {
	 *                                      $insertedId = $result['code']; // If success, the id from the inserted row will be returned, else false
	 *                                  } else {
	 *                                      // raiseErrorMessage....
	 *                                  }
	 */
	protected function insert($table, $data)
	{
		// Initialise variables.
		self::init($table);

		// Build the column/value clause
		$fieldHelper = self::buildDataClause($data, 'INSERT');

		self::$query
			->insert( self::$db->quoteName('#__' . $table) )
			->columns( self::$db->quoteName($fieldHelper->columns) )
			->values( implode(',', $fieldHelper->values) );

		self::$db->setQuery(self::$query);

		try {
			self::$db->execute();
			$result = array('status' => (boolean) true, 'id' => (int) self::$db->insertid(), 'message' => 'inserted');
		} catch (Exception $e) {
			$result = array('status' => (boolean) false, 'code' => (int) $e->getCode(), 'message' => $e->getMessage());
		}

		// Clear the query
		self::$db->clear();

		return $result;
	}


	/**
	 * Method to update data in database based on given id. This method has an integrated sanity check: Only fields updated in database which were found. Unknown fields will be ignored.
	 *
	 * @param     string     $table     Name of the table where to insert data without prefix or #__
	 * @param     array      $data      Array with the datas
	 * @param     int        $id        The row id to update
	 *
	 * @return    array                 Status = (boolean) true|false -- True if success else false
	 *                                  Id = (int)                    -- If true the inserted id, if false the error code is returned
	 *                                  Code = (int)                  -- If true integer 1, if false the error code is returned
	 *                                  Message                       -- Empty message if true or error message if false
	 *
	 * @since                           6.0
	 *
	 * @usage                           $data = array(
	 *                                      'last_name' => 'Monroe',
	 *                                      'first_name' => 'Marilyn'
	 *                                      'fieldDoesNotExistInDatabase' => 'whatever'
	 *                                  );
	 *                                  $result = NFWDatabase::update('database_table', $data, 5); // From #__database_table
	 *
	 *                                  if( $result ) {
	 *                                      // Update successfull
	 *                                  } else {
	 *                                      // Update failed => raiseErrorMessage....
	 *                                  }
	 */
	protected function update($table, $data, $id = false)
	{
		// Check for an id
		if (!$id || !(int) $id) {
			return false;
		}

		// Initialise variables.
		self::init($table);

		// Build the field clause
		$fieldHelper = self::buildDataClause($data, 'UPDATE');

		self::$query
			->update( self::$db->quoteName('#__' . $table) )
			->set( $fieldHelper->fields )
			->where( 'id = ' . (int) $id );

		self::$db->setQuery(self::$query);

		try {
			self::$db->execute();

			$result = array('status' => (boolean) true, 'id' => (int) $id, 'message' => 'updated');
		} catch (Exception $e) {
			$result = array('status' => (boolean) false, 'code' => (int) $e->getCode(), 'message' => $e->getMessage());
		}

		// Clear the query
		self::$db->clear();

		return $result;
	}


	/**
	 * Method to delete a row from database.
	 *
	 * @param     mixed      $table     Name of the table where to insert data without prefix or #__
	 * @param     mixed      $id        The row id to delete from database table
	 *
	 * @return    array                 Status = (boolean) true|false -- True if success else false
	 *                                  Id = (int)                    -- If true the inserted id, if false the error code is returned
	 *                                  Code = (int)                  -- If true integer 1, if false the error code is returned
	 *                                  Message                       -- Empty message if true or error message if false
	 *
	 * @since                           6.0
	 *
	 * @usage                           $result = NFWDatabase::delete('database_table', $id); // From #__database_table
	 *
	 *                                  if($result) {
	 *                                      // Delete row successful
	 *                                  } else {
	 *                                      // Delete row failed => raiseErrorMessage....
	 *                                  }
	 */
	protected function delete($table, $id, $output = 'ARRAY', $debug = false)
	{
		// Initialise variables.
		self::init($table);

		self::$query
			->delete( self::$db->quoteName('#__' . $table) )
			->where( 'id = ' . $id );

		self::$db->setQuery(self::$query);

		try {
			self::$db->execute();

			$result = array('status' => (boolean) true, 'id' => (int) $id, 'message' => 'deleted');
		} catch (Exception $e) {
			$result = array('status' => (boolean) false, 'code' => (int) $e->getCode(), 'message' => $e->getMessage());
		}

		// Clear the query
		self::$db->clear();

		return $result;
	}


	/**
	 * Method to save data into the database. This method has an integrated sanity check: Only fields inserted into the database which were found. Unknown fields will be ignored.
	 * It checks if database entries, based on the match-clause, exist and update those rows, else a new row will be inserted.
	 *
	 * @param     mixed      $table     Name of the table where to insert data without prefix or #__
	 * @param     mixed      $data      Array with the datas, or object with arrays of datas
	 * @param     string     $output    Define an output, standard = ARRAY ( ARRAY|OBJECT|JSON|XML_UTF-8 )
	 * @param     boolean    $debug     Debuggin options true|false
	 *
	 * @return    mixed                 Return object with the resulting table ids that are inserted or false
	 *                                  Status = (boolean) true|false -- True if success else false
	 *                                  Code = (int) true|false       -- If true the inserted id, if false the error code is returned
	 *                                  Message                       -- Empty message if true or false
	 *                                  Debug                         -- Error message if false and $debug is true
	 *
	 * @since                           6.0
	 *
	 * @usage save multiaction:         $data = array();
	 * Either a whole set of arrays     // John Doe will be created
	 * in an object or a single         $data[] = array(
	 * array                                'last_name' => 'Doe',
	 *                                      'first_name' => 'John'
	 *                                      'fieldDoesNotExistInDatabase' => 'whatever'
	 *                                  );
	 *                                  // First_name will be updated
	 *                                  $data[] = array(
	 *                                      'id' => 945,
	 *                                      'first_name' => 'Jenna'
	 *                                  );
	 *                                  // Row 13 will be deleted
	 *                                  $data[] = array(
	 *                                      'id' => 13,
	 *                                      'delete' => true,
	 *                                  );
	 *
	 *                                  $dataObject = (object) $data;
	 *
	 *                                  $result = NFWDatabase::save('database_table', $dataObject, 'OBJECT'); // From #__database_table
	 *
	 *                                  if($result['status']) {
	 *                                      $insertedId = $result['code']; // If success, the id from the inserted row will be returned, else false
	 *                                  } else {
	 *                                      // raiseErrorMessage....
	 *                                  }
	 */
	public function save($table, $data, $output = 'ARRAY', $debug = false)
	{
		// Initialise variables.
		self::init($table);

		$result = array();

		if ( is_object($data) ) {
			foreach( $data as $data ) {
				// Check for an id in the dataset to update
				if ( isset($data['id']) && (int) $data['id'] ) {
					if ( isset($data['delete']) && $data['delete'] == true ) {
						$result[] = self::delete($table, $data['id'], $output, $debug);
					} else {
						$result[] = self::update($table, $data, $data['id'], $output, $debug);
					}
				} else {
					$result[] = self::insert($table, $data, $output, $debug);
				}
			}

			// Convert to object to identify for XML_UTF-8
			if ( $output == 'XML_UTF-8' ) {
				$result = (object) $result;
			}
		} else {
			// Check for an id in the dataset to update
			if ( isset($data['id']) && (int) $data['id'] ) {
				if ( isset($data['delete']) && $data['delete'] == true ) {
					$result = self::delete($table, $data['id'], $output, $debug);
				} else {
					$result = self::update($table, $data, $data['id'], $output, $debug);
				}
			} else {
				$result = self::insert($table, $data, $output, $debug);
			}
		}

		// Perform the output
		return self::formatOutput($result, $output);
	}


	/**
	 * Method to save data into the database. This method has an integrated sanity check: Only fields inserted into the database which were found. Unknown fields will be ignored.
	 * It checks if database entries, based on the match-clause, exist and update those rows, else a new row will be inserted.
	 *
	 * @param     mixed      $table     Name of the table where to insert data without prefix or #__
	 * @param     mixed      $data      Array with the datas, or object with arrays of datas
	 * @param     string     $output    Define an output, standard = ARRAY ( ARRAY|OBJECT|JSON|XML_UTF-8 )
	 * @param     boolean    $debug     Debuggin options true|false
	 *
	 * @return    mixed                 Return object with the resulting table ids that are inserted or false
	 *                                  Status = (boolean) true|false -- True if success else false
	 *                                  Code = (int) true|false       -- If true the inserted id, if false the error code is returned
	 *                                  Message                       -- Empty message if true or false
	 *                                  Debug                         -- Error message if false and $debug is true
	 *
	 * @since                           6.0
	 *
	 * @usage                           $data = array();
	 *                                  $data[] = array(
	 *                                      'last_name' => 'Doe',
	 *                                      'first_name' => 'John'
	 *                                      'fieldDoesNotExistInDatabase' => 'whatever'
	 *                                  );
	 *                                  $data[] = array(
	 *                                      'last_name' => 'Martini',
	 *                                      'first_name' => 'Jenna'
	 *                                      'fieldAlsoNotExistInDB' => 'whatever'
	 *                                  );
	 *
	 *                                  $dataObject = (object) $data;
	 *
	 *                                  $result = NFWDatabase::save('database_table', $dataObject); // From #__database_table
	 *
	 *                                  if($result['status']) {
	 *                                      $insertedId = $result['code']; // If success, the id from the inserted row will be returned, else false
	 *                                  } else {
	 *                                      // raiseErrorMessage....
	 *                                  }
	 */
	public function check($table, $data, $output = 'ARRAY', $debug = false)
	{
		// Initialise variables.
		self::init($table);

		if ( is_object($data) ) {
			foreach( $data as $data ) {
				$result = self::insert($table, $data, $output, $debug);
			}
		} else {
			$result = self::insert($table, $data, $output, $debug);
		}

		return $result;
	}


	/*
	 * Method to build the columns/value clause
	 *
	 * @param    string    $method    The method for building the clause (INSERT|UPDATE)
	 */
	protected function buildDataClause($data, $method)
	{
		if ( !$data || !$method ) {
			return false;
		}

		// Get available columns from database based on $table
		$tableColumns = self::getTableObject(self::$tableName);

		$fieldHelper = new stdClass;

		switch($method)
		{
			case 'INSERT':
				$columns = array();
				$values = array();

				foreach($tableColumns as $tableColumn)
				{
					// Check and set this first to override later, if supported by table and if set in data
					if ( $tableColumn == 'created' ) {
						$columns[] = $tableColumn;
						$values[] = self::$db->quote( self::$currentTime );
					}

					// Check and set this first to override later, if supported by table and if set in data
					if ( $tableColumn == 'created_by' ) {
						$columns[] = $tableColumn;
						$values[] = self::$userId;
					}

					if( isset($data[$tableColumn]) )
					{
						$columns[] = $tableColumn;
						$values[] = self::$db->quote( $data[$tableColumn] );
					}
				}

				$fieldHelper->columns = $columns;
				$fieldHelper->values = $values;

				break;

			case 'UPDATE':
				$fields = array();

				foreach($tableColumns as $tableColumn)
				{
					// Check and set this first to override later, if supported by table and if set in data
					if ( $tableColumn == 'modified' ) {
						$fields[] = 'modified = ' . self::$db->quote( self::$currentTime );
					}

					// Check and set this first to override later, if supported by table and if set in data
					if ( $tableColumn == 'modified_by' ) {
						$fields[] = 'modified_by = ' . self::$userId;
					}

					if ( isset($data[$tableColumn]) )
					{
						$fields[] = $tableColumn . ' = ' . self::$db->quote( $data[$tableColumn] );
					}
				}

				$fieldHelper->fields = $fields;

				break;


			case 'CONDITIONS':
				// conditions clause
				$where = '';

				foreach($tableColumns as $tableColumn)
				{
					if ( isset($data[$tableColumn]) )
					{
						if ( count($data) >= 2 ) {
							$where .= $tableColumn . ' = ' . self::$db->quote( $data[$tableColumn] ) . ' AND ';
						} else {
							$where .= $tableColumn . ' = ' . self::$db->quote( $data[$tableColumn] );
						}
						unset($data[$tableColumn]);
					}
				}

				$fieldHelper->conditions = $where;

				break;
		}

		return $fieldHelper;
	}


	/*
	 * Method to format the output from the database results
	 * used by ::save()
	 */
	public function formatOutput($results, $output)
	{
		switch($output)
		{
			case 'ARRAY':
				$result = (array) $results;
				break;

			case 'OBJECT':
				$result = (object) $results;
				break;

			case 'JSON':
				$result = json_encode($results);
				break;

			case 'XML_UTF-8':
				$result = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
				if ( is_object($results) ) {
					$result .= '<results>' . "\n";
					foreach($results as $key => $value) {
						$result .= '	<result>' . "\n";
						$result .= '		<' . $key . '>' .  $value  . '</' . $key . '>' . "\n";
						$result .= '	</result>' . "\n";
					}
					$result .= '</results>' . "\n";
				} else {
					$result .= '	<result>' . "\n";
					foreach($results as $key => $value) {
						$result .= '		<' . $key . '>' .  $value  . '</' . $key . '>' . "\n";
					}
					$result .= '	</result>' . "\n";
				}
				break;
		}

		return $result;
	}


	/**
	 * Method to get a table/columns object from the current table
	 *
	 * @param     string     $table    Name of the table
	 * @param     boolean    $debug    Debuggin options true|false
	 *
	 * @return    object               Based on the $table either the complete database table and columns or the columns from given table
	 *
	 * @since                          6.0
	 */
	protected function getTableObject($table = false, $debug = null)
	{
		// Initialise variables.
		$config = JFactory::getConfig();
//		$session = JFactory::getSession();
		$dbPrefix = $config->get('dbprefix');
		$db = JFactory::getDbo();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$debug = (boolean) $config->get('debug');
		}

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get a rowlist of all tables
		$db->setQuery('SHOW TABLES');
		$results = $db->loadRowList();

		$tableObject = new stdClass;

		foreach($results as $result) {
			$rowName = str_replace($dbPrefix, '', $result[0]);
			$quer = 'SHOW COLUMNS FROM #__' . $rowName;
			$db->setQuery($quer);
			$columns = $db->loadObjectList();

			foreach($columns as $column) {
				$helper = $column->Field;
				$tableObject->$rowName->$helper = $helper;
			}
		}

		if ($table) {
			return $tableObject->$table;
		} else {
			return $tableObject;
		}
	}
}