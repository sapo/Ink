<?php

  /**
   * Canada states definition
   *
   * @package angie.library.resources
   */
  class CanadaStates {
   
    /**
     * Array of Canada states
     *
     * @var array
     */ 
    var $states = array(
      'AB' => 'Alberta',
      'BC' => 'British Columbia',
      'MB' => 'Manitoba',
      'NB' => 'New Brunswick',
      'NF' => 'Newfoundland',
      'NT' => 'Northwest Territories',
      'NS' => 'Nova Scotia',
      'ON' => 'Ontario',
      'PE' => 'Prince Edward Island',
      'QC' => 'Quebec',
      'SK' => 'Saskatchewan',
      'YT' => 'Yukon',
    );
  
    /**
		* Return state name based on the code
		*
		* @param string $code
		* @return string
		*/
		function getStateName($code) {
		  $find_code = strtoupper($code);
		  return isset($this->states[$find_code]) ? $this->states[$find_code] : $code;
		} // getStateName
		
		/**
		* Return all states
		*
		* @param void
		* @return array
		*/
		function getStates() {
		  return $this->states;
		} // getStates
		
		/**
    * Return single instance
    *
    * @param void
    * @return States
    */
    function &instance() {
      static $instance;
      if(!instance_of($instance, 'CanadaStates')) {
        $instance = new CanadaStates();
      } // if
      return $instance;
    } // instance
  
  } // CanadaStates

?>