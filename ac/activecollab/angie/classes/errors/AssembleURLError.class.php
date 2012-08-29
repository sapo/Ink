<?php

  /**
   * Route assemble error
   * 
   * This error is thrown when we fail to assembe URL based on default values 
   * and provided data
   */
  class AssembleURLError extends Error {
    
    /**
    * Route string
    *
    * @var string
    */
    var $route_string;
    
    /**
    * Data used for URL assembling
    *
    * @var array
    */
    var $assembly_data;
    
    /**
    * Array of default values
    *
    * @var array
    */
    var $default_data;
    
    /**
    * Fatal error
    * 
    * On fatal error script stops execution and handle_fatal_error hook is 
    * called
    *
    * @var boolean
    */
    var $is_fatal = true;
  
    /**
    * Constructor
    *
    * @param string $route_string
    * @param array $assembly_data
    * @param array $default_values
    * @return Angie_Error_Router_Assemble
    */
    function __construct($route_string, $assembly_data, $default_data, $message = null) {
      if(is_null($message)) {
        $message = 'Failed to assembe URL based on provided data';
      } // if
      
      $this->setRouteString($route_string);
      $this->setAssemblyData($assembly_data);
      $this->setDefaultData($default_data);
      
      parent::__construct($message, true);
    } // __construct
    
    /**
    * Return additional error params
    *
    * @param void
    * @return array
    */
    function getAdditionalParams() {
      return array(
        'route string' => $this->getRouteString(),
        'assembly data' => $this->getAssemblyData(),
        'default data' => $this->getDefaultData()
      ); // array
    } // getAdditionalParams
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
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
    * Get assembly_data
    *
    * @param null
    * @return array
    */
    function getAssemblyData() {
      return $this->assembly_data;
    } // getAssemblyData
    
    /**
    * Set assembly_data value
    *
    * @param array $value
    * @return null
    */
    function setAssemblyData($value) {
      $this->assembly_data = $value;
    } // setAssemblyData
    
    /**
    * Get default_data
    *
    * @param null
    * @return array
    */
    function getDefaultData() {
      return $this->default_data;
    } // getDefaultData
    
    /**
    * Set default_data value
    *
    * @param array $value
    * @return null
    */
    function setDefaultData($value) {
      $this->default_data = $value;
    } // setDefaultData
  
  } // Angie_Router_Error_Assemble

?>