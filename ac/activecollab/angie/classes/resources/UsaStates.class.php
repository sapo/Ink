<?php

  /**
   * Index of all USA states
   *
   * @package angie.library.resources
   */
  class UsaStates {
  
    /**
    * Array of states
    *
    * @var array
    */
    var $states = array (
  		"AK" => "Alaska",
  		"AL" => "Alabama",
  		"AR" => "Arkansas",
  		"AZ" => "Arizona",
  		"CA" => "California",
  		"CO" => "Colorado",
  		"CT" => "Connecticut",
  		"DC" => "District of Columbia",
  		"DE" => "Delaware",
  		"FL" => "Florida",
  		"GA" => "Georgia",
  		"HI" => "Hawaii",
  		"IA" => "Iowa",
  		"ID" => "Idaho",
  		"IL" => "Illinois",
  		"IN" => "Indiana",
  		"KS" => "Kansas",
  		"KY" => "Kentucky",
  		"LA" => "Louisiana",
  		"MA" => "Massachusetts",
  		"MD" => "Maryland",
  		"ME" => "Maine",
  		"MI" => "Michigan",
  		"MN" => "Minnesota",
  		"MO" => "Missouri",
  		"MS" => "Mississippi",
  		"MT" => "Montana",
  		"NC" => "North Carolina",
  		"ND" => "North Dakota",
  		"NE" => "Nebraska",
  		"NH" => "New Hampshire",
  		"NJ" => "New Jersey",
  		"NM" => "New Mexico",
  		"NV" => "Nevada",
  		"NY" => "New York",
  		"OH" => "Ohio",
  		"OK" => "Oklahoma",
  		"OR" => "Oregon",
  		"PA" => "Pennsylvania",
  		"PR" => "Puerto Rico",
  		"RI" => "Rhode Island",
  		"SC" => "South Carolina",
  		"SD" => "South Dakota",
  		"TN" => "Tennessee",
  		"TX" => "Texas",
  		"UT" => "Utah",
  		"VA" => "Virginia",
  		"VT" => "Vermont",
  		"WA" => "Washington",
  		"WI" => "Wisconsin",
  		"WV" => "West Virginia",
  		"WY" => "Wyoming",
		); // array
		
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
      if(!instance_of($instance, 'UsaStates')) {
        $instance = new UsaStates();
      } // if
      return $instance;
    } // instance
  
  } // UsaStates

?>