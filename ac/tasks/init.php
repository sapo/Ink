<?php

  /**
   * Initialize system so events can be triggered
   *
   * @package activeCollab
   * @subpackage tasks
   */
  
  define('USE_INIT', true);
  define('INIT_MODULES', true);
  define('INIT_APPLICATION', true);
  
  require_once dirname(__FILE__).'/../config/config.php';
  require_once ROOT . '/init.php';
  
  if(DIRECTORY_SEPARATOR == '\\') {
    define('PUBLIC_PATH', str_replace('\\', '/', dirname(__FILE__).'/../'.PUBLIC_FOLDER_NAME));
  } else {
    define('PUBLIC_PATH', dirname(__FILE__).'/../'.PUBLIC_FOLDER_NAME);
  } // if
  
  if(defined('PROTECT_SCHEDULED_TASKS') && PROTECT_SCHEDULED_TASKS) {
    $code = array_var($argv, 1);
    if(empty($code) || strtoupper($code) != strtoupper(substr)) {
      print "Error: Invalid protection code!\n\nMake sure that you provide first 5 characters of your license key after file name:\n\n  ~ php event.php #CODE#\n\n";
      die();
    } // if
  } // if

?>