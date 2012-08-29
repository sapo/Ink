<?php

  /**
  * JSON error
  * 
  * This error is throw when Services_JSON fails to encode specific value to 
  * JSON string
  *
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class JSONEncodeError extends Error {
    
    /**
    * Variable that cannot be encoded
    *
    * @var mixed
    */
    var $var;
    
    /**
    * This error is fatal
    *
    * @var boolean
    */
    var $is_fatal = true;
  
    /**
    * Constructor
    *
    * @param mixed $var
    * @param string $message
    * @return JSONEncodeError
    */
    function __construct($var, $message = null) {
      if(empty($message)) {
        $message = 'Failed to encode specified value to JSON string';
      } // if
      
      $this->setVar($var);
      parent::__construct($message);
    } // __construct
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Get var
    *
    * @param null
    * @return mixed
    */
    function getVar() {
      return $this->var;
    } // getVar
    
    /**
    * Set var value
    *
    * @param mixed $value
    * @return null
    */
    function setVar($value) {
      $this->var = $value;
    } // setVar
  
  } // JSONEncodeError

?>