<?php

  /**
    * Routing error
    *
    * Routing error is thrown when we fail to match request string with array of 
    * defined routes
    */
  class RoutingError extends Error {
  
    /**
    * Request string that was not matched
    *
    * @var string
    */
    var $request_string;
    
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
    * Constructor
    *
    * @param string $request_string
    * @param string $message
    * @return Angie_Router_Error_Match
    */
    function __construct($request_string, $message = null) {
      if(is_null($message)) {
        $message = "String '$request_string' does not match any of mapped routes";
      } // if
      
      $this->setRequestString($request_string);
      
      parent::__construct($message);
    } // __construct
    
    /**
    * Return additional error params
    *
    * @param void
    * @return array
    */
    function getAdditionalParams() {
      return array(
        'request string' => $this->getRequestString(),
      ); // array
    } // getAdditionalParams
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Get request_string
    *
    * @param null
    * @return string
    */
    function getRequestString() {
      return $this->request_string;
    } // getRequestString
    
    /**
    * Set request_string value
    *
    * @param string $value
    * @return null
    */
    function setRequestString($value) {
      $this->request_string = $value;
    } // setRequestString
  
  } // RoutingError

?>