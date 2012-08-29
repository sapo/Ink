<?php

  /**
  * Invalid controller action error
  *
  * @version 1.0
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class InvalidInstanceError extends Error {
    
    /**
    * Name of the variable
    *
    * @var string
    */
    var $variable_name;
    
    /**
    * Value of the variable
    *
    * @var mixed
    */
    var $variable_value;
    
    /**
    * Expected classname
    *
    * @var string
    */
    var $expected_class;
  
    /**
    * Construct the InvalidInstanceError
    *
    * @param string $var_name
    * @param mixed $var_value
    * @param string $expected_class
    * @param string $message
    * @param boolean $is_fatal
    * @return InvalidInstanceError
    */
    function __construct($var_name, $var_value, $expected_class, $message = null, $is_fatal = true) {
      
      // Prepare message...
      if(is_null($message)) $message = "$$var_name is not valid $expected_class instance";
      
      // Inherit...
      parent::__construct($message, $is_fatal);
      
      // Set vars...
      $this->setVariableName($var_name);
      $this->setVariableValue($var_value);
      $this->setExpectedClass($expected_class);
      
    } // __construct
    
    /**
    * Return errors specific params...
    *
    * @access public
    * @param void
    * @return array
    */
    function getAdditionalParams() {
      return array(
        'variable name' => $this->getVariableName(),
        'variable value' => $this->getVariableValue(),
        'expected class' => $this->getExpectedClass()
      ); // array
    } // getAdditionalParams
    
    // -------------------------------------------------------
    // Getters and setters
    // -------------------------------------------------------
    
    /**
    * Get variable_name
    *
    * @access public
    * @param null
    * @return string
    */
    function getVariableName() {
      return $this->variable_name;
    } // getVariableName
    
    /**
    * Set variable_name value
    *
    * @access public
    * @param string $value
    * @return null
    */
    function setVariableName($value) {
      $this->variable_name = $value;
    } // setVariableName
    
    /**
    * Get variable_value
    *
    * @access public
    * @param null
    * @return mixed
    */
    function getVariableValue() {
      return $this->variable_value;
    } // getVariableValue
    
    /**
    * Set variable_value value
    *
    * @access public
    * @param mixed $value
    * @return null
    */
    function setVariableValue($value) {
      $this->variable_value = $value;
    } // setVariableValue
    
    /**
    * Get expected_class
    *
    * @access public
    * @param null
    * @return string
    */
    function getExpectedClass() {
      return $this->expected_class;
    } // getExpectedClass
    
    /**
    * Set expected_class value
    *
    * @access public
    * @param string $value
    * @return null
    */
    function setExpectedClass($value) {
      $this->expected_class = $value;
    } // setExpectedClass
  
  } // InvalidInstanceError

?>