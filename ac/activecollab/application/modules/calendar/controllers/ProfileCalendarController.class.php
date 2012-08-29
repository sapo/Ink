<?php

  // Extends profile controller
  use_controller('users', SYSTEM_MODULE);
  
  /**
   * User calendar controller
   *
   * @package activeCollab.modules.calendar
   * @subpackage controllers
   */
  class ProfileCalendarController extends UsersController {
    
    /**
     * Profile calendar controller
     *
     * @var string
     */
    var $controller_name = 'profile_calendar';
    
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
     * @return UsersCalendarController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if(!can_access_profile_calendar($this->logged_user, $this->active_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $this->wireframe->addBreadCrumb(lang('Calendar'), Calendar::getProfileCalendarUrl($this->active_user));
    } // __construct
    
    /**
     * Index
     *
     * @param voi
     * @return null
     */
    function index() {
      require_once CALENDAR_MODULE_PATH . '/models/generators/ProfileCalendarGenerator.class.php';
      
      $today = new DateTimeValue(time() + get_user_gmt_offset());
      if($this->request->get('month') && $this->request->get('year')) {
        $month = $this->request->get('month');
        $year = $this->request->get('year');
      } else {
        $month = $today->getMonth();
        $year = $today->getYear();
      } // if
      
      $first_weekday = UserConfigOptions::getValue('time_first_week_day', $this->logged_user);
      
      $generator = new ProfileCalendarGenerator($month, $year, $first_weekday);
      $generator->setUser($this->active_user);
      $generator->setData(Calendar::getUserData($this->active_user, $month, $year));
      
      $this->smarty->assign(array(
        'month' => $month,
        'year' => $year,
        'calendar' => $generator,
        'navigation_pattern' => Calendar::getProfileMonthUrl($this->active_user, '-YEAR-', '-MONTH-'),
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
      
      $this->wireframe->addBreadCrumb($day->getYear() . ' / ' . $day->getMonth(), Calendar::getProfileMonthUrl($this->active_user, $day->getYear(), $day->getMonth()));
      $objects = ProjectObjects::groupByProject(Calendar::getUserDayData($this->active_user, $day));
            
      $this->smarty->assign(array(
        'day' => $day,
        'groupped_objects' => $objects,
      ));
    } // day
    
    /**
     * Render iCalendar feed
     *
     * @param void
     * @return null
     */
    function ical() {
      $filter = new AssignmentFilter();
      $filter->setUserFilter(USER_FILTER_SELECTED);
      $filter->setUserFilterData(array($this->active_user->getId()));
      $filter->setProjectFilter(PROJECT_FILTER_ACTIVE);
  		
  		render_icalendar(
  		  lang(":user's calendar", array('user' => $this->active_user->getDisplayName())),
  		  AssignmentFilters::executeFilter($this->logged_user, $filter, false)
  		);
  		die();
    } // ical
    
    /**
     * Show iCalendar subscribe page
     *
     * @param void
     * @return null
     */
    function ical_subscribe() {
      $this->wireframe->print_button = false;
      
      $ical_url = assemble_url('profile_calendar_ical', array(
        'company_id' => $this->active_user->getCompanyId(),
        'user_id' => $this->active_user->getId(),
        'token' => $this->logged_user->getToken(true),
      ));
      
      $ical_subscribe_url = str_replace(array('http://', 'https://'), array('webcal://', 'webcal://'), $ical_url);
      
      $this->smarty->assign(array(
        'ical_url' => $ical_url,
        'ical_subscribe_url' => $ical_subscribe_url
      ));
    } // ical_subscribe
    
  }

?>