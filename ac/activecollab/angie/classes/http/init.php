<?php

  /**
   * Initialization file of HTTP package
   */
  
  define('HTTP_LIB_PATH', ANGIE_PATH . '/classes/http');
  
  require HTTP_LIB_PATH . '/HTTP.class.php';
  require HTTP_LIB_PATH . '/HTTP_Header.class.php';
  require HTTP_LIB_PATH . '/HTTP_Download.class.php';

?>