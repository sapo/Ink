<?php

  if(!defined('IN_UPGRADE_SCRIPT')) {
    die('Behave! :)');
  } // if

  define('UPGRADE_SCRIPT_VERSION', '1.0');
  define('ENVIRONMENT_PATH', realpath(UPGRADE_SCRIPT_PATH . '/../../'));
  
  require ENVIRONMENT_PATH . '/config/config.php';
  require ROOT . '/angie.php';
  require ANGIE_PATH . '/init.php';
  
  require UPGRADE_SCRIPT_PATH . '/library/UpgradeScript.class.php';
  require UPGRADE_SCRIPT_PATH . '/library/UpgradeUtility.class.php';
  
  set_time_limit(0); // No time limit!

?>