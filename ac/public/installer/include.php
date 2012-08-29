<?php

  session_start();
  
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  
  ini_set('date.timezone', 'GMT');
  if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set('GMT');
  } else {
    @putenv('TZ=GMT'); // Don't throw a warning if system in safe mode
  } // if
  
  define('PROBE_VERSION', '1.0');
  define('PROBE_FOR', 'activeCollab 1.0');

  define('STATUS_OK', 'ok');
  define('STATUS_WARNING', 'warning');
  define('STATUS_ERROR', 'error');
  
  define('INSTALLER_PATH', dirname(__FILE__));
  define('INSTALLATION_PATH', realpath(INSTALLER_PATH . '/../../'));

  // Include library
  require_once INSTALLER_PATH . '/library/functions.php';
  require_once INSTALLER_PATH . '/library/classes/Template.class.php';
  require_once INSTALLER_PATH . '/library/Installation.class.php';
  
?>