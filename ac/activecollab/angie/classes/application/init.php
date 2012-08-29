<?php

  /**
  * Angie application initialization file
  *
  * @author Ilija Studen <ilija.studen@gmail.com>
  */

  define('ANGIE_APPLICATION_LIB_PATH', ANGIE_PATH . '/classes/application');
  
  require ANGIE_APPLICATION_LIB_PATH . '/AngieApplication.class.php';
  
  /**
  * Return application instance
  *
  * @param void
  * @return AngieApplication
  */
  function &application() {
    static $instance = false;
    
    if(!defined('APPLICATION_NAME')) {
      $nothing = null;
      return $nothing;
    } // if
    
    if($instance === false) {
      $class = APPLICATION_NAME;
      $file = APPLICATION_PATH . "/$class.class.php";
      
      if(is_file($file)) {
        require $file;
        
        if(class_exists($class)) {
          $application = new $class();
          if(instance_of($application, 'AngieApplication')) {
            $instance = $application;
          } else {
            use_error('ClassNotImplementedError');
            return new ClassNotImplementedError($class, $file);
          } // if
        } else {
          use_error('ClassNotImplementedError');
          return new ClassNotImplementedError($class, $file);
        } // if
        
      } else {
        use_error('ClassNotImplementedError');
        return new ClassNotImplementedError($class, $file);
      } // if
    } // if
    
    return $instance;
  } // application
  
  /**
  * Call application shutdown function on shutdown
  *
  * @param void
  * @return null
  */
  function angie_shutdown() {
    $application =& application();
    if(instance_of($application, 'AngieApplication')) {
      $application->shutdown();
    } // if
  } // angie_shutdown
  
  register_shutdown_function('angie_shutdown');

?>