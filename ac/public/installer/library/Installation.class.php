<?php

  /**
   * Installation class
   */
  class Installation {
    
    /**
     * Installation path
     *
     * @var string
     */
    var $root_path = '';
    
    /**
     * Absolute path to the instance
     *
     * @var string
     */
    var $instance_path = '';
    
    /**
     * MySQL connection resource
     *
     * @var resource
     */
    var $database_connection;
    
    /**
     * Database host
     *
     * @var string
     */
    var $database_host;
    
    /**
     * Database username
     *
     * @var string
     */
    var $database_username;
    
    /**
     * Database password
     *
     * @var string
     */
    var $database_password;
    
    /**
     * Database name
     *
     * @var string
     */
    var $database_name;
    
    /**
     * Table prefix
     *
     * @var string
     */
    var $table_prefix = 'ac_';
    
    /**
     * Database supports transactions
     *
     * @var boolean
     */
    var $can_transact = true;
    
    /**
     * Defaukt Collation
     *
     * @var string
     */
    var $default_collation;
    
    /**
     * Defaukt Collation
     *
     * @var string
     */
    var $default_charset;
    
    /**
     * Absolute URL
     *
     * @var string
     */
    var $absolute_url;
    
    /**
     * Name of the public folder
     *
     * @var string
     */
    var $public_folder_name = 'public';
    
    /**
     * Owner company name
     *
     * @var string
     */
    var $company_name;
    
    /**
     * Admin user email
     *
     * @var string
     */
    var $email;
    
    /**
     * Admin user password
     *
     * @var string
     */
    var $password;
    
    /**
     * User accepeted the license
     *
     * @var boolean
     */
    var $license_accepeted = false;
    
    /**
     * Prepare and process config form
     *
     * @access public
     * @param void
     * @return boolean
     */
    function execute() {
      if(trim($this->absolute_url) == '') {
        return $this->breakExecution('Absolute URL is required');
      } // if
      
      if(trim($this->public_folder_name) == '') {
        return $this->breakExecution('Public folder name is required');
      } // if
      
      if(trim($this->company_name) == '') {
        return $this->breakExecution('Company name is required');
      } // if
      
      if(trim($this->email) == '') {
        return $this->breakExecution('Administrators email address is required');
      } else {
        if(!is_valid_email($this->email)) {
          return $this->breakExecution('Administrators email address is not valid');
        } // if
      } // if
      
      if(trim($this->password) == '') {
        return $this->breakExecution('Administrators password is required');
      } // if
      
      if(!$this->license_accepeted) {
        return $this->breakExecution('You need to accept activeCollab License Agreement in order to continue');
      } // if
      
      $tpl = new Template();
      
      if(str_ends_with($this->absolute_url, '/')) {
      	$absolute_url = substr($this->absolute_url, 0, strlen($this->absolute_url) - 1);
      } else {
        $absolute_url = $this->absolute_url;
      } // if
      
      $connected = false;
      if($this->database_connection = @mysql_connect($this->database_host, $this->database_username, $this->database_password)) {
        $connected = @mysql_select_db($this->database_name, $this->database_connection);
      } // if
      
      if($connected) {
        $this->printMessage('Database connection has been established successfully', STATUS_OK);
      } else {
        return $this->breakExecution('Failed to connect to database with data you provided', STATUS_ERROR);
      } // if
      
      // ---------------------------------------------------
      //  Check if we have InnoDB support
      // ---------------------------------------------------
      
      $this->can_transact = $this->haveInnoDbSupport();
      if($this->can_transact) {
        $this->printMessage('InnoDB storage engine is supported', STATUS_OK);
      } else {
        $this->printMessage('InnoDB storage engine is not supported (recommended)', STATUS_WARNING);
      } // if
      
      $constants = array(
        'ROOT' => $this->root_path,
        'PUBLIC_FOLDER_NAME' => $this->public_folder_name,
      
        'DB_HOST' => $this->database_host,
        'DB_USER' => $this->database_username,
        'DB_PASS' => $this->database_password,
        'DB_NAME' => $this->database_name,
        'DB_CAN_TRANSACT' => $this->can_transact,
        'TABLE_PREFIX' => $this->table_prefix,
        
        'ROOT_URL' => $absolute_url,
        'PATH_INFO_THROUGH_QUERY_STRING' => true,
        'FORCE_QUERY_STRING' => true,
        'LOCALIZATION_ENABLED' => false,
        'ADMIN_EMAIL' => $this->email,
        'DEBUG' => 1,
        'API_STATUS' => 1,
        'PROTECT_SCHEDULED_TASKS' => true,
      ); // array
      
      // Check MySQL version
      $mysql_version = mysql_get_server_info($this->database_connection);
      if($mysql_version && version_compare($mysql_version, '4.1', '>=')) {
        $this->default_collation = 'utf8_unicode_ci';
        $this->default_charset = 'utf8';
        
        $constants['DB_CHARSET'] = 'utf8';
        
        @mysql_query("SET NAMES 'utf8'", $this->database_connection);
        
        $tpl->assign('default_collation', 'collate ' . $this->default_collation);
        $tpl->assign('table_collation', 'COLLATE=' . $this->default_collation);
        $tpl->assign('default_charset', 'DEFAULT CHARSET=' . $this->default_charset);
      } else {
        $constants['DB_CHARSET'] = null;
        
        $tpl->assign('default_collation', '');
        $tpl->assign('table_collation', '');
        $tpl->assign('default_charset', '');
      } // if
      
      if($this->can_transact) {
        $tpl->assign('engine', 'ENGINE=InnoDB');
      } else {
        $tpl->assign('engine', 'ENGINE=MyISAM');
      } // if
      
      $tpl->assign('table_prefix', $this->table_prefix);
      
      $tpl->assign('company_name', $this->company_name);
      $tpl->assign('email', $this->email);
      $tpl->assign('password', $this->password);
      
      if($this->can_transact) {
        @mysql_query('BEGIN WORK', $this->database_connection);
      } // if
      
      // Database construction
      $total_queries = 0;
      $executed_queries = 0;
      if($this->executeMultipleQueries($tpl->fetch(get_template_path('mysql_schema.php')), $total_queries, $executed_queries)) {
        $this->printMessage("Tables created in '$this->database_name'. (Executed queries: $executed_queries)", STATUS_OK);
      } else {
        return $this->breakExecution('Failed to import database construction. MySQL said: ' . mysql_error($this->database_connection));
      } // if
      
      // Initial data
      $total_queries = 0;
      $executed_queries = 0;
      if($this->executeMultipleQueries($tpl->fetch(get_template_path('mysql_initial_data.php')), $total_queries, $executed_queries)) {
        $this->printMessage("Initial data imported into '$this->database_name'. (Executed queries: $executed_queries)", STATUS_OK);
      } else {
        return $this->breakExecution('Failed to import initial data. MySQL said: ' . mysql_error($this->database_connection));
      } // if
      
      // Randon ID-s
      $company_id = rand(1, 25);
      $user_id = rand(1, 25);
      
      $company_name = mysql_real_escape_string($this->company_name, $this->database_connection);
      $admin_email = mysql_real_escape_string($this->email, $this->database_connection);
      $admin_token = make_string(40);
      
      $insert_company_sql = sprintf('INSERT INTO `' . $this->table_prefix . "companies` (`id`, `name`, `is_owner`, `created_on`) VALUES ('%s', '%s', '1', NOW())", $company_id, $company_name);
      $insert_user_sql = sprintf('INSERT INTO `' . $this->table_prefix . "users` (`id`, `company_id`, `role_id`, `email`, `token`) VALUES ('%s', '%s', '1', '%s', '%s')", $user_id, $company_id, $admin_email, $admin_token);
      if(@mysql_query($insert_company_sql, $this->database_connection)) {
        $this->printMessage('Owner company created', STATUS_OK);
        if(@mysql_query($insert_user_sql, $this->database_connection)) {
          $this->printMessage('Administrator account created', STATUS_OK);
        } else {
          return $this->breakExecution('Failed to create administrators account. MySQL said: ' . mysql_error($this->database_connection));
        } // if
      } else {
        return $this->breakExecution('Failed to create owner company. MySQL said: ' . mysql_error($this->database_connection));
      } // if
      
      @mysql_query('UPDATE ' . $this->table_prefix . "assignment_filters SET created_by_id = '$user_id' WHERE created_by_id = '0'", $this->database_connection); // Update created_by_id for assignment filters
      @mysql_query("INSERT INTO " . $this->table_prefix . "update_history (version, created_on) VALUES ('2.3', NOW())", $this->database_connection); // Insert current version
      
      if($this->can_transact) {
        @mysql_query('COMMIT', $this->database_connection);
      } // if
      
      if($this->writeConfigFile($tpl, $constants)) {
        $this->printMessage('Configuration data has been successfully added to the configuration file', STATUS_OK);
      } else {
        return $this->breakExecution('Failed to write config data into config file');
      } // if
      
      // Update user password
      $digested_password = mysql_real_escape_string(sha1($this->password), $this->database_connection);
      @mysql_query('UPDATE ' . $this->table_prefix . "users SET password = '$digested_password' WHERE id = $user_id");
      
      return true;
    } // excute
    
    // ---------------------------------------------------
    //  Util methods
    // ---------------------------------------------------
    
    /**
     * Add error message to all messages and break the execution
     *
     * @access public
     * @param string $error_message Reason why we are breaking execution
     * @return boolean
     */
    function breakExecution($error_message) {
      $this->printMessage($error_message, STATUS_ERROR);
      if(is_resource($this->database_connection) && $this->can_transact) {
        @mysql_query('ROLLBACK', $this->database_connection);
      } // if
      return false;
    } // breakExecution
    
    /**
     * Write $constants in config file
     *
     * @access public
     * @param Template $tpl
     * @param array $constants
     * @return boolean
     */
    function writeConfigFile(&$tpl, $constants) {
      $tpl->assign('config_file_constants', $constants);
      return file_put_contents(INSTALLATION_PATH . '/config/config.php', $tpl->fetch(get_template_path('config_file.php')));
    } // writeConfigFile
    
    /**
     * This function will return true if server we are connected on has InnoDB support
     *
     * @param void
     * @return boolean
     */
    function haveInnoDbSupport() {
      if($result = mysql_query("SHOW VARIABLES LIKE 'have_innodb'", $this->database_connection)) {
        if($row = mysql_fetch_assoc($result)) {
          return strtolower($row['Value']) == 'yes';
        } // if
      } // if
      return false;
    } // haveInnoDBSupport
    
    /**
     * Execute multiple queries
     * 
     * This one is really quick and dirty because I want to finish this and catch
     * the bus. Need to be redone ASAP
     * 
     * This function returns true if all queries are executed successfully
     *
     * @access public
     * @todo Make a better implementation
     * @param string $sql
     * @param integer $total_queries Total number of queries in SQL
     * @param integer $executed_queries Total number of successfully executed queries
     * @return boolean
     */
    function executeMultipleQueries($sql, &$total_queries, &$executed_queries) {
      if(!trim($sql)) {
        $total_queries = 0;
        $executed_queries = 0;
        return true;
      } // if
      
      // Make it work on PHP 5.0.4
      $sql = str_replace(array("\r\n", "\r"), array("\n", "\n"), $sql);
      
      $queries = explode(";\n", $sql);
      if(!is_array($queries) || !count($queries)) {
        $total_queries = 0;
        $executed_queries = 0;
        return true;
      } // if
      
      $total_queries = count($queries);
      foreach($queries as $query) {
        if(trim($query)) {
          if(@mysql_query(trim($query))) {
            $executed_queries++;
          } else {
            $this->printMessage("SQL error: " . $query, STATUS_ERROR);
            return false;
          } // if
        } // if
      } // if
      
      return true;
    } // executeMultipleQueries
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Print message through output object
     *
     * @param string $message
     * @param boolean $status
     * @return null
     */
    function printMessage($message, $status) {
      print '<li class="' . $status . '"><span>' . $status . '</span> &mdash; ' . clean($message) . '</li>';
    } // printMessage
  
  } // Installation

?>