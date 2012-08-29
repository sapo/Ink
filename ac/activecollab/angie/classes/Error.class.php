<?php

  /**
   * Error class
   * 
   * Errors are similar to exceptions in PHP5 but without some cool tricks
   * that build in error handling provides.
   *
   * @author Ilija Studen <ilija.studen@gmail.com>
   */
  class Error extends AngieObject {
    
    /**
     * Error message
     *
     * @var string
     */
    var $message;
  
    /**
     * Error line
     *
     * @var integer
     */
    var $line;
    
    /**
     * Error file
     *
     * @var string
     */
    var $file;
    
    /**
     * Backtrace array
     *
     * @var array
     */
    var $backtrace;
    
    /**
     * Fatal errors are treated as exceptions
     * 
     * On fatal error ErrorCollector will display error (or apropriate error 
     * message if in production mode) and stop script execution
     *
     * @var boolean
     */
    var $is_fatal = false;
    
    /**
     * Construct the error
     *
     * @param string $message
     * @param boolean $is_fatal
     * @return null
     */
    function __construct($message, $is_fatal = null) {
      $this->setMessage($message);
      
      if($is_fatal === true || $is_fatal === false) {
        $this->is_fatal = $is_fatal;
      } // if
      
      if($is_fatal) {
        ob_start();
        debug_print_backtrace();
        $this->setBacktrace(ob_get_clean());
      } else {
        $this->setBacktrace('Backtrace is available only for fatal errors');
      } // if
      
      // And log!
      $collector =& ErrorCollector::instance();
      $collector->collect($this);
    } // __construct
    
    /**
     * Return error params (name -> value pairs). General params are file and line
     * and any specific error have their own params...
     *
     * @param void
     * @return array
     */
    function getParams() {
      $base = array(
        'file' => $this->getFile(),
        'line' => $this->getLine()
      ); // array
      
      // Get additional params...
      $additional = $this->getAdditionalParams();
      
      // And return (join if we have additional params)
      return is_array($additional) ? array_merge($base, $additional) : $base;
    } // getParams
    
    /**
     * Return additional error params
     *
     * @param void
     * @return array
     */
    function getAdditionalParams() {
      return null;
    } // getAdditionalParams
    
    /**
     * Describe errors
     *
     * @param void
     * @return array
     */
    function describe() {
      $params = $this->getAdditionalParams();
      return is_array($params) ? 
        array_merge(array('message' => $this->getMessage()), $params) : 
        array('message' => $this->getMessage());
    } // describe
    
    // -------------------------------------------------------
    // Getters and setters
    // -------------------------------------------------------
    
    /**
     * Get message
     *
     * @param null
     * @return string
     */
    function getMessage() {
      return $this->message;
    } // getMessage
    
    /**
     * Set message value
     *
     * @param string $value
     * @return null
     */
    function setMessage($value) {
      if (is_array($value)) {
        $this->message = implode(', ', $value);
      } else {
        $this->message = $value;  
      } // if
    } // setMessage
    
    /**
     * Get line
     *
     * @param null
     * @return integer
     */
    function getLine() {
      return $this->line;
    } // getLine
    
    /**
     * Set line value
     *
     * @param integer $value
     * @return null
     */
    function setLine($value) {
      $this->line = $value;
    } // setLine
    
    /**
     * Get file
     *
     * @param null
     * @return string
     */
    function getFile() {
      return $this->file;
    } // getFile
    
    /**
     * Set file value
     *
     * @param string $value
     * @return null
     */
    function setFile($value) {
      $this->file = $value;
    } // setFile
    
    /**
     * Get backtrace
     *
     * @param null
     * @return array
     */
    function getBacktrace() {
      return $this->backtrace;
    } // getBacktrace
    
    /**
     * Set backtrace value
     *
     * @param array $value
     * @return null
     */
    function setBacktrace($value) {
      $this->backtrace = $value;
    } // setBacktrace
  
  } // Error

?>