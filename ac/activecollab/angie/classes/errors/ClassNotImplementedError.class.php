<?php

  /**
  * Class not implemented error
  *
  * This error is thrown when we expect a certain class to be defined but we 
  * failed to find it. Optional parameter is location where we expect to find 
  * class definition
  * 
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class ClassNotImplementedError extends Error {
  
    /**
    * Class name
    *
    * @var string
    */
    var $class;
    
    /**
    * Expected location
    *
    * @var string
    */
    var $expected_location;
    
    /**
    * Constructor
    * 
    * $expected_location is provided only if expected location is known
    *
    * @param string $class
    * @param string $expected_location
    * @param string $message
    * @param boolean $is_fatal
    * @return ClassNotImplementedError
    */
    function __construct($class, $expected_location = null, $message = null, $is_fatal = true) {
      if($message === null) {
        $message = "Class '$class' is not implemented";
        if($expected_location) {
          $message .= ". Expected location: '$expected_location'";
        } // if
      } // if
      
      $this->setClass($class);
      $this->setExpectedLocation($expected_location);
      
      parent::__construct($message, $is_fatal);
    } // __construct
    
    /**
    * Return additional error params
    *
    * @param void
    * @return array
    */
    function getAdditionalParams() {
      return array(
        'class' => $this->getClass(),
        'expected_location' => $this->getExpectedLocation(),
      ); // array
    } // getAdditionalParams
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Get class
    *
    * @param null
    * @return string
    */
    function getClass() {
      return $this->class;
    } // getClass
    
    /**
    * Set class value
    *
    * @param string $value
    * @return null
    */
    function setClass($value) {
      $this->class = $value;
    } // setClass
    
    /**
    * Get expected_location
    *
    * @param null
    * @return string
    */
    function getExpectedLocation() {
      return $this->expected_location;
    } // getExpectedLocation
    
    /**
    * Set expected_location value
    *
    * @param string $value
    * @return null
    */
    function setExpectedLocation($value) {
      $this->expected_location = $value;
    } // setExpectedLocation
  
  } // ClassNotImplementedError

?>