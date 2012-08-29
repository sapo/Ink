<?php

  /**
   * Single date time value
   * 
   * This class provides some handy methods for working with timestamps and extracting data from them
   *
   * @package angie.library.datetime
   */
  class DateTimeValue extends DateValue {
    
    /**
     * Cached hour value
     *
     * @var integer
     */
    var $hour;
    
    /**
     * Cached minutes value
     *
     * @var integer
     */
    var $minute;
    
    /**
     * Cached seconds value
     *
     * @var integer
     */
    var $second;
    
    // ---------------------------------------------------
    //  Static methods
    // ---------------------------------------------------
    
    /**
     * Returns current time object
     *
     * @param void
     * @return DateTimeValue
     */
    function now() {
      return new DateTimeValue(time());
    } // now
    
    /**
     * This function works like mktime, just it always returns GMT
     *
     * @param integer $hour
     * @param integer $minute
     * @param integer $second
     * @param integer $month
     * @param integer $day
     * @param integer $year
     * @return DateTimeValue
     */
    function make($hour, $minute, $second, $month, $day, $year) {
      return new DateTimeValue(mktime($hour, $minute, $second, $month, $day, $year));
    } // make
    
    /**
     * Make time from string using strtotime() function. This function will return null
     * if it fails to convert string to the time
     *
     * @param string $str
     * @return DateTimeValue
     */
    function makeFromString($str) {
      $timestamp = strtotime($str);
      return ($timestamp === false) || ($timestamp === -1) ? null : new DateTimeValue($timestamp);
    } // makeFromString
    
    /**
     * Return beginning of the month DateTimeValue
     *
     * @param integer $month
     * @param integer $year
     * @return DateTimeValue
     */
    function beginningOfMonth($month, $year) {
      return new DateTimeValue("$year-$month-1 00:00:00");
    } // beginningOfMonth
    
    /**
     * Return end of the month
     *
     * @param integer $month
     * @param integer $year
     * @return DateTimeValue
     */
    function endOfMonth($month, $year) {
      $reference = mktime(0, 0, 0, $month, 15, $year);
      $last_day = date('t', $reference);
      
      return new DateTimeValue("$year-$month-$last_day 23:59:59");
    } // endOfMonth
    
    // ---------------------------------------------------
    //  Formating
    // ---------------------------------------------------
    
    /**
     * Return datetime formated in MySQL datetime format
     *
     * @param void
     * @return string
     */
    function toMySQL() {
      return $this->format(DATETIME_MYSQL);
    } // toMySQL
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Break timestamp into its parts and set internal variables
     *
     * @param void
     * @return null
     */
    function parse() {
      $this->date_data = getdate($this->timestamp);
      
      if($this->date_data) {
        $this->year   = (integer) $this->date_data['year'];
        $this->month  = (integer) $this->date_data['mon'];
        $this->day    = (integer) $this->date_data['mday'];
        $this->hour   = (integer) $this->date_data['hours'];
        $this->minute = (integer) $this->date_data['minutes'];
        $this->second = (integer) $this->date_data['seconds'];
      } // if
    } // parse
    
    /**
     * Update internal timestamp based on internal param values
     *
     * @param void
     * @return null
     */
    function setTimestampFromAttributes() {
      $this->setTimestamp(mktime(
        $this->hour, 
        $this->minute, 
        $this->second, 
        $this->month, 
        $this->day, 
        $this->year
      )); // setTimestamp
    } // setTimestampFromAttributes
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return hour
     *
     * @param void
     * @return integer
     */
    function getHour() {
      return $this->hour;
    } // getHour
    
    /**
     * Set hour value
     *
     * @param integer $value
     * @return null
     */
    function setHour($value) {
      $this->hour = (integer) $value;
      $this->setTimestampFromAttributes();
    } // setHour
    
    /**
     * Return minute
     *
     * @param void
     * @return integer
     */
    function getMinute() {
      return $this->minute;
    } // getMinute
    
    /**
     * Set minutes value
     *
     * @param integer $value
     * @return null
     */
    function setMinute($value) {
      $this->minute = (integer) $value;
      $this->setTimestampFromAttributes();
    } // setMinute
    
    /**
     * Return seconds
     *
     * @param void
     * @return integer
     */
    function getSecond() {
      return $this->second;
    } // getSecond
    
    /**
     * Set seconds
     *
     * @param integer $value
     * @return null
     */
    function setSecond($value) {
      $this->second = (integer) $value;
      $this->setTimestampFromAttributes();
    } // setSecond
  
  } // DateTimeValue

?>