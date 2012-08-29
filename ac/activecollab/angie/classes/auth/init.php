<?php

  /**
   * Authentication library initialization file
   *
   * @package angie.library.authentication
   */
  
  define('AUTH_LIB_PATH', ANGIE_PATH . '/classes/auth');
  
  require AUTH_LIB_PATH . '/Authentication.class.php';
  require AUTH_LIB_PATH . '/providers/AuthenticationProvider.class.php';
  
  /**
  * Include authentication provider
  *
  * @param string $provider
  * @return null
  */
  function use_auth_provider($provider) {
    $custom_path = CUSTOM_PATH . "/auth_providers/$provider.class.php";
    if(is_file($custom_path)) {
      require_once $custom_path;
    } else {
      require_once AUTH_LIB_PATH . "/providers/$provider.class.php";
    } // if
  } // use_auth_provider
  
  /**
   * Return owner company instance
   *
   * @param void
   * @return Company
   */
  function get_owner_company() {
    static $instance = null;
    
    if($instance === null) {
      $instance = cache_get('owner_company');
      if(!$instance) {
        $instance = Companies::findOwnerCompany();
      } // if
    } // if
    
    return $instance;
  } // get_owner_company
  
  /**
  * Return logged user instance
  *
  * @param void
  * @return User
  */
  function &get_logged_user() {
    static $instance = null;
    
    if($instance === null) {
      $instance =& Authentication::instance();
    } // if
    
    if($instance->provider) {
      return $instance->provider->getUser();
    } else {
      $return = null;
      return $return;
    } // if
  } // get_logged_user
  
  /**
  * Return logged user ID
  *
  * @param void
  * @return integer
  */
  function get_logged_user_id() {
    static $user_id = false;
    
    if($user_id === false) {
      $user =& get_logged_user();
      $user_id = instance_of($user, 'User') ? $user->getId() : null;
    } // if
    
    return $user_id;
  } // get_logged_user_id
  
  /**
  * Returns true if we have logged user
  *
  * @param void
  * @return boolean
  */
  function is_logged_user() {
    static $is_logged = null;
    
    if($is_logged === null) {
      $user =& get_logged_user();
      $is_logged = instance_of($user, 'User');
    } // if
    
    return $is_logged;
  } // is_logged_user

?>