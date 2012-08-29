<?php

  /**
   * Router
   * 
   * Router provides support for canonical, pretty URL-s out of box. Reuqest is 
   * matched with set of routes mapped by the user; when router finds first match 
   * it will use data collected from it and match process will be stoped. Routes 
   * are matched in reveresed order so make sure that general routes are on top 
   * of the map list
   * 
   * @package angie.library.router
   */
  class Router extends AngieObject {
    
    /**
     * Array of mapped routes
     *
     * @var array
     */
    var $routes = array();
    
    /**
     * Current module
     * 
     * Used by loadByModules function to remember the name of current module. 
     * When this value is present, by no 'module' is defined in route 
     * definition, map() method will use this value
     *
     * @var string
     */
    var $current_module = null;
    
    /**
     * Load routes by modules
     *
     * @param array $modules
     * @return null
     */
    function loadByModules($modules) {
      foreach($modules as $module) {
        $this->current_module = $module->getName();
        $module->defineRoutes($this);
      } // foreach
    } // loadByModules
    
    /**
     * Regiter a new route
     * 
     * This function will create a new route based on route string, default 
     * values and additional requirements and save  it under specific name. Name 
     * is used so you can access the route when assembling URL based on a given 
     * route. Name needs to be unique (if route with a given name is already 
     * registered it will be overwriten).
     *
     * @param string $name
     * @param string $route
     * @param array $defaults
     * @param array $requirements
     * @return Angie_Router_Route
     */
    function map($name, $route, $defaults = null, $requirements = null) {
      if($defaults) {
        if(!isset($defaults['module'])) {
          $defaults['module'] = $this->current_module;
        } // if
      } else {
        $defaults = array('module' => $this->current_module);
      } // if
      
      $this->routes[$name] = new Route($name, $route, $defaults, $requirements);
      return $this->routes[$name];
    } // map
    
    /**
     * Match request string agains array of mapped routes
     * 
     * This function will loop request string agains array of mapped routes. As 
     * soon as request string is matched looping is stopped and result of route 
     * match method is returned (array of name => value pairs). In case that 
     * none of the mapped routes does not match request string RoutingError will 
     * be thrown
     *
     * @param string $str
     * @param string $query_string
     * @return Request
     * @throws RoutingError
     */
    function match($str, $query_string) {
      if(DEBUG >= DEBUG_DEVELOPMENT) {
        log_message("Routing string '$str'", LOG_LEVEL_INFO, 'routing');
      } // if
      $routes = array_reverse($this->routes);
    
      foreach($routes as $route_name => $route) {
        $match = $route->match($str, $query_string);
        if(instance_of($match, 'Request')) {
          if(DEBUG >= DEBUG_DEVELOPMENT) {
            log_message("String '$str' matched with '$route_name' route", LOG_LEVEL_INFO, 'routing');
          } // if
          return $match;
        } // if
      } // foreach
      
      if(DEBUG >= DEBUG_DEVELOPMENT) {
        log_message("Failed to find a route that match '$str'", LOG_LEVEL_ERROR, 'routing');
      } // if
      
      use_error('RoutingError');
      return new RoutingError($str);
    } // match
    
    /**
     * Assemble URL
     * 
     * Supported options:
     * 
     * - url_base (string): base for URL-s, default is an empty string
     * - query_arg_separator (string): what to use to separate query string 
     *   arguments, default is '&'
     * - anchor (string): name of the URL anchor
     * 
     * @param string $name
     * @param array $data
     * @param array $options
     * @throws Angie_Router_Error_Assemble
     */
    function assemble($name, $data = array(), $options = null) {
      $url_base = array_var($options, 'url_base', '');
      if(empty($url_base)) {
        $url_base = URL_BASE;
      } // if
      $query_arg_separator = array_var($options, 'query_arg_separator', '&');
      $anchor = array_var($options, 'anchor', '');
      
      $route = array_var($this->routes, $name);
      if(!instance_of($route, 'Route')) {
        use_error('RouteNotDefinedError');
        return new RouteNotDefinedError($name);
      } // if
      
      log_message("Route $name assembled", LOG_LEVEL_INFO, 'routing');
      
      return $route->assemble($data, $url_base, $query_arg_separator, $anchor);
    } // assemble
    
    /**
     * Clean router, mostly used in tests
     *
     * @param void
     * @return null
     */
    function cleanUp() {
      $this->routes = array();
    } // cleanUp
  
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Returns array of mapped routes
     *
     * @param void
     * @return array
     */
    function getRoutes() {
      return $this->routes;
    } // getRoutes
    
    /**
     * Return route by name
     *
     * @param string $name
     * @return Angie_Route
     */
    function getRoute($name) {
      return array_var($this->routes, $name);
    } // getRoute
    
    /**
     * Return router instance
     *
     * @param void
     * @return Router
     */
    function &instance() {
      static $instance;
      if(!instance_of($instance, 'Router')) {
        $instance = new Router();
      } // if
      return $instance;
    } // instance
  
  } // Router

?>