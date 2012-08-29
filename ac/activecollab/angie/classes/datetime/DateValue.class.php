<?php

  /**
   * Data value object
   *
   * Instance of this class represents single date (time part is ignored)
   * 
   * @package angie.library.datetime
   */
  class DateValue extends AngieObject {
  
    /**
     * Internal timestamp value
     *
     * @var integer
     */
    var $timestamp;
    
    /**
     * Cached day value
     *
     * @var integer
     */
    var $day;
    
    /**
     * Cached month value
     *
     * @var integer
     */
    var $month;
    
    /**
     * Cached year value
     *
     * @var integer
     */
    var $year;
    
    /**
     * Date data, result of getdate() function
     *
     * @var array
     */
    var $date_data;
    
    // ---------------------------------------------------
    //  Static methods
    // ---------------------------------------------------
    
    /**
     * Returns today object
     *
     * @param void
     * @return DateValue
     */
    function now() {
      return new DateValue(time());
    } // now
    
    /**
     * This function works like mktime, just it always returns GMT
     *
     * @param integer $month
     * @param integer $day
     * @param integer $year
     * @return DateValue
     */
    function make($month, $day, $year) {
      return new DateValue(mktime(0, 0, 0, $month, $day, $year));
    } // make
    
    /**
     * Make time from string using strtotime() function. This function will 
     * return null if it fails to convert string to the time
     *
     * @param string $str
     * @return DateValue
     */
    function makeFromString($str) {
      $timestamp = strtotime($str);
      return ($timestamp === false) || ($timestamp === -1) ? null : new DateValue($timestamp);
    } // makeFromString
    
    // ---------------------------------------------------
    //  Instance methods
    // ---------------------------------------------------
  
    /**
     * Construct the DateValue
     *
     * @param integer $timestamp
     * @return DateValue
     */
    function __construct($timestamp = null) {
      if($timestamp === null) {
        $timestamp = time();
      } elseif(is_string($timestamp)) {
        $timestamp = strtotime($timestamp);
      } // if
      $this->setTimestamp($timestamp);
    } // __construct
    
    /**
     * Advance for specific time
     * 
     * If $mutate is true value of this object will be changed. If false a new 
     * DateValue or DateTimeValue instance will be returned with timestamp 
     * moved for $input number of seconds
     *
     * @param integer $input
     * @param boolean $mutate
     * @return DateTimeValue
     */
    function advance($input, $mutate = true) {
      $timestamp = (integer) $input;
      
      if($mutate) {
        $this->setTimestamp($this->getTimestamp() + $timestamp);
      } else {
        if(instance_of($this, 'DateTimeValue')) {
          return new DateTimeValue($this->getTimestamp() + $timestamp);
        } else {
          return new DateValue($this->getTimestamp() + $timestamp);
        } // if
      } // if
    } // advance
    
    /**
     * This function will return true if this day is today
     *
     * @param integer $offset
     * @return boolean
     */
    function isToday($offset = null) {
      $today = new DateTimeValue(time() + $offset);
      $today->beginningOfDay();
      
      return $this->getDay()   == $today->getDay() &&
             $this->getMonth() == $today->getMonth() &&
             $this->getYear()  == $today->getYear();
    } // isToday
    
    /**
     * This function will return true if this date object is yesterday
     *
     * @param integer $offset
     * @return boolean
     */
    function isYesterday($offset = null) {
      return $this->isToday($offset - 86400);
    } // isYesterday
    
    /**
     * Returns true if this date object is tomorrow
     *
     * @param integer $offset
     * @return boolean
     */
    function isTomorrow($offset = null) {
      return $this->isToday($offset + 86400);
    } // isTomorrow
    
    /**
     * Is this a weekend day
     *
     * @param void
     * @return boolean
     */
    function isWeekend() {
      $weekday = $this->getWeekday();
      return $weekday == 0 || $weekday == 6;
    } // isWeekend
    
    /**
     * This function will move interlan data to the beginning of day and return 
     * modified object. 
     *
     * @param void
     * @return DateTimeValue
     */
    function beginningOfDay() {
      return new DateTimeValue(mktime(0, 0, 0, $this->getMonth(), $this->getDay(), $this->getYear()));
    } // beginningOfDay
    
    /**
     * This function will set hours, minutes and seconds to 23:59:59 and return 
     * this object.
     * 
     * If you wish to get end of this day simply type:
     *
     * @param void
     * @return DateTimeValue
     */
    function endOfDay() {
      return new DateTimeValue(mktime(23, 59, 59, $this->getMonth(), $this->getDay(), $this->getYear()));
    } // endOfDay
    
    /**
     * Return beginning of week object
     *
     * @param boolean $first_day_sunday
     * @return DateTimeValue
     */
    function beginningOfWeek($first_day_sunday = true) {
      $weekday = $this->getWeekday();
      
      $result = $this->beginningOfDay();
      
      if($first_day_sunday) {
        return $result->advance($weekday * -86400, false);
      } else {
        return $result->advance(($weekday - 1) * -86400, false);
      } // if
    } // beginningOfWeek
    
    /**
     * Return end of week date time object
     *
     * @param boolean $first_day_sunday
     * @return DateTimeValue
     */
    function endOfWeek($first_day_sunday = true) {
    	$beginning = $this->beginningOfWeek($first_day_sunday);
    	return $beginning->advance(604799, false);
    } // endOfWeek
    
    /**
     * Calculate difference in days between this day and $second date
     *
     * @param DateValue $second
     * @return integer
     */
    function daysBetween($second) {
      if(!instance_of($second, 'DateValue')) {
        return new InvalidParamError('second', $second, '$second is expected to be instance of DateValue class');
      } // if
      
      $first_timestamp = mktime(12, 0, 0, $this->getMonth(), $this->getDay(), $this->getYear());
      $second_timestamp = mktime(12, 0, 0, $second->getMonth(), $second->getDay(), $second->getYear());
      
      if($first_timestamp == $second_timestamp) {
        return 0;
      } // if
      
      $diff = (integer) abs($first_timestamp - $second_timestamp);
      if($diff < 86400) {
        return $this->getDay() != $second->getDay() ? 1 : 0;
      } else {
        return (integer) round($diff / 86400);
      } // if
    } // daysBetween
    
    // ---------------------------------------------------
    //  Format to some standard values
    // ---------------------------------------------------
    
    /**
     * Return formated datetime
     *
     * @param string $format
     * @return string
     */
    function format($format) {
      return date($format, $this->getTimestamp());
    } // format
    
    /**
     * Return datetime formated in MySQL datetime format
     *
     * @param void
     * @return string
     */
    function toMySQL() {
      return $this->format(DATE_MYSQL);
    } // toMySQL
    
    /**
     * Return ISO8601 formated time
     *
     * @param void
     * @return string
     */
    function toISO8601() {
      return $this->format(DATE_ISO8601);
    } // toISO
    
    /**
     * Return atom formated time (W3C format)
     *
     * @param void
     * @return string
     */
    function toAtom() {
      return $this->format(DATE_ATOM);
    } // toAtom
    
    /**
     * Return RSS format
     *
     * @param void
     * @return string
     */
    function toRSS() {
      return $this->format(DATE_RSS);
    } // toRSS
    
    /**
     * Return iCalendar formated date and time
     *
     * @param void
     * @return string
     */
    function toICalendar() {
      return $this->format('Ymd\THis\Z');
    } // toICalendar
    
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
        $this->year  = (integer) $this->date_data['year'];
        $this->month = (integer) $this->date_data['mon'];
        $this->day   = (integer) $this->date_data['mday'];
      } // if
    } // parse
    
    /**
     * Update internal timestamp based on internal param values
     *
     * @param void
     * @return null
     */
    function setTimestampFromAttributes() {
      $this->setTimestamp(mktime(0, 0, 0, $this->month, $this->day, $this->year));
    } // setTimestampFromAttributes
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get timestamp
     *
     * @param null
     * @return integer
     */
    function getTimestamp() {
      return $this->timestamp;
    } // getTimestamp
    
    /**
     * Set timestamp value
     *
     * @param integer $value
     * @return null
     */
    function setTimestamp($value) {
      $this->timestamp = $value;
      $this->parse();
    } // setTimestamp
    
    /**
     * Return year
     *
     * @param void
     * @return integer
     */
    function getYear() {
      return $this->year;
    } // getYear
    
    /**
     * Set year value
     *
     * @param integer $value
     * @return null
     */
    function setYear($value) {
      $this->year = (integer) $year;
      $this->setTimestampFromAttributes();
    } // setYear
    
    /**
     * Return numberic representation of month
     *
     * @param void
     * @return integer
     */
    function getMonth() {
      return $this->month;
    } // getMonth
    
    /**
     * Set month value
     *
     * @param integer $value
     * @return null
     */
    function setMonth($value) {
      $this->month = (integer) $value;
      $this->setTimestampFromAttributes();
    } // setMonth
    
    /**
     * Return days
     *
     * @param void
     * @return integer
     */
    function getDay() {
      return $this->day;
    } // getDay
    
    /**
     * Set day value
     *
     * @param integer $value
     * @return null
     */
    function setDay($value) {
      $this->day = (integer) $value;
      $this->setTimestampFromAttributes();
    } // setDay
    
    /**
     * Return weeekday for given date
     *
     * @param void
     * @return integer
     */
    function getWeekday() {
      return array_var($this->date_data, 'wday');
    } // getWeekday
    
    /**
     * Return yearday from given date
     *
     * @param void
     * @return integer
     */
    function getYearday() {
      return array_var($this->date_data, 'yday');
    } // getYearday
    
    /**
     * Return ISO value
     *
     * @param void
     * @return string
     */
    function __toString() {
      return $this->toMySQL();
    } // __toString
    
  } // DateValue

?>