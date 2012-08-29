<?php

  define('ROUTE_URL_VARIABLE', ':');
  define('ROUTE_REGEX_DELIMITER', '#');
  define('ROUTE_DEFAULT_REGEX', '([a-z0-9\-\._]+)');
  define('ROUTE_QUERY_STRING_SWITCH', '*');
  
  /**
   * Route definition class
   * 
   * @package angie.library.router
   */
  class Route extends AngieObject {
    
    /**
     * Name of the route
     *
     * @var string
     */
    var $name;
    
    /**
     * Input route string that is parsed into parts on construction
     *
     * @var string
     */
    var $route_string;
    
    /**
     * Route string parsed into associative array of param name => regular 
     * expression
     *
     * @var array
     */
    var $parts;
    
    /**
     * Default values for specific params
     *
     * @var array
     */
    var $defaults = array();
    
    /**
     * Regular expressions that force specific expressions for specific params
     *
     * @var array
     */
    var $requirements = array();

    /**
     * Construct route
     * 
     * This function will parse route string and populate $this->parts with rules 
     * that need to be matched
     *
     * @param string $route
     * @param array $defaults
     * @param array $requirements
     * @return Route
     */
    function __construct($name, $route, $defaults = array(), $requirements = array()) {
      $this->route_string = $route; // original string
      
      $route = trim($route, '/');
      
      $this->name         = $name;
      $this->defaults     = (array) $defaults;
      $this->requirements = (array) $requirements;

      foreach(explode('/', $route) as $pos => $part) {
        if(substr($part, 0, 1) == ROUTE_URL_VARIABLE) {
          $name = substr($part, 1);
          $regex = (isset($requirements[$name]) ? '(' . $requirements[$name] . ')' : ROUTE_DEFAULT_REGEX);
          $this->parts[$pos] = array(
            'name'  => $name, 
            'regex' => $regex
          ); // array
        } else {
          $this->parts[$pos] = array(
            'raw' => $part,
            'regex' => str_replace('\-', '-', preg_quote($part, ROUTE_REGEX_DELIMITER)), // Unescape \-
          ); // array
        } // if
      } // foreach

    } // __construct

    /**
     * Match $path with this route
     * 
     * Break down $path in part and compare with parsed route (rules are 
     * collected in $this->parts). This function will return associative array of 
     * matched parts
     *
     * @param string $path
     * @param string $query_string
     * @return boolean
     */
    function match($path, $query_string = null) {
      $values = $this->defaults;
      
      $parameters = array();
      $regex = array();
      foreach($this->parts as $part) {
        $regex[] = $part['regex'];
        if(isset($part['name'])) {
          $parameters[] = $part['name'];
        } // if
      } // foreach
      
      $regex = '/^' . implode('\/', $regex) . '$/';
      $matches = null;
      if(preg_match($regex, trim($path, '/'), $matches)) {
        $index = 0;
        foreach($parameters as $parameter_name) {
          $index++;
          $values[$parameter_name] = $matches[$index];
        } // foreach
      } else {
        return false;
      } // if
      
      $reserved = array('module', 'controller', 'action'); // reserved variable name
      
      if($query_string) {
        $query_string_parameters = array();
        parse_str($query_string, $query_string_parameters);
        
        if(is_foreachable($query_string_parameters)) {
          foreach($query_string_parameters as $parameter_name => $parameter_value) {
            if(isset($values[$parameter_name]) && in_array($values[$parameter_name], $reserved)) {
              continue;
            } // if
            $values[$parameter_name] = $parameter_value;
          } // foreach
        } // if
        
      } // if
      
      return new Request($this->getName(), $values);
    } // match

    /**
     * Assemle URL based on provided input data
     * 
     * This function will use input data and put it into route string. It can 
     * return relative path based on the route string or absolute URL 
     * (PROJECT_URL constant will be used as a base)
     *
     * @param array $data
     * @param string $url_base
     * @param string $query_arg_separator
     * @param string $anchor
     * @return string
     * @throws AssembleURLError
     */
    function assemble($data, $url_base, $query_arg_separator, $anchor = '') {
      if(!is_array($data)) {
        if($data === null) {
          $data = array();
        } else {
          $data = array('id' => $data);
        } // if
      } // if
      
      $path_parts = array();
      
      $part_names = array();
      foreach($this->parts as $key => $part) {
        if(isset($part['name'])) {
          $part_name = $part['name'];
          $part_names[] = $part_name;
          
          if(isset($data[$part_name])) {
            $path_parts[$key] = $data[$part_name];
          } elseif(isset($this->defaults[$part_name])) {
            $path_parts[$key] = $this->defaults[$part_name];
          } else {
            use_error('AssembleURLError');
            return new AssembleURLError($this->getRouteString(), $data, $this->getDefaults());
          } // if
        } else {
          $path_parts[$key] = $part['regex'];
        } // if
      } // foreach
      
      $query_parts = array();
      foreach($data as $k => $v) {
        if(!in_array($k, $part_names)) {
          $query_parts[$k] = $v;
        } // if
      } // foreach
      
      if(PATH_INFO_THROUGH_QUERY_STRING) {
        $url = $url_base;
        $query_parts = array_merge(array('path_info' => implode('/', $path_parts)), $query_parts);
      } else {
        $url = with_slash($url_base) . implode('/', $path_parts);
        if(!str_ends_with($url, '/') && str_ends_with($this->route_string, '/')) {
          $url .= '/';
        } // if
      } // if
      
      if(count($query_parts)) {
        if(version_compare(PHP_VERSION, '5.1.2', '>=')) {
          $url .= '?' . http_build_query($query_parts, '', $query_arg_separator);
        } else {
          $url .= '?' . http_build_query($query_parts, '');
        } // if
      } // if
      
      $trimmed_anchor = trim($anchor);
      if($trimmed_anchor) {
        $url .= '#' . $anchor;
      } // if
      
      return $url;
    } // assemble
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get name
     *
     * @param null
     * @return string
     */
    function getName() {
      return $this->name;
    } // getName
    
    /**
     * Set name value
     *
     * @param string $value
     * @return null
     */
    function setName($value) {
      $this->name = $value;
    } // setName
    
    /**
     * Get route_string
     *
     * @param null
     * @return string
     */
    function getRouteString() {
      return $this->route_string;
    } // getRouteString
    
    /**
     * Set route_string value
     *
     * @param string $value
     * @return null
     */
    function setRouteString($value) {
      $this->route_string = $value;
    } // setRouteString
    
    /**
     * Return defaults value
     *
     * @param void
     * @return array
     */
    function getDefaults() {
      return $this->defaults;
    } // getDefaults
    
    /**
     * Return requirements value
     *
     * @param void
     * @return array
     */
    function getRequirements() {
      return $this->requirements;
    } // getRequirements
  
  } // Route

?>