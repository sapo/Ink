<?php

  /**
  * Not implemented error
  *
  * This error is introduced to simulate abstract classes when they need to 
  * throw some kind of exception when method that should be overriden in 
  * subclass is not overriden.
  * 
  * NotImplementedError is fatal - it will break script execution when 
  * enountered
  * 
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class NotImplementedError extends Error {
    
    /**
    * Name of the class
    *
    * @var string
    */
    var $class;
    
    /**
    * Name of the method that is not implemented
    *
    * @var string
    */
    var $method;
    
    /**
    * This message is fatal and it will break script exectuion when encountered
    *
    * @var boolean
    */
    var $is_fatal = true;
  
    /**
    * Constructor
    *
    * @param string $class
    * @param string $method
    * @param string $message
    * @return NotImplementedError
    */
    function __construct($class, $method, $message = null) {
      if($message === null) {
        $message = "You are trying to use a method that is not implemented - $class::$method()";
      } // if
      
      $this->class = $class;
      $this->method = $method;
      
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
        'class' => $this->class,
        'method' => $this->method,
      ); // array
    } // getAdditionalParams
  
  } // NotImplementedError

?>