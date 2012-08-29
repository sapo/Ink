<?php

  /**
   * Init database library
   *
   * Load resources and define helper methods for database library. Currently 
   * this library supports only MySQL database through old MySQL extension (no 
   * MySQLi or PDO)
   * 
   * @package angie.library.database
   */

  define('DB_LIB_PATH', ANGIE_PATH . '/classes/database');
  
  include_once DB_LIB_PATH . '/DBConnection.class.php';
  include_once DB_LIB_PATH . '/DataObject.class.php';
  include_once DB_LIB_PATH . '/DataManager.class.php';
  
  /**
  * Connect to database
  *
  * @param string $host
  * @param string $user
  * @param string $pass
  * @param string $database
  * @param boolean $persist
  * @param string $charset
  * @return boolean
  * @throws DBConnectError
  */
  function db_connect($host, $user, $pass, $database, $persist = true, $charset = null) {
    $connection =& DBConnection::instance();
    return $connection->connect($host, $user, $pass, $database, $persist, $charset);
  } // db_connect
  
  /**
  * Execute query and return result
  *
  * @param string $sql
  * @return DBResult
  * @throws mixed
  */
  function db_execute($sql) {
    $arguments = func_get_args();
    array_shift($arguments);
    $arguments = count($arguments) ? $arguments : null;
    
    $connection =& DBConnection::instance();
    return $connection->execute($sql, $arguments);
  } // db_execute
  
  /**
  * Execute query and return first row from result
  *
  * @param string $sql
  * @return array
  * @throws DBQueryError
  */
  function db_execute_one($sql) {
    $arguments = func_get_args();
    array_shift($arguments);
    $arguments = count($arguments) ? $arguments : null;
    
    $connection =& DBConnection::instance();
    return $connection->execute_one($sql, $arguments);
  } // db_execute_one
  
  /**
  * Execute query and return all rows
  *
  * @param string $sql
  * @return array
  * @throws DBQueryError
  */
  function db_execute_all($sql) {
    $arguments = func_get_args();
    array_shift($arguments);
    $arguments = count($arguments) ? $arguments : null;
    
    $connection =& DBConnection::instance();
    return $connection->execute_all($sql, $arguments);
  } // db_execute_all
  
  /**
  * Return insert ID
  *
  * @param void
  * @return integer
  */
  function db_last_insert_id() {
    $connection =& DBConnection::instance();
    return $connection->lastInsertId();
  } // db_last_insert_id
  
  /**
  * Return number of affected rows
  *
  * @param void
  * @return integer
  */
  function db_affected_rows() {
    $connection =& DBConnection::instance();
    return $connection->affectedRows();
  } // db_affected_rows
  
  /**
  * Escape value
  *
  * @param mixed $value
  * @return string
  */
  function db_escape($value) {
    $connection =& DBConnection::instance();
    return $connection->escapeString($value);
  } // db_escape
  
  /**
  * Prepare string
  *
  * @param string $sql
  * @param array $arguments
  * @return string
  */
  function db_prepare_string($sql, $arguments) {
    $connection =& DBConnection::instance();
    return $connection->prepareSQL($sql, $arguments);
  } // db_prepare_string
  
  /**
  * Start transaction
  *
  * @param void
  * @return null
  */
  function db_begin_work() {
    $connection =& DBConnection::instance();
    return $connection->beginWork();
  } // db_begin_work
  
  /**
  * Commit transaction
  *
  * @param void
  * @return null
  */
  function db_commit() {
    $connection =& DBConnection::instance();
    return $connection->commit();
  } // db_commit
  
  /**
  * Rollback transaction
  *
  * @param void
  * @return null
  */
  function db_rollback() {
    $connection =& DBConnection::instance();
    return $connection->rollback();
  } // db_rollback
  
  /**
  * List all tables
  *
  * @param string $sql
  * @return DBResult
  * @throws mixed
  */
  function db_list_tables($prefix='') {
    $connection =& DBConnection::instance();
    return $connection->listTables($prefix);
  } // db_execute
  
  /**
   * Dump selected tables
   *
   * @param array $tables
   * @param string $output_file
   * @param boolean $dump_structure
   * @param boolean $dump_data
   * @return boolean
   */
  function db_dump_tables($tables=null, $output_file, $dump_structure=true, $dump_data=true) {
    $connection=& DBConnection::instance();
    return $connection->dumpTables($tables, $output_file, $dump_structure, $dump_structure);
  } // db_dump_tables
  
  /**
   * Import SQL file
   *
   * @param string $sql_file
   * @return boolean
   */
  function db_import($sql_file, $database=null) {
    $connection=& DBConnection::instance();
    return $connection->import($sql_file, $database);
  } // db_import
  
  /**
   * Show database information
   *
   * @param void
   * @return array
   */
  function db_version() {
    $connection=& DBConnection::instance();
    return $connection->getVersion();
  } // db_info
  
  /**
   * Get MySQL server variable value
   *
   * @param string $variable_name
   * @return mixed
   */
  function db_get_variable_value($variable_name) {
    $connection = & DBConnection::instance();
    return $connection->getVariableValue($variable_name);
  } // db_get_variable_value

?>