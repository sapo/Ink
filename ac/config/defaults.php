<?php

  /**
   * Instance dependent defaults file
   *
   * @package activeCollab
   */
  
  if(!defined('ENVIRONMENT_PATH')) {
    define('ENVIRONMENT_PATH', str_replace('\\', '/', realpath(dirname(__FILE__) . '/..')));
  } // if

  require_once ROOT . '/defaults.php';

?>