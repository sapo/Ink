<?php

  /**
  * Database connection error
  *
  * @version 1.0
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class DBConnectError extends Error {
    
    /**
    * Hostname
    *
    * @var string
    */
    var $host;
    
    /**
    * Username
    *
    * @var string
    */
    var $user;
    
    /**
    * Password
    *
    * @var string
    */
    var $pass;
    
    /**
    * Database name
    *
    * @var string
    */
    var $database;
    
    /**
    * Fatal error
    * 
    * On fatal error script stops execution and handle_fatal_error hook is 
    * called
    *
    * @var boolean
    */
    var $is_fatal = true;
  
    /**
    * Construct the DBConnectError
    *
    * @access public
    * @param string $dsn Connection string (URL)
    * @param string $message
    * @return DBConnectError
    */
    function __construct($host, $user, $pass, $database, $message = null) {
      if(is_null($message)) {
        $message = "Failed to connect to database";
      } // if
      
      $this->setHost($host);
      $this->setUser($user);
      $this->setPassword($pass);
      $this->setDatabase($database);
      
      parent::__construct($message);
    } // __construct
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Get host
    *
    * @param null
    * @return string
    */
    function getHost() {
      return $this->host;
    } // getHost
    
    /**
    * Set host value
    *
    * @param string $value
    * @return null
    */
    function setHost($value) {
      $this->host = $value;
    } // setHost
    
    /**
    * Get user
    *
    * @param null
    * @return string
    */
    function getUser() {
      return $this->user;
    } // getUser
    
    /**
    * Set user value
    *
    * @param string $value
    * @return null
    */
    function setUser($value) {
      $this->user = $value;
    } // setUser
    
    /**
    * Get pass
    *
    * @param null
    * @return string
    */
    function getPassword() {
      return $this->pass;
    } // getPassword
    
    /**
    * Set pass value
    *
    * @param string $value
    * @return null
    */
    function setPassword($value) {
      $this->pass = $value;
    } // setPassword
    
    
    /**
    * Get database
    *
    * @param null
    * @return string
    */
    function getDatabase() {
      return $this->database;
    } // getDatabase
    
    /**
    * Set database value
    *
    * @param string $value
    * @return null
    */
    function setDatabase($value) {
      $this->database = $value;
    } // setDatabase
  
  } // DBConnectError

?>