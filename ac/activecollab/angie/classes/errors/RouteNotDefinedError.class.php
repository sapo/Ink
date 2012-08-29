<?php

  /**
   * Route not defined error
   *
   * @package angie
   * @subpackage errors
   */
  class RouteNotDefinedError extends Error {
    
    /**
     * Name of the route
     *
     * @var string
     */
    var $route_name;
    
    /**
     * Construct route not defined error instance
     *
     * @param string $name
     * @param string $message
     * @return RouteNotDefinedError
     */
    function __construct($name, $message = null) {
      if($message === null) {
        $message = "Route '$name' is not defined";
      } // if
      
      $this->setRouteName($name);
      parent::__construct($name, true);
    } // __construct
    
    /**
     * Return additional error params
     *
     * @param void
     * @return array
     */
    function getAdditionalParams() {
      return array(
        'route' => $this->getRouteName(),
      ); // array
    } // getAdditionalParams
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return route_name
     *
     * @param void
     * @return string
     */
    function getRouteName() {
    	return $this->route_name;
    } // getRouteName
    
    /**
     * Set route_name
     *
     * @param string $value
     * @return null
     */
    function setRouteName($value) {
      $this->route_name = $value;
    } // setRouteName
    
  }

?>