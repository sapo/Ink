<?php

  /**
   * Init controller classes and resources
   *
   * @package angie.library.controller
   */
  
  define('CONTROLLER_LIB_PATH', ANGIE_PATH . '/classes/controller');
  
  // Classes
  require_once CONTROLLER_LIB_PATH . '/Controller.class.php';
  require_once CONTROLLER_LIB_PATH.  '/PageController.class.php';

?>