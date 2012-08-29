<?php

  /**
   * Timezones
   * 
   * This class let user get a list of all timezones or to get information on any 
   * single timezone.
   *
   * @package angie.library.datetime
   */
  class Timezones extends AngieObject {
    
    /**
    * Offset in seconds - cities map
    *
    * @var array
    */
    var $timezones = array(
      -43200 => array('International Date Line West'),
      -39600 => array('Midway Island', 'Samoa'),
      -36000 => array('Hawaii'),
      -32400 => array('Alaska'),
      -28800 => array('Pacific Time (US & Canada)'),
      -25200 => array('Mountain Time (US & Canada)'),
      -21600 => array('Central Time (US & Canada)'),
      -18000 => array('Eastern Time (US & Canada)'),
      -16200 => array('Caracas'),
      -14400 => array('Atlantic Time (Canada)'),
      -12600 => array('Newfoundland'),
      -10800 => array('Brasilia', 'Buenos Aires', 'Georgetown', 'Greenland'), 
       -7200 => array('Mid-Atlantic'),
       -3600 => array('Azores', 'Cape Verde Is.'),
           0 => array('Dublin', 'Edinburgh', 'Lisbon', 'London', 'Casablanca', 'Monrovia'),
        3600 => array('Berlin', 'Brussels', 'Copenhagen', 'Madrid', 'Paris', 'Rome'),
        7200 => array('Kaliningrad', 'South Africa', 'Warsaw'),
       10800 => array('Baghdad', 'Riyadh', 'Moscow', 'Nairobi'),
       12600 => array('Tehran'),
       14400 => array('Abu Dhabi', 'Muscat', 'Baku', 'Tbilisi', 'Yerevan'),
       16200 => array('Kabul'),
       18000 => array('Ekaterinburg', 'Islamabad', 'Karachi', 'Tashkent'),
       19800 => array('Chennai', 'Kolkata', 'Mumbai', 'New Delhi'),
       20700 => array('Kathmandu'),
       21600 => array('Astana', 'Dhaka', 'Sri Jayawardenepura', 'Almaty', 'Novosibirsk'),
       23400 => array('Rangoon'),
       25200 => array('Bangkok', 'Hanoi', 'Jakarta', 'Krasnoyarsk'),
       28800 => array('Beijing', 'Hong Kong', 'Perth', 'Singapore', 'Taipei'),
       32400 => array('Seoul', 'Osaka', 'Sapporo', 'Tokyo', 'Yakutsk'),
       34200 => array('Darwin', 'Adelaide'),
       36000 => array('Melbourne', 'Papua New Guinea', 'Sydney', 'Vladivostok'),
       39600 => array('Magadan', 'Solomon Is.', 'New Caledonia'),
       43200 => array('Fiji', 'Kamchatka', 'Marshall Is.', 'Auckland', 'Wellington'),
       46800 => array('Nuku\'alofa'),
    ); // array
  
    /**
    * Return all timezones
    * 
    * Use map at $timezones and return array of populated Timezone objects
    *
    * @param void
    * @return array
    */
    function getAll() {
      if(isset($this) && instance_of($this, 'Timezones')) {
        $result = array();
        foreach($this->timezones as $offset => $name) {
          $result[] = new Timezone($offset, implode(', ', $name));
        } // foreach
        return $result;
      } else {
        $instance =& Timezones::instance();
        return $instance->getAll();
      } // if
    } // getAll
    
    /**
    * Return timezone object by offset (in seconds)
    * 
    * Invalid parametar exception will be thrown if timezone with a given offset 
    * does not exist
    *
    * @param integer $offset
    * @return Timezone
    * @throws InvalidParamError
    */
    function getByOffset($offset) {
      if(isset($this) && instance_of($this, 'Timezones')) {
        $name = array_var($this->timezones, $offset);
        if(is_array($name)) {
          return new Timezone($offset, implode(', ', $name));
        } else {
          return new InvalidParamError('offset', $offset, "Timezone with offset of $offset seconds does not exist");
        } // if
      } else {
        $instance =& Timezones::instance();
        return $instance->getByOffset($offset);
      } // if
    } // getByOffset
    
    /**
    * Return timezones instance
    *
    * @param void
    * @return Timezones
    */
    function &instance() {
      static $instance;
      if(!instance_of($instance, 'Timezones')) {
        $instance = new Timezones();
      } // if
      return $instance;
    } // instance
  
  } // Timezones

?>