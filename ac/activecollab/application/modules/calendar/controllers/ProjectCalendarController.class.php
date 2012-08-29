<?php
  
  // Extend projects controller
  use_controller('project', SYSTEM_MODULE);
  
  /**
   * Project calendar controller
   *
   * @package activeCollab.modules.calendar
   * @subpackage controllers
   */
  class ProjectCalendarController extends ProjectController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'project_calendar';
    
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
     * @return ProjectCalendarController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->wireframe->addBreadCrumb(lang('Calendar'), Calendar::getProjectCalendarUrl($this->active_project));
    } // __construct
    
    /**
     * Index
     *
     * @param voi
     * @return null
     */
    function index() {
      require_once CALENDAR_MODULE_PATH . '/models/generators/ProjectCalendarGenerator.class.php';
      
      $today = new DateTimeValue(time() + get_user_gmt_offset());
      if($this->request->get('month') && $this->request->get('year')) {
        $month = $this->request->get('month');
        $year = $this->request->get('year');
      } else {
        $month = $today->getMonth();
        $year = $today->getYear();
      } // if
      
      $first_weekday = UserConfigOptions::getValue('time_first_week_day', $this->logged_user);
      
      $generator = new ProjectCalendarGenerator($month, $year, $first_weekday);
      $generator->setProject($this->active_project);
      $generator->setData(Calendar::getProjectData($this->logged_user, $this->active_project, $month, $year));
      
      $this->smarty->assign(array(
        'month'       => $month,
        'year'        => $year,
        'calendar'    => $generator,
        'page_tab' => 'calendar',
        'navigation_pattern' => Calendar::getProjectMonthUrl($this->active_project, '-YEAR-', '-MONTH-'),
      ));
    } // index
    
    /**
     * Show events for a given day
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
      
      $this->wireframe->addBreadCrumb($day->getYear() . ' / ' . $day->getMonth(), Calendar::getProjectMonthUrl($this->active_project, $day->getYear(), $day->getMonth()));
      $objects = Calendar::getProjectDayData($this->logged_user, $this->active_project, $day);
      
    	$this->smarty->assign(array(
        'day' => $day,
        'objects' => $objects,
      ));
    } // day
    
  } // ProjectCalendarController
?>