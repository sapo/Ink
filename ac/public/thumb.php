<?php

  /**
   * Load and passthru a specific thumbnail
   */
  
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  
  // Make sure that we have timezone set (PHP 5.3.0 compatibility)
  ini_set('date.timezone', 'GMT');
  if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set('GMT');
  } else {
    @putenv('TZ=GMT'); // Don't throw a warning if system in safe mode
  } // if
  
  $name = isset($_GET['name']) ? trim($_GET['name']) : '';
  $size = isset($_GET['ver']) ? (integer) $_GET['ver'] : 0;
  
  if(empty($name) || empty($size)) {
    header("HTTP/1.0 404 Not Found");
    die();
  } // if
  
  require_once '../config/config.php';
  require_once ROOT . '/angie.php';
  
  require_once ANGIE_PATH . '/functions/environment.php';
  require_once ANGIE_PATH . '/functions/general.php';
  require_once ANGIE_PATH . '/functions/web.php';
  
  $path = ENVIRONMENT_PATH . '/thumbnails/' . $name;
  if(!is_file($path) || (filesize($path) != $size)) {
    header("HTTP/1.0 404 Not Found");
    die();
  } // if
  
  download_file($path, 'image/jpeg', 'thumbnail.jpg');

?>