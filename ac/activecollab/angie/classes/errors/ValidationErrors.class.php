<?php

  /**
  * Container of multiple validation errors
  *
  * @version 1.0
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class ValidationErrors extends Error {
    
    /**
    * Array of form errors
    *
    * @var array
    */
    var $errors = array();
  
    /**
    * Construct the FormErrors
    *
    * @param array $errors
    * @param string $message
    * @return FormErrors
    */
    function __construct($errors = null, $message = null) {
      if($message === null) {
        $message = 'Failed to validate model properties';
      } // if
      
      if(is_array($errors)) {
        foreach($errors as $k => $error) {
          $field = is_numeric($k) ? null : $k;
          if(is_array($error)) {
            foreach($error as $single_error) {
              $this->addError($single_error, $field);
            } // foreach
          } elseif($error) {
            $this->addError($error, $field);
          } // if
        } // if
      } // if
      
      parent::__construct($message);
    } // __construct
    
    /**
    * Return errors specific params...
    *
    * @param void
    * @return array
    */
    function getAdditionalParams() {
      return array(
        'errors' => $this->getErrors()
      ); // array
    } // getAdditionalParams
    
    /**
     * Describe error for XML/JSON export
     *
     * @param void
     * @return array
     */
    function describe() {
    	$result = array(
    	  'message' => $this->getMessage(),
    	  'field_errors' => array(),
    	);
    	foreach($this->getErrors() as $field => $messages) {
    	  foreach($messages as $message) {
    	    if($field == ANY_FIELD) {
    	      $result['field_errors'][] = $message;
    	    } else {
    	      $result['field_errors'][] = "$field: $message";
    	    } // if
    	  } // foreach
    	}  // if
    	
    	return $result;
    } // describe
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Return number of errors from specific form
    *
    * @param void
    * @return array
    */
    function getErrors() {
      return count($this->errors) ? $this->errors : null;
    } // getErrors
    
    /**
    * Return field errors
    *
    * @param string $field
    * @return array
    */
    function getFieldErrors($field) {
      return array_var($this->errors, $field);
    } // getFieldErrors
    
    /**
    * Returns true if there are error messages reported
    *
    * @param void
    * @return boolean
    */
    function hasErrors() {
      return (boolean) count($this->errors);
    } // hasErrors
    
    /**
    * Check if a specific field has reported errors
    *
    * @param string $field
    * @return boolean
    */
    function hasError($field) {
      return isset($this->errors[$field]) && count($this->errors[$field]);
    } // hasError
    
    /**
    * Add error to array
    *
    * @param string $error Error message
    * @param string $field
    * @return null
    */
    function addError($error, $field = '') {
      if(trim($field) == '') {
        $field = ANY_FIELD;
      } // if
      
      if(!is_array($this->errors)) {
        $this->errors[$field] = array();
      } // if
      
      $this->errors[$field][] = $error;
    } // addError
    
    /**
     * Returns error messages as string
     * 
     * @param void
     * @return string
     */
    function getErrorsAsString() {
      if (!$this->hasErrors()) {
        return null;
      } // if
      
      $this_errors = array();
      $errors = $this->getErrors();
      foreach ($errors as $error) {
      	$this_errors[] = implode(", ", $error);
      } // foreach
      return trim(implode(", ", $this_errors));
    } // getErrorMessagesAsString
  
  } // ValidationErrors

?>