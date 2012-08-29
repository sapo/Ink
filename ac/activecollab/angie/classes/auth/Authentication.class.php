<?php

  /**
   * Authentication manager
   *
   * @package angie.library.authentication
   */
  class Authentication extends AngieObject {
    
    /**
    * Authentication provider
    * 
    * @var AuthenticationProvider
    */
    var $provider;
    
    /**
    * Construct authentication manager
    *
    * @param AuthenticationProvider $auth_provider
    * @return Authentication
    */
    function __construct($provider, $initialize = true) {
      if(instance_of($provider, 'AuthenticationProvider')) {
        $this->provider = $provider;
        if($initialize) {
          $this->provider->initialize();
        } // if
      } // if
    } // __construct
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Return one and only Authentication instance
    *
    * @param AuthenticationProvider $auth_provider
    * @param boolean $initialize
    * @return Authentication
    */
    function &instance($auth_provider = null, $initialize = false) {
      static $instance;
      if(!instance_of($instance, 'Authentication')) {
        $instance = new Authentication($auth_provider, $initialize);
      } // if
      return $instance;
    } // instance
    
  } // Authentication

?>