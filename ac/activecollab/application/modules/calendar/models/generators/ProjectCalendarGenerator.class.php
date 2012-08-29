<?php

  // Extend calendar generator
  require_once DATETIME_LIB_PATH . '/CalendarGenerator.class.php';

  /**
   * Project calendar generator
   *
   * Extend calendar generator and implement rendering of project items
   * 
   * @author Ilija Studen <ilija.studen@gmail.com>
   */
  class ProjectCalendarGenerator extends CalendarGenerator {
    
    /**
     * Project
     *
     * @var Project
     */
    var $project;
    
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
        'day_url' => Calendar::getProjectDayUrl($this->project, $day->getYear(), $day->getMonth(), $day->getDay()),
        'day_data' => array_var($this->data, $this->year . '-' . $this->month . '-' . $day->getDay()),
      ));
      
      return $this->smarty->fetch(get_template_path('cell', null, 'calendar'));
    } // renderDay
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get project
     *
     * @param null
     * @return Project
     */
    function getProject() {
      return $this->project;
    } // getProject
    
    /**
     * Set project value
     *
     * @param Project $value
     * @return null
     */
    function setProject($value) {
      $this->project = $value;
    } // setProject
    
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
  
  } // ProjectCalendarGenerator

?>