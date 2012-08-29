<?php

  /**
  * Query error
  *
  * @version 1.0
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class DBQueryError extends Error {
  
    /**
    * SQL that broke
    *
    * @var string
    */
    var $sql;
    
    /**
    * Error number
    *
    * @var integer
    */
    var $error_number;
    
    /**
    * Error message
    *
    * @var string
    */
    var $error_message;
    
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
    * Construct the DBQueryError
    *
    * @param void
    * @return DBQueryError
    */
    function __construct($sql, $error_number, $error_message, $message = null) {
      if(is_null($message)) {
        $message = "Query failed with message '$error_message'";
      } // if
      
      $this->setSQL($sql);
      $this->setErrorNumber($error_number);
      $this->setErrorMessage($error_message);
      
      parent::__construct($message, true);
    } // __construct
    
    /**
    * Return errors specific params...
    *
    * @param void
    * @return array
    */
    function getAdditionalParams() {
      return array(
        'sql' => $this->getSQL(),
        'error number' => $this->getErrorNumber(),
        'error message' => $this->getErrorMessage()
      ); // array
    } // getAdditionalParams
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Get sql
    *
    * @param null
    * @return string
    */
    function getSQL() {
      return $this->sql;
    } // getSQL
    
    /**
    * Set sql value
    *
    * @param string $value
    * @return null
    */
    function setSQL($value) {
      $this->sql = $value;
    } // setSQL
    
    /**
    * Get error_number
    *
    * @param null
    * @return integer
    */
    function getErrorNumber() {
      return $this->error_number;
    } // getErrorNumber
    
    /**
    * Set error_number value
    *
    * @param integer $value
    * @return null
    */
    function setErrorNumber($value) {
      $this->error_number = $value;
    } // setErrorNumber
    
    /**
    * Get error_message
    *
    * @param null
    * @return string
    */
    function getErrorMessage() {
      return $this->error_message;
    } // getErrorMessage
    
    /**
    * Set error_message value
    *
    * @param string $value
    * @return null
    */
    function setErrorMessage($value) {
      $this->error_message = $value;
    } // setErrorMessage
  
  } // DBQueryError

?>