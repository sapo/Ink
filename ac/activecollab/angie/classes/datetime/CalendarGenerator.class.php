<?php

  /**
   * Base calendar generator
   *
   * Purpose of calendar generator is to provide foundation for generation of 
   * any type of month calendar. It can (and should) be inherited and extended 
   * with functions for a specific calendar
   * 
   * @package angie.library.datetime
   */
  class CalendarGenerator extends AngieObject {
    
    /**
     * Month that need to be rendered
     *
     * @var integer
     */
    var $month;
    
    /**
     * Month of this year
     *
     * @var integer
     */
    var $year;
    
    /**
     * First day in the week
     * 
     * 0 is Sunday, 6 is Saturday
     *
     * @var integer
     */
    var $first_weekday = 0;
    
    /**
     * Array of day names (used by renderDayHeader)
     *
     * @var array
     */
    var $day_names = array();
  
    /**
     * Constructor
     *
     * @param integer $month
     * @param integer $year
     * @param integer $first_weekday
     * @return CalendarGenerator
     */
    function __construct($month, $year, $first_weekday = 0) {
      $this->day_names = array(
        0 => lang('Sunday'),
        1 => lang('Monday'),
        2 => lang('Tuesday '),
        3 => lang('Wednesday'),
        4 => lang('Thursday'),
        5 => lang('Friday'),
        6 => lang('Saturday'),
      );
      
      $this->month = (integer) $month;
      $this->year = (integer) $year;
      $this->first_weekday = (integer) $first_weekday;
    } // __construct
    
    /**
     * Render calendar
     *
     * @param void
     * @return string
     */
    function render() {
      $first_day = DateTimeValue::beginningOfMonth($this->month, $this->year);
      $last_day = DateTimeValue::endOfMonth($this->month, $this->year);
      
      // ---------------------------------------------------
      //  Header
      // ---------------------------------------------------
      
      $result = "\n<table class=\"calendar\">\n<thead>\n<tr>\n";
      
      $first_day_num = 0;
      $current_day = $this->first_weekday;
      for($i = 0; $i < 7; $i++) {
        $real_day = $current_day > 6 ? $current_day - 7 : $current_day; // get real day num (we have a small offset with start day setting)
        $class = $real_day == 0 || $real_day == 6 ? 'weekend' : 'weekday'; // class for row
        $current_day++;
        if($real_day == $first_day->getWeekday()) {
          $first_day_num = $real_day; // we got where we need to start...
        } // if
        
        $result .= "<th class=\"$class\">" . $this->renderDayHeader($real_day) . "</th>\n";
      } // for
      
      $result .= "</tr>\n</thead>\n";
      
      // ---------------------------------------------------
      //  Body
      // ---------------------------------------------------
      
      if($this->first_weekday > $first_day_num) {
        $left_span = 7 - ($this->first_weekday - $first_day_num);
      } else {
        $left_span = $first_day_num - $this->first_weekday;
      } // if
      
      $weekday = $left_span;
      
      $result .= "<tbody>\n<tr>\n";
      if($left_span) {
        $result .= "<td class=\"previousMonth\" colspan=\"$left_span\"></td>\n";
      } // if
      
      for($i = 1; $i <= $last_day->getDay(); $i++) {
        $day = new DateValue($this->year . '-' . $this->month . '-' . $i);
        
        if($weekday == 0) {
          $result .= "<tr>\n";
        } // if
        
        $result .= $this->renderDay($day, $weekday);
        
        if($weekday == 6) {
          $result .= "</tr>\n";
        } // if
        
        $weekday = $weekday == 6 ? 0 : $weekday + 1;
      } // for
      
      // Close row if we ended in the middle of it
      if($weekday > 0) {
        $right_span = 7 - $weekday;
        $result .= "<td class=\"nextMonth\" colspan=\"$right_span\"></td>\n</tr>";
      } // if
      
      // ---------------------------------------------------
      //  Close and done
      // ---------------------------------------------------
      
      return $result . "</tbody>\n</table>";
    } // render
    
    /**
     * Return content for day header cell
     *
     * @param integer $day
     * @return string
     */
    function renderDayHeader($day) {
      return $this->day_names[$day];
    } // renderDayHeader
    
    /**
     * Render specific day
     * 
     * $weekday is numeric representation of day in a week. 0 is Sunday -> 6 is 
     * Saturday
     *
     * @param DateValue $day
     * @param integer $weekday
     * @return null
     */
    function renderDay($day, $weekday) {
      $class = $weekday == 0 || $weekday == 6 ? 'weekend' : 'weekday';
      return '<td class="' . $class . '">' . $day->getDay() . '</td>';
    } // renderDay
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get first_weekday
     *
     * @param null
     * @return integer
     */
    function getFirstWeekday() {
      return $this->first_weekday;
    } // getFirstWeekday
    
    /**
     * Set first_weekday value
     *
     * @param integer $value
     * @return null
     */
    function setFirstWeekday($value) {
      $this->first_weekday = $value;
    } // setFirstWeekday
    
    /**
     * Get month
     *
     * @param null
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
      $this->month = $value;
    } // setMonth
    
    /**
     * Get year
     *
     * @param null
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
      $this->year = $value;
    } // setYear
  
  } // CalendarGenerator

?>