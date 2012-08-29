<?php

  /**
   * Main API interface file
   *
   * @package activeCollab
   * @subpackage instance
   */

  define('PUBLIC_PATH', dirname(__FILE__));
  
  define('USE_INIT', true);
  define('INIT_MODULES', true);
  define('INIT_APPLICATION', true);
  define('HANDLE_REQUEST', true);
  
  $config_file = realpath(PUBLIC_PATH . '/../config/config.php');
  
  if(is_file($config_file)) {
    require_once($config_file);
    
    if(defined('MAINTENANCE_MESSAGE') && MAINTENANCE_MESSAGE) {
      header("HTTP/1.1 503 Service Unavailable");
      die();
    } // if
    
    define('ANGIE_API_CALL', true); // force API call!
    define('ANGIE_PATH_INFO', isset($_GET['path_info']) ? $_GET['path_info'] : '');
    if(isset($_SERVER['QUERY_STRING'])) {
      define('ANGIE_QUERY_STRING', $_SERVER['QUERY_STRING']);
    } else {
      $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
      if(($pos = strpos($request_uri, '?')) !== false) {
        define('ANGIE_QUERY_STRING', substr($request_uri, $pos + 1));
      } else {
        define('ANGIE_QUERY_STRING', '');
      } // if
    } // if
    
    // Initialize application and handle request
    require_once ROOT . '/init.php';
  } else {
    header("HTTP/1.1 503 Service Unavailable");
    die();
  } // if

?>