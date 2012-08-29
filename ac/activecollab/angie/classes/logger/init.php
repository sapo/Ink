<?php

  /**
   * Initialize logger library
   *
   * @package angie.library.logger
   */
  
  define('LOGGER_LIB_PATH', ANGIE_PATH . '/classes/logger');
  
  require LOGGER_LIB_PATH . '/Logger.class.php';
  
  define('LOG_LEVEL_INFO', 0);
  define('LOG_LEVEL_NOTICE', 1);
  define('LOG_LEVEL_WARNING', 2);
  define('LOG_LEVEL_ERROR', 3);
  
  /**
  * Add item to the log
  *
  * @param string $message
  * @param integer $level
  * @param string $group
  * @return null
  */
  function log_message($message, $level = LOG_LEVEL_INFO, $group = null) {
    static $logger = null;
    
    if($logger === null) {
      $logger =& Logger::instance();
    } // if
    
    $logger->logMessage($message, $level, $group);
  } // log_message
  
  /**
   * Handle PHP error
   *
   * @param integer $errno
   * @param integer $errstr
   * @param string $errfile
   * @param integer $errline
   * @return null
   */
  function angie_error_handler($errno, $errstr, $errfile, $errline) {
    static $instance = null;
    
    if($errno == 2048) {
      return; // Kill E_STRICT... Yeah, yeah, they have the best intentions...
    } // if
    
    if(!defined('E_STRICT')) {
      define('E_STRICT', 2048);
    } // if
    if(!defined('E_RECOVERABLE_ERROR')) {
      define('E_RECOVERABLE_ERROR', 4096);
    } // if
    
    $error_types = array (
      E_ERROR              => 'Error',
      E_WARNING            => 'Warning',
      E_PARSE              => 'Parsing Error',
      E_NOTICE             => 'Notice',
      E_CORE_ERROR         => 'Core Error',
      E_CORE_WARNING       => 'Core Warning',
      E_COMPILE_ERROR      => 'Compile Error',
      E_COMPILE_WARNING    => 'Compile Warning',
      E_USER_ERROR         => 'User Error',
      E_USER_WARNING       => 'User Warning',
      E_USER_NOTICE        => 'User Notice',
      E_STRICT             => 'Runtime Notice',
      E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
    ); // array
    
    $error_type = isset($error_types[$errno]) ? $error_types[$errno] : 'Unknown error';
    
    if($instance === null) {
      $logger =& Logger::instance();
    } // if
    
    switch ($errno) {
      case E_USER_ERROR:
        $logger->logMessage("[$error_type] $errstr (in $errfile on $errline)", LOG_LEVEL_ERROR);
        break;

      case E_USER_WARNING:
        $logger->logMessage("[$error_type] $errstr (in $errfile on $errline)", LOG_LEVEL_WARNING);
        break;

      case E_USER_NOTICE:
        $logger->logMessage("[$errno] $errstr (in $errfile on $errline)", LOG_LEVEL_INFO);
        break;

      default:
        $logger->logMessage("[$error_type] $errstr (in $errfile on $errline)", LOG_LEVEL_ERROR);
        break;
    } // switch
  } // angie_error_handler
  set_error_handler('angie_error_handler');

?>