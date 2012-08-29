<?php

  /**
   * Load and passthru captcha
   */
  session_start();
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  
  require_once '../config/config.php';
  require_once ROOT . '/angie.php';
  
  require_once ANGIE_PATH . '/functions/files.php';
  
  require_once ANGIE_PATH . '/classes/AngieObject.class.php';
  require_once ANGIE_PATH . '/classes/captcha/Captcha.class.php';

  $captcha = new Captcha(200,30);
  $captcha->Create();

?>