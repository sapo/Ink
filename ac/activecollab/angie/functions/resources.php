<?php

  /**
   * Functions that wrap resources
   * 
   * A set of functions used to wrap and load resources such are names of 
   * countries, codes etc.
   * 
   * @package angie.functions
   */

  /**
   * Return array of USA states
   *
   * @param void
   * @return array
   */
  function get_usa_states() {
    static $instance;
    
    if(!instance_of($instance, 'UsaStates')) {
      require_once ANGIE_PATH . '/classes/resources/UsaStates.class.php';
      $instance =& UsaStates::instance();
    } // if
    
    return $instance->getStates();
  } // get_usa_states
  
  /**
   * Return array of Canada states
   *
   * @param void
   * @return array
   */
  function get_canada_states() {
    static $instance;
    
    if(!instance_of($instance, 'CanadaStates')) {
      require_once ANGIE_PATH . '/classes/resources/CanadaStates.class.php';
      $instance =& CanadaStates::instance();
    } // if
    
    return $instance->getStates();
  } // get_canada_states
  
  /**
   * Return name of state based on its code
   *
   * @param string $code
   * @return string
   */
  function get_state_name($code) {
    $code = strtoupper($code);
    $states = array_merge(get_usa_states(), get_canada_states());
    return isset($states[$code]) ? $states[$code] : $code;
  } // get_state_name
  
  /**
   * Return all countries
   *
   * @param void
   * @return array
   */
  function get_countries() {
    static $instance;
    
    if(!instance_of($instance, 'Countries')) {
      require_once ANGIE_PATH . '/classes/resources/Countries.class.php';
      $instance =& Countries::instance();
    } // if
    
    return $instance->getCountries();
  } // get_countries
  
  /**
   * Return country name based on country code
   *
   * @param string $code
   * @return string
   */
  function get_country_name($code) {
    static $instance;
    
    if(!instance_of($instance, 'Countries')) {
      require_once ANGIE_PATH . '/classes/resources/Countries.class.php';
      $instance =& Countries::instance();
    } // if
    
    return $instance->getCountryName($code);
  } // get_country_name

?>