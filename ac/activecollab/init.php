<?php

  /**
   * Application initialization file
   *
   * @package activeCollab
   */

  if(!defined('USE_INIT')) {
    die('System error: Invalid inclusion. Check /init.php');
  } // if
  
  require ROOT . '/angie.php';
  require ANGIE_PATH . '/init.php';
  
  if(!function_exists('handle_fatal_error')) {
  
    /**
     * Handle fatal error
     *
     * @param Error $error
     * @return null
     */
    function handle_fatal_error($error) {
      if(DEBUG >= DEBUG_DEVELOPMENT) {
        dump_error($error);
      } else {
        if(instance_of($error, 'RoutingError') || instance_of($error, 'RouteNotDefinedError')) {
          header("HTTP/1.1 404 Not Found");
          print '<h1>Not Found</h1>';
          if(instance_of($error, 'RoutingError')) {
            print '<p>Page "<em>' . clean($error->getRequestString()) . '</em>" not found.</p>';
          } else {
            print '<p>Route "<em>' . clean($error->getRouteName()) . '</em>" not mapped.</p>';
          } // if
          print '<p><a href="' . assemble_url('homepage') . '">&laquo; Back to homepage</a></p>';
          die();
        } // if
        
        // Send email to administrator
        if(defined('ADMIN_EMAIL') && is_valid_email(ADMIN_EMAIL)) {
          $content = '<p>Hi,</p><p>activeCollab setup at ' . clean(ROOT_URL) . ' experienced fatal error. Info:</p>';
          
          ob_start();
          dump_error($error, false);
          $content .= ob_get_clean();
          
          @mail(ADMIN_EMAIL, 'activeCollab Crash Report', $content, "Content-Type: text/html; charset=utf-8");
        } // if
        
        // log...
        if(defined('ENVIRONMENT_PATH') && class_exists('Logger')) {
          $logger =& Logger::instance();
          $logger->logToFile(ENVIRONMENT_PATH . '/logs/' . date('Y-m-d') . '.txt');
        } // if
      } // if
      
      $error_message = '<div style="text-align: left; background: white; color: red; padding: 7px 15px; border: 1px solid red; font: 12px Verdana; font-weight: normal;">';
      $error_message .= '<p>Fatal error: activeCollab has failed to executed your request (reason: ' . clean(get_class($error)) . '). Information about this error has been logged and sent to administrator.</p>';
      if(is_valid_url(ROOT_URL)) {
        $error_message .= '<p><a href="' . ROOT_URL . '">&laquo; Back to homepage</a></p>';
      } // if
      $error_message .= '</div>';
      
      print $error_message;
      die();
    } // handle_fatal_error
  } // if
  
  $application =& application();
  $application->prepare(array(
    'initialize_resources' => true,
    'connect_to_database'  => true,
    'initialize_smarty'    => true,
    'init_modules'         => defined('INIT_MODULES') && INIT_MODULES,
    'authenticate'         => true,
    'init_locale'          => true,
    'load_hooks'           => true,
  ));
  
  if(defined('INIT_APPLICATION') && INIT_APPLICATION) {
    if(DEBUG >= DEBUG_DEVELOPMENT) {
      benchmark_timer_set_marker('Init application');
    } // if
    $application->init();
  } // if
  
  if(defined('HANDLE_REQUEST') && HANDLE_REQUEST) {
    if(DEBUG >= DEBUG_DEVELOPMENT) {
      benchmark_timer_set_marker('Handle request');
    } // if
    $application->handleHttpRequest();
  } // if
  
?>