<?php

  // Extend calendar generator
  require_once DATETIME_LIB_PATH . '/CalendarGenerator.class.php';

  /**
   * User calendar generator
   *
   * Generator used to generate user profile calendars
   * 
   * @author Ilija Studen <ilija.studen@gmail.com>
   */
  class ProfileCalendarGenerator extends CalendarGenerator {
    
    /**
     * Selected user
     *
     * @var User
     */
    var $user;
    
    /**
     * Calendar data
     *
     * @var array
     */
    var $data;
    
    /**
     * Smarty instance used for template rendering
     *
     * @var Smarty
     */
    var $smarty;
    
    /**
     * Render calendar using Smarty
     *
     * @param void
     * @return string
     */
    function render() {
      $this->smarty =& Smarty::instance();
      return parent::render();
    } // render
  
    /**
     * Render single day
     *
     * @param DateValue $day
     * @param integer $weekday
     * @return string
     */
    function renderDay($day, $weekday) {
      $this->smarty->assign(array(
        'day' => $day,
        'day_url' => Calendar::getProfileDayUrl($this->user, $day->getYear(), $day->getMonth(), $day->getDay()),
        'day_data' => array_var($this->data, $this->year . '-' . $this->month . '-' . $day->getDay()),
      ));
      
      return $this->smarty->fetch(get_template_path('cell', null, 'calendar'));
    } // renderDay
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get user
     *
     * @param null
     * @return User
     */
    function getUser() {
      return $this->user;
    } // getUser
    
    /**
     * Set user value
     *
     * @param User $value
     * @return null
     */
    function setUser($value) {
      $this->user = $value;
    } // setUser
    
    /**
     * Get data
     *
     * @param null
     * @return array
     */
    function getData() {
      return $this->data;
    } // getData
    
    /**
     * Set data value
     *
     * @param array $value
     * @return null
     */
    function setData($value) {
      $this->data = $value;
    } // setData
  
  } // ProfileCalendarGenerator

?>