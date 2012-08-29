<?php

  /**
   * Abstract authentication provider
   * 
   * @package angie.library.authentication
   * @subpackage provider
   */
  class AuthenticationProvider extends AngieObject {
    
    /**
     * Logged user
     * 
     * @var User
     */
    var $user;
    
    /**
     * Initialize provider
     * 
     * This method is called on authentication manager initialization
     *
     * @param void
     * @return User
     */
    function initialize() {
      use_error('NotImplementederror');
      return new NotImplementedError('initialize', 'AuthenticationProvider');
    } // initialize
    
    /**
     * Authenticate with given credential agains authentication source
     *
     * @param array $credentials
     * @return User
     */
    function authenticate($credentials) {
      use_error('NotImplementederror');
      return new NotImplementedError('authenticate', 'AuthenticationProvider');
    } // authenticate
    
    /**
     * Set logged user
     * 
     * This method is called after user is successfully authenticated. We can put 
     * functionality that remembers user in a cookie or session, updates flags 
     * and timestamps in users table and so on
     *
     * @param User $user
     * @param array $settings
     * @return User
     */
    function &logUserIn($user, $settings = null) {      
      $this->user = $user;
      return $this->user;
    } // logUserIn
    
    /**
     * Log user out
     *
     * @param void
     * @return null
     */
    function logUserOut() {
      $this->user = null;
    } // logUserOut
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return logged user (if we have it)
     *
     * @param void
     * @return User
     */
    function &getUser() {
      return $this->user;
    } // getUser
    
  } // AuthenticationProvider

?>