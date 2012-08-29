<?php
  
  /**
   * System level calendar
   *
   * @package activeCollab.modules.calendar
   * @subpackage controllers
   */
  class CalendarController extends ApplicationController  {
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = CALENDAR_MODULE;
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return CalendarController
     */
    function __construct($request) {
      parent::__construct($request);      
      $this->wireframe->addBreadCrumb(lang('Calendar'), assemble_url('dashboard_calendar'));
      $this->wireframe->current_menu_item = 'calendar';
    } // __construct
    
    /**
     * Calendar
     *
     * @param void
     * @return null
     */
    function index() {
      require_once CALENDAR_MODULE_PATH . '/models/generators/DashboardCalendarGenerator.class.php';
      
      if($this->request->get('month') && $this->request->get('year')) {
        $month = $this->request->get('month');
        $year = $this->request->get('year');
      } else {
        $today = new DateTimeValue(time() + get_user_gmt_offset());
        
        $month = $today->getMonth();
        $year = $today->getYear();
      } // if
      
      $first_weekday = UserConfigOptions::getValue('time_first_week_day', $this->logged_user);
      
      $generator = new DashboardCalendarGenerator($month, $year, $first_weekday);
      $generator->setData(Calendar::getActiveProjectsData($this->logged_user, $month, $year));
      
      $this->smarty->assign(array(
        'month' => $month,
        'year' => $year,
        'calendar' => $generator,
        'navigation_pattern' => Calendar::getDashboardMonthUrl('-YEAR-', '-MONTH-'),
      ));
    } // calendar
    
    /**
     * Show calendar day
     *
     * @param void
     * @return null
     */
    function day() {
      if($this->request->get('year') && $this->request->get('month') && $this->request->get('day')) {
        $day = new DateValue($this->request->get('year') . '-' . $this->request->get('month') . '-' . $this->request->get('day'));
      } else {
        $day = DateValue::now();
      } // if
      
      $this->wireframe->addBreadCrumb($day->getYear() . ' / ' . $day->getMonth(), Calendar::getDashboardMonthUrl($day->getYear(), $day->getMonth()));
      $objects = ProjectObjects::groupByProject(Calendar::getActiveProjectsDayData($this->logged_user, $day));
      
      $this->smarty->assign(array(
        'day' => $day,
        'groupped_objects' => $objects,
      ));
    } // day
    
    /**
     * Export to iCalendar
     *
     * @param void
     * @return null
     */
    function export() {
    	
    } // export
    
  } // CalendarController

?>