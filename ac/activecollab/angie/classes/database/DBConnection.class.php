<?php

  /**
   * Database connection
   *
   * @package angie.library.database
   */
  class DBConnection {
  
    /**
     * Database link
     *
     * @var resource
     */
    var $link;
    
    /**
     * Transaction level
     *
     * @var integer
     */
    var $transaction_level = 0;
    
    /**
     * Count of executed queries
     *
     * @var integer
     */
    var $query_counter = 0;
    
    /**
     * Connect to database
     *
     * @param string $host Hostname
     * @param string $user Username
     * @param string $pass Password
     * @param string $database Database name
     * @param boolean $persist Create persistant database connection
     * @param string $charset
     * @return boolean
     * @throws DBConnectError
     */
    function connect($host, $user, $pass, $database, $persist = true, $charset = null) {
      $link = $persist ? mysql_pconnect($host, $user, $pass) : mysql_connect($host, $user, $pass);
       
      if(!is_resource($link)) {
        use_error('DBConnectError');
        return new DBConnectError($host, $user, $pass, $database);
      } // if
      
      if(!mysql_select_db($database, $link)) {
        use_error('DBConnectError');
        return new DBConnectError($host, $user, $pass, $database);
      } // if
      
      $this->link = $link;
      
      if($charset) {
        $this->execute('SET NAMES ?', array($charset));
        if(version_compare(mysql_get_server_info($link), '5.0.0', '>=')) {
          $this->execute('SET SESSION character_set_database = ?', array($charset));
        } // if
      } // if
      
      return true;
    } // connect
    
    /**
     * Disconnect
     *
     * @param void
     * @return boolean
     */
    function disconnect() {
      if(is_resource($this->link)) {
    	  mysql_close($this->link);
    	  $this->link = null;
      } // if
    } // disconnect
    
    /**
     * Execute sql
     *
     * @param string $sql
     * @param array $arguments
     * @return mixed
     * @throws DBQueryError
     */
    function execute($sql, $arguments = null) {
      return $this->prepareAndExecute($sql, $arguments);
    } // execute
    
    /**
     * Execute query and return first row. If there is no first row NULL is returned
     *
     * @param string $sql
     * @param array $arguments
     * @return array
     * @throws DBQueryError
     */
    function execute_one($sql, $arguments = null) {
      return $this->prepareAndExecute($sql, $arguments, true);
    } // execute_one
    
    /**
     * Execute SQL and return all rows. If there is no rows NULL is returned
     *
     * @param string $sql
     * @param array $arguments
     * @return array
     * @throws DBQueryError
     */
    function execute_all($sql, $arguments = null) {
      return $this->prepareAndExecute($sql, $arguments);
    } // execute_all
    
    /**
     * Return number of affected rows
     *
     * @param void
     * @return integer
     */
    function affectedRows() {
      return mysql_affected_rows($this->link);
    } // affectedRows
    
    /**
     * Return last insert ID
     *
     * @param void
     * @return integer
     */
    function lastInsertId() {
      return mysql_insert_id($this->link);
    } // lastInsertId
    
    /**
     * Begin transaction
     *
     * @param void
     * @return boolean
     */
    function beginWork() {
      if($this->transaction_level == 0) {
        $execute = $this->execute('BEGIN WORK');
        if(is_error($execute)) {
          return $execute;
        } // if
      } // if
      $this->transaction_level++;
      
      return true;
    } // beginWork
    
    /**
     * Commit transaction
     *
     * @param void
     * @return boolean
     */
    function commit() {
      if($this->transaction_level) {
        $this->transaction_level--;
        if($this->transaction_level == 0) {
          $execute = $this->execute('COMMIT');
          if(is_error($execute)) {
            return $execute;
          } // if
        } // if
      } // if
      
      return true;
    } // commit
    
    /**
     * Rollback transaction
     *
     * @param void
     * @return boolean
     */
    function rollback() {
      if($this->transaction_level) {
        $this->transaction_level = 0;
        $execute = $this->execute('ROLLBACK');
        if(is_error($execute)) {
          return $execute;
        } // if
      } // if
      
      return true;
    } // rollback
    
    /**
     * Prepare SQL and execute it...
     *
     * @param string $sql
     * @param arary $arguments
     * @param boolean $only_first
     * @return DBResult
     * @throws DBQueryError
     */
    function prepareAndExecute($sql, $arguments = null, $only_first = false) {
      if(is_array($arguments)) {
        $sql = $this->prepareSQL($sql, $arguments);
      } // if
      
      $query_result = mysql_query($sql, $this->link);
      
      if(DEBUG >= DEBUG_DEVELOPMENT && !str_starts_with(strtolower($sql), 'explain')) {
        log_message($sql, LOG_LEVEL_INFO, 'sql');
      } // if
      
      if($query_result === false) {
        if(DEBUG >= DEBUG_PRODUCTION) {
          log_message('SQL error. MySQL said: ' . mysql_error($this->link) . "\n($sql)", LOG_LEVEL_ERROR, 'sql');
        } // if
        
        use_error('DBQueryError');
        
        $error_message = mysql_error($this->link);
        $error_number = mysql_errno($this->link);
        
        // Non-transactional tables not rolled back!
        if($error_number == 1196) {
          log_message('Non-transactional tables not rolled back!', LOG_LEVEL_WARNING, 'sql');
          return true;
          
        // Server gone away
        } elseif($error_number == 2006 || $error_number == 2013) {
          if(defined('DB_AUTO_RECONNECT') && DB_AUTO_RECONNECT > 0) {
            
            $executed = false;
            for($i = 1; $i <= DB_AUTO_RECONNECT; $i++) {
              if(DEBUG >= DEBUG_PRODUCTION) {
                log_message("Trying to reconnect, attempt #$i", LOG_LEVEL_INFO, 'sql');
              } // if
              
              $connect = $this->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PERSIST, DB_CHARSET);
              if($connect && !is_error($connect)) {
                $query_result = mysql_query($sql, $this->link);
                if($query_result !== false) {
                  $executed = true;
                  break; // end of the loop
                } // if
              } // if
            } // for
            
            // Not executed after reconnects?
            if(!$executed) {
              return new DBQueryError($sql, $error_number, $error_message);
            } // if
            
          } else {
            return new DBQueryError($sql, $error_number, $error_message);
          } // if
          
        // Other
        } else {
          return new DBQueryError($sql, $error_number, $error_message);
        } // if
      } // if
      
      $this->query_counter++;
      
      // Simple result
      if($query_result === true) {
        return true;
      } // if
      
      if($only_first) {
        $return = mysql_fetch_assoc($query_result);
        if(!is_array($return)) {
          $return = null;
        } // if
      } else {
        $return = array();
        while($row = mysql_fetch_assoc($query_result)) {
          $return[] = $row;
        } // while
        if(!count($return)) {
          $return = null;
        } // if
      } // if
      
      mysql_free_result($query_result);
      return $return;
    } // prepareAndExecute
    
    /**
     * Prepare SQL (replace ? with data from $arguments array)
     *
     * @param string $sql SQL that need to be prepared
     * @param array $arguments Array of SQL arguments...
     * @return string
     */
    function prepareSQL($sql, $arguments = null) {
      if(is_foreachable($arguments)) {
        $offset = 0;
        foreach($arguments as $argument) {
          $question_mark_pos = strpos_utf($sql, '?', $offset);
          if($question_mark_pos !== false) {
            $escaped = $this->escapeString($argument);
            $escaped_len = strlen_utf($escaped);
            
            $sql = substr_utf($sql, 0, $question_mark_pos) . $escaped . substr_utf($sql, $question_mark_pos + 1, strlen_utf($sql));
            
            $offset = $question_mark_pos + $escaped_len;
          } // if
        } // foreach
      } // if
      
      return $sql;
    } // prepareSQL
    
    /**
     * Escape string before we use it in query...
     *
     * @param string $unescaped String that need to be escaped
     * @return string
     */
    function escapeString($unescaped) {
      $is_array = false;
      if(is_array($unescaped)) {
        $is_array = true;
      } // if
      
      if(instance_of($unescaped, 'DateTimeValue')) {
        return "'" . mysql_real_escape_string(date(DATETIME_MYSQL, $unescaped->getTimestamp()), $this->link) . "'";
      } elseif(instance_of($unescaped, 'DateValue')) {
        return "'" . mysql_real_escape_string(date(DATE_MYSQL, $unescaped->getTimestamp()), $this->link) . "'";
      } elseif(is_float($unescaped)) {
        return str_replace(',', '.', (float) $unescaped); // replace , with . for locales where comma is used by the system (German for example)
      } elseif(is_bool($unescaped)) {
        return $unescaped ? "'1'" : "'0'";
      } elseif(is_null($unescaped)) {
        return 'NULL';
      } elseif(is_array($unescaped)) {
        $escaped = array();
        foreach($unescaped as $v) {
          $escaped[] = $this->escapeString($v);
        } // foreach
        
        return implode(', ', $escaped);
      } else {
        return "'" . mysql_real_escape_string($unescaped, $this->link) . "'";
      } // if
    } // escapeString
    
    /**
     * Return SQL log
     *
     * @param void
     * @return array
     */
    function getSQLLog() {
      return $this->sql_log;
    } // getSQLLog
    
    // ---------------------------------------------------
    //  Util methods
    // ---------------------------------------------------
    
    /**
     * Return array of tables from selected database
     *
     * If there is no tables in database empty array is returned
     * 
     * @param void
     * @return array
     */
    function listTables($prefix = null) {
      $tables = array();
      
      if($prefix) {
        $rows = $this->execute_all("SHOW TABLES LIKE '$prefix%'");
      } else {
        $rows = $this->execute_all('SHOW TABLES');
      } // if
      
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $table_name = array_shift($row);
          
          if($prefix) {
            if(str_starts_with($table_name, $prefix)) {
              $tables[] = $table_name;
            } // if
          } else {
            $tables[] = $table_name;
          } // if
        } // foreach
      } // if
      
      return $tables;
    } // listTables
    
    /**
     * List names of the table
     *
     * @param string $table_name
     * @return array
     */
    function listTableFields($table_name) {
      $result = array();
      
      if($table_name) {
        $rows = $this->execute_all("DESCRIBE `$table_name`");
        if(is_foreachable($rows)) {
          foreach($rows as $row) {
            $result[] = $row['Field'];
          } // foreach
        } // if
      } // if
      
      return $result;
    } // listTableFields
    
    /**
     * Drop all tables from database
     *
     * @param void
     * @return boolean
     */
    function clearDatabase() {
      $tables = $this->listTables();
      if(is_foreachable($tables)) {
        return $this->execute('DROP TABLES ' . implode(', ', $tables));
      } else {
        return true; // it's already clear
      } // if
    } // clearDatabase
    
    /**
     * Returns true if server we are connected to supports collation
     *
     * @param void
     * @return boolean
     */
    function supportsCollation() {
      return version_compare(mysql_get_server_info($this->link), '4.1') >= 0;
    } // supportsCollation
    
    /**
     * Return connection instance
     *
     * @param void
     * @return DBConnection
     */
    function &instance() {
      static $instance;
      if(!instance_of($instance, 'DBConnection')) {
        $instance = new DBConnection();
      } // if
      return $instance;
    } // instance
    
    /**
     * Do a mysql dump of specified tables, if tables are not provided
     * it will dump all tables in current database
     *
     * @param array $tables
     * @param string $destination_file
     * @param boolean $dump_structure
     * @param boolean $dump_data
     * @return boolean
     */
    function dumpTables($tables = null, $output_file, $dump_structure = true, $dump_data = true) {
      // maximum query length
      $max_query_length=838860;
      $output_stream = '';
           
      if (!is_foreachable($tables)) {
        $tables = $this->listTables();
      } // if
            
      if (is_foreachable($tables)) {
        foreach ($tables as $table_name) {
          // dump_structure
        	if ($dump_structure) {
        	  $create_table = $this->execute_one("SHOW CREATE TABLE $table_name");
        	  $output_stream.= "DROP TABLE IF EXISTS `$table_name`;\n".$create_table['Create Table'].";\n\n";
        	} // if
        	
        	// dump_data
        	if ($dump_data) {
            $output_stream.= "/*!40000 ALTER TABLE `$table_name` DISABLE KEYS */;\n";

            $query_result = mysql_query("SELECT * FROM `$table_name`", $this->link);
            
            $inserted_values = '';
            while ($row = mysql_fetch_row($query_result)) {
              $values = '';
              
              foreach ($row as $field) {
                $values.= $values ? ',' : '';
                $values.= (is_null($field) ? "NULL" : ("'".mysql_real_escape_string($field, $this->link)."'"));
              } // foreach
              
              $inserted_values.= ($inserted_values ? ',' : '');
            	$inserted_values.='('.$values.')';
            	
              if (strlen($inserted_values) > $max_query_length) {
                $output_stream.= "INSERT INTO `$table_name` VALUES $inserted_values;\n";
                $inserted_values = '';
              } // if
            } // while
            
            if ($inserted_values) {
              $output_stream.= "INSERT INTO `$table_name` VALUES $inserted_values;\n";
            } // if
            $output_stream.= "/*!40000 ALTER TABLE `$table_name` ENABLE KEYS */;\n";
        	} // if
        } // foreach
        
        return file_put_contents($output_file, $output_stream) ? true : new Error('Cannot create output file: '.$output_file);
      } else {
        return new Error('Database has no tables');
      } // if
    } // dumpTables
    
    /**
     * Import sql file into database
     *
     * @param string $sql_file
     * @param string $database
     * @return boolean
     */
    function import($sql_file, $database=null) {
      if (!is_file($sql_file)) {
        return new Error('SQL File Not found');
      } // if
      
      if ($database) {
        $select_db_result = mysql_select_db($database, $this->link);
        if (!$select_db_result) {
          return new Error('Could not select database: '.$database);
        } // if
      } // if
      
      $sql = file($sql_file);
      if (is_foreachable($sql)) {
        $this->beginWork();
        $query = "";
        foreach($sql as $sql_line){
          if(trim($sql_line) != "" && strpos(trim($sql_line), "--") !== 0){
            $query .= $sql_line;
  
            if(preg_match("/;[\040]*\$/", $sql_line)){
              $result = mysql_query($query, $this->link);
              if (!$result) {
                $this->rollback();
                return new Error(mysql_error($this->link));
              } // if
              $query = "";
            } // if
          } // if
        } // foreach
        $this->commit();
        return true;
      } else {
        return new Error('SQL file is empty');
      } // if
    } // import
    
    /**
     * Show database info
     * 
     * @param void
     * @return void
     */
    function getVersion() {
      return mysql_get_server_info($this->link);
    } // info
    
    /**
     * Get MySQL variable value
     *
     * @param string $variable_name
     * @return mixed
     */
    function getVariableValue($variable_name) {
      $variables = $this->execute('SHOW VARIABLES');
      $variables_assoc = array();
      
      foreach ($variables as $variable) {
      	$variables_assoc[$variable['Variable_name']] = $variable['Value'];
      } // foreach
      
      return array_var($variables_assoc, $variable_name);
    } // getVariableValue
    
  } // DBConnection

?>