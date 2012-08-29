<?php

  /**
   * Initialize Angie
   * 
   * @package angie
   */
  
  if(defined('ANGIE_INITED') && ANGIE_INITED) {
    return;
  } else {
    define('ANGIE_INITED', true);
  } // if

  // Environment path is used by many environment classes. If not
  // defined do it now
  if(!defined('ANGIE_PATH')) {
    define('ANGIE_PATH', dirname(__FILE__));
  } // if
  
  // Data type constants, used by data access object...
  define('DATA_TYPE_NONE',     'NONE');
  define('DATA_TYPE_INTEGER',  'INTEGER');
  define('DATA_TYPE_STRING',   'STRING');
  define('DATA_TYPE_FLOAT',    'FLOAT');
  define('DATA_TYPE_BOOLEAN',  'BOOLEAN');
  define('DATA_TYPE_ARRAY',    'ARRAY');
  define('DATA_TYPE_RESOURCE', 'RESOURCE');
  define('DATA_TYPE_OBJECT',   'OBJECT');
  
  // ---------------------------------------------------
  //  Prepare PHP
  // ---------------------------------------------------
  
  @session_start();
  set_include_path('');
  
  ini_set('magic_quotes_runtime', false); // don't break Smarty!
  
  error_reporting(E_ALL);
  if(defined('DEBUG') && DEBUG) {
    ini_set('display_errors', 1);
  } else {
    ini_set('display_errors', 0);
  } // if
  
  define('BUILT_IN_LOCALE', 'en_US.UTF-8');
  
  if(!defined('DEFAULT_LOCALE')) {
    define('DEFAULT_LOCALE', BUILT_IN_LOCALE);
  } // if
  
  setlocale(LC_ALL, DEFAULT_LOCALE);
  
  // ---------------------------------------------------
  //  Functions and constants
  // ---------------------------------------------------
  
  require_once ANGIE_PATH . '/constants.php';
  require_once ANGIE_PATH . '/autoload.php';
  
  require_once ANGIE_PATH . '/functions/environment.php';
  require_once ANGIE_PATH . '/functions/files.php';
  require_once ANGIE_PATH . '/functions/general.php';
  require_once ANGIE_PATH . '/functions/resources.php';
  require_once ANGIE_PATH . '/functions/utf.php';
  require_once ANGIE_PATH . '/functions/web.php';
  
  // ---------------------------------------------------
  //  Lets prepare URL data
  // ---------------------------------------------------
  
  prepare_path_info(); // Extract path info and query string from request
  fix_input_quotes(); // Remove slashes is magic quotes gpc is on from $_GET, $_POST and $_COOKIE
  
  // Base
  require_once ANGIE_PATH . '/classes/AngieObject.class.php';
  
  // Debug
  if((defined('DEBUG') && DEBUG) && (DEBUG >= DEBUG_DEVELOPMENT)) {
    require_once ANGIE_PATH . '/classes/BenchmarkTimer.class.php';
    benchmark_timer_start();
    benchmark_timer_set_marker('Init environment');
  } // if
  
  // Classes
  require_once ANGIE_PATH . '/classes/Error.class.php';
  require_once ANGIE_PATH . '/classes/ErrorCollector.class.php';
  require_once ANGIE_PATH . '/classes/Inflector.class.php';
  require_once ANGIE_PATH . '/classes/Flash.class.php';
  require_once ANGIE_PATH . '/classes/Pager.class.php';
  require_once ANGIE_PATH . '/classes/Cookies.class.php';
  require_once ANGIE_PATH . '/classes/Request.class.php';
  require_once ANGIE_PATH . '/classes/GlobalStorage.class.php';
  require_once ANGIE_PATH . '/classes/NamedList.class.php';
  require_once ANGIE_PATH . '/classes/NamedList.class.php';
  require_once ANGIE_PATH . '/classes/captcha/Captcha.class.php';
  
  // Libraries
  require_once ANGIE_PATH . '/classes/application/init.php';
  require_once ANGIE_PATH . '/classes/auth/init.php';
  require_once ANGIE_PATH . '/classes/cache/init.php';
  require_once ANGIE_PATH . '/classes/controller/init.php';
  require_once ANGIE_PATH . '/classes/database/init.php';
  require_once ANGIE_PATH . '/classes/datetime/init.php';
  require_once ANGIE_PATH . '/classes/router/init.php';
  require_once ANGIE_PATH . '/classes/smarty/init.php';
  require_once ANGIE_PATH . '/classes/events/init.php';
  require_once ANGIE_PATH . '/classes/json/init.php';
  require_once ANGIE_PATH . '/classes/logger/init.php';
  
  // Base errors
  require_once ANGIE_PATH . '/classes/errors/FileDnxError.class.php';
  require_once ANGIE_PATH . '/classes/errors/InvalidParamError.class.php';
  require_once ANGIE_PATH . '/classes/errors/InvalidInstanceError.class.php';
  require_once ANGIE_PATH . '/classes/errors/ValidationErrors.class.php';

?>