<?php

  /**
   * Init router library
   * 
   * @package angie.library.router
   */
  
  define('ROUTER_LIB_PATH', ANGIE_PATH . '/classes/router');
  
  require_once ROUTER_LIB_PATH . '/Route.class.php';
  require_once ROUTER_LIB_PATH . '/Router.class.php';
  
  /**
   * Assemble URL based on a specific route
   * 
   * Supported options:
   * 
   * - url_base (string): base for URL-s, default is an empty string
   * - query_arg_separator (string): what to use to separate query string 
   *   arguments, default is '&'
   * - anchor (string): name of the URL anchor
   *
   * @param string $name
   * @param array $params
   * @param array $options
   * @return string
   */
  function assemble_url($name, $params = null, $options = null) {
    static $instance;
    if($instance === null) {
      $instance =& Router::instance();
    } // if
    return $instance->assemble($name, $params, $options);
  } // assemble_url
  
  /**
   * Assemble URL from string
   * 
   * This function will convert string in format ?route=name[&param=value...] to 
   * URL based on route and params. Route paremeter is required
   *
   * @param string $string
   * @return null
   */
  function assemble_from_string($string) {
    $params = parse_string(substr($string, 1));
    
    $route = array_var($params, 'route');
    if(empty($route)) {
      use_error('RouteNotDefinedError');
      return new RouteNotDefinedError($route);
    } // if
    unset($params['route']);
    
    return assemble_url($route, $params);
  } // assemble_from_string

?>