<?php

  /**
   * Empty authentication provider
   * 
   * Use this class as skeleton for your authentication provider
   *
   * @package angie.library.authentication
   * @subpackage provider
   */
  class EmptyAuthenticationProvider extends AuthenticationProvider {
    
    /**
    * Initialize basic authentication
    *
    * @param void
    * @return User
    */
    function initialize() {
      return null;
    } // init
    
    /**
    * Try to log user in with given credentials
    *
    * @param array $credentials
    * @return User
    */
    function authenticate($credentials) {
      return null;
    } // authenticate
    
  } // BasicAuthenticationProvider

?>