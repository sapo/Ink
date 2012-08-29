<?php

  /**
  * Invalid param error
  *
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class InvalidParamError extends Error {
  
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
    * Construct the InvalidParamError
    *
    * $is_fatal argument gives an opetion to developer to treat this error as an 
    * exception and stop script execution. It is useful when you need for script 
    * to break in order to prevent more severe errors
    * 
    * @access public
    * @param string $var_name Variable name
    * @param string $var_value Variable value that broke the code
    * @param string $message
    * @param boolean $is_fatal
    * @return InvalidParamError
    */
    function __construct($var_name, $var_value, $message = null, $is_fatal = false) {
      if(is_null($message)) {
        $message = "$$var_name is not valid param value";
      } // if
      
      $this->setVariableName($var_name);
      $this->setVariableValue($var_value);
      
      parent::__construct($message, $is_fatal);
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
        'variable value' => $this->getVariableValue()
      ); // array
    } // getAdditionalParams
    
    // -------------------------------------------------------
    // Getters and setters
    // -------------------------------------------------------
    
    /**
    * Get variable_name
    *
    * @param null
    * @return string
    */
    function getVariableName() {
      return $this->variable_name;
    } // getVariableName
    
    /**
    * Set variable_name value
    *
    * @param string $value
    * @return null
    */
    function setVariableName($value) {
      $this->variable_name = $value;
    } // setVariableName
    
    /**
    * Get variable_value
    *
    * @param null
    * @return mixed
    */
    function getVariableValue() {
      return $this->variable_value;
    } // getVariableValue
    
    /**
    * Set variable_value value
    *
    * @param mixed $value
    * @return null
    */
    function setVariableValue($value) {
      $this->variable_value = $value;
    } // setVariableValue
  
  } // InvalidParamError

?>