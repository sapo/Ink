<?php

  /**
   * Simple autoload utility
   * 
   * @package angie
   */
  
  define('CAN_AUTOLOAD', version_compare(PHP_VERSION, '5.0') >= 0);
  
  /**
   * Autoload class
   *
   * @param string $class
   * @return null
   */
  function __autoload($class) {
    $classes = array_var($GLOBALS, '_autoload_classes');
    
    $path = array_var($classes, strtoupper($class));
    if($path && is_file($path)) {
      require_once $path;
    } // if
  } // __autoload
  
  /**
   * Add $class to autoload index
   * 
   * $class can be associative array of classes that need to be added to the 
   * index. $path is not required in that case
   *
   * @param string $class
   * @param string $path
   * @return null
   */
  function set_for_autoload($class, $path = null) {
    
    // Add to index
    if(CAN_AUTOLOAD) {
      if(is_array($class)) {
        foreach($class as $k => $v) {
          $GLOBALS['_autoload_classes'][strtoupper($k)] = $v;
        } // if
      } else {
        $GLOBALS['_autoload_classes'][strtoupper($class)] = $path;
      } // if
      
    // Load now
    } else {
      if(is_array($class)) {
        foreach($class as $k => $v) {
          require_once $v;
        } // if
      } else {
        require_once $path;
      } // if
    } // if
  } // set_for_autoload

?>