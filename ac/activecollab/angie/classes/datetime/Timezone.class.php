<?php

  /**
   * Timezone class
   * 
   * Single timezone instance where timezone is defined with a name and offset in seconds
   *
   *  @package angie.library.datetime
   */
  class Timezone extends AngieObject {
    
    /**
    * GMT offset in seconds. Can be negative
    *
    * @var integer
    */
    var $offset;
    
    /**
    * Timezone name
    *
    * @var string
    */
    var $name;
  
    /**
    * Constructor new timezone object
    *
    * @param integer $offset
    * @param string $name
    * @return Timezone
    */
    function __construct($offset, $name) {
      $this->setOffset($offset);
      $this->setName($name);
    } // __construct
    
    /**
    * Return formatted offset (in hours)
    * 
    * $separator is used as a separator between hour and minutes part of 
    * formatted value
    *
    * @param string $separator
    * @return string
    */
    function getFormattedOffset($separator = ':') {
      if($this->offset == 0) {
        return '';
      } // if
      
      $sign = $this->offset > 0 ? '+' : '-';
      $hours = abs($this->offset) / 3600;
      if($hours < 10) {
        $hours = '0' . floor($hours);
      } // if
      $minutes = (abs($this->offset) % 3600) / 60;
      if($minutes < 10) {
        $minutes = '0' . $minutes;
      } // if
      
      return $sign . $hours . $separator . $minutes;
    } // getFormattedOffset
    
    /**
    * Return string representation of this timezone
    *
    * @param void
    * @return string
    */
    function __toString() {
      return '(GMT' . $this->getFormattedOffset() . ') ' . $this->getName();
    } // __toString
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Get offset
    *
    * @param null
    * @return integer
    */
    function getOffset() {
      return $this->offset;
    } // getOffset
    
    /**
    * Set offset value
    *
    * @param integer $value
    * @return null
    */
    function setOffset($value) {
      $this->offset = $value;
    } // setOffset
    
    /**
    * Get name
    *
    * @param null
    * @return string
    */
    function getName() {
      return $this->name;
    } // getName
    
    /**
    * Set name value
    *
    * @param string $value
    * @return null
    */
    function setName($value) {
      $this->name = $value;
    } // setName
  
  } // Timezone

?>