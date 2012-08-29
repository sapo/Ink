<?php

  /**
   * Calendar module definition
   *
   * @package activeCollab.modules.calendar
   * @subpackage models
   */
  class CalendarModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'calendar';
    
    /**
     * Is system module flag
     *
     * @var boolean
     */
    var $is_system = false;
    
    /**
     * Module version
     *
     * @var string
     */
    var $version = '2.0';
    
    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------
    
    /**
     * Define module routes
     *
     * @param Router $r
     * @return null
     */
    function defineRoutes(&$router) {
      $router->map('dashboard_calendar', 'dashboard/calendar', array('controller' => 'calendar', 'action' => 'index'));
      $router->map('dashboard_calendar_year', 'dashboard/calendar/:year', array('controller' => 'calendar', 'action' => 'index'), array('year' => '\d+'));
      $router->map('dashboard_calendar_month', 'dashboard/calendar/:year/:month', array('controller' => 'calendar', 'action' => 'index'), array('year' => '\d+', 'month' => '\d+'));
      $router->map('dashboard_calendar_day', 'dashboard/calendar/:year/:month/:day', array('controller' => 'calendar', 'action' => 'day'), array('year' => '\d+', 'month' => '\d+', 'day' => '\d+'));
      
      // Project Calendar
      $router->map('project_calendar', 'projects/:project_id/calendar', array('controller' => 'project_calendar', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('project_calendar_year', 'projects/:project_id/calendar/:year', array('controller' => 'project_calendar', 'action' => 'index'), array('project_id' => '\d+', 'year' => '\d+'));
      $router->map('project_calendar_month', 'projects/:project_id/calendar/:year/:month', array('controller' => 'project_calendar', 'action' => 'index'), array('project_id' => '\d+', 'year' => '\d+', 'month' => '\d+'));
      $router->map('project_calendar_day', 'projects/:project_id/calendar/:year/:month/:day', array('controller' => 'project_calendar', 'action' => 'day'), array('project_id' => '\d+', 'year' => '\d+', 'month' => '\d+', 'day' => '\d+'));
    
      // Profile Calendar
      $router->map('profile_calendar', 'people/:company_id/users/:user_id/calendar', array('controller' => 'profile_calendar', 'action' => 'index'), array('user_id' => '\d+'));
      $router->map('profile_calendar_year', 'people/:company_id/users/:user_id/calendar/:year', array('controller' => 'profile_calendar', 'action' => 'index'), array('user_id' => '\d+', 'year' => '\d+'));
      $router->map('profile_calendar_month', 'people/:company_id/users/:user_id/calendar/:year/:month', array('controller' => 'profile_calendar', 'action' => 'index'), array('user_id' => '\d+', 'year' => '\d+', 'month' => '\d+'));
      $router->map('profile_calendar_day', 'people/:company_id/users/:user_id/calendar/:year/:month/:day', array('controller' => 'profile_calendar', 'action' => 'day'), array('user_id' => '\d+', 'year' => '\d+', 'month' => '\d+', 'day' => '\d+'));
      $router->map('profile_calendar_ical', 'people/:company_id/users/:user_id/calendar/ical', array('controller' => 'profile_calendar', 'action' => 'ical'), array('user_id' => '\d+'));
      $router->map('profile_calendar_ical_subscribe', 'people/:company_id/users/:user_id/calendar/ical-subscribe', array('controller' => 'profile_calendar', 'action' => 'ical_subscribe'), array('user_id' => '\d+'));
    } // defineRoutes
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
      $events->listen('on_build_menu', 'on_build_menu');
      $events->listen('on_project_tabs', 'on_project_tabs');
      $events->listen('on_user_options', 'on_user_options');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  Names
    // ---------------------------------------------------
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Calendar');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Enables viewing project data in a calendar. Global calendar is available plus a calendar for each project and user');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Data that is shown in the calendars will not be deleted');
    } // getUninstallMessage
    
  }

?>