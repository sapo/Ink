<?php

  /**
   * Error collector class
   * 
   * Error collector is place where all errors reported by system can be found. 
   * It also handles fatal errors. Only errors that inherit Error class will be 
   * handled and logged.
   *
   * @author Ilija Studen <ilija.studen@gmail.com>
   */
  class ErrorCollector extends AngieObject {
  
    /**
     * Collected errors
     *
     * @var array
     */
    var $errors = array();
    
    /**
     * Add new error to collection
     *
     * @param Error $error
     * @return boolean
     */
    function collect(&$error) {
      if(!instance_of($error, 'Error')) {
        return false;
      } // if
      $this->errors[] = $error;
      
      if($error->is_fatal) {
        if(function_exists('handle_fatal_error')) {
          handle_fatal_error($error);
        } else {
          if(defined('DEBUG') && DEBUG) {
            dump_error($error);
          } else {
            print 'We are sorry but fatal error prevented system from executing your request. Please try again in a few minutes';
          } // if
          die();
        } // if
      } // if
    } // collect
    
    /**
     * Check if we have errors
     *
     * @param void
     * @return boolean
     */
    function hasErrors() {
      return (boolean) count($this->errors);
    } // hasErrors
    
    /**
     * Return errors array
     *
     * @param void
     * @return array
     */
    function getErrors() {
      return $this->errors;
    } // getErrors
    
    /**
     * Return instance of collector
     *
     * @param void
     * @return ErrorCollector
     */
    function &instance() {
      static $instance;
      if(!instance_of($instance, 'ErrorCollector')) {
        $instance = new ErrorCollector();
      } // if
      return $instance;
    } // instance
  
  } // ErrorCollector

?>