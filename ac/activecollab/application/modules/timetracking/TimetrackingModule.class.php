<?php

  /**
   * Timetracking module definition
   *
   * @package activeCollab.modules.timetracking
   * @subpackage models
   */
  class TimetrackingModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'timetracking';
    
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
      
      // Global timetracking
      $router->map('global_time', 'time', array('controller' => 'global_timetracking'));
      $router->map('global_time_report_add', 'time/add', array('controller' => 'global_timetracking', 'action' => 'add'));
      $router->map('global_time_report', 'time/:report_id', array('controller' => 'global_timetracking', 'action' => 'report'), array('report_id' => '\d+'));
      $router->map('global_time_report_export', 'time/:report_id/export', array('controller' => 'global_timetracking', 'action' => 'report_export'), array('report_id' => '\d+'));
      $router->map('global_time_report_edit', 'time/:report_id/edit', array('controller' => 'global_timetracking', 'action' => 'edit'), array('report_id' => '\d+'));
      $router->map('global_time_report_delete', 'time/:report_id/delete', array('controller' => 'global_timetracking', 'action' => 'delete'), array('report_id' => '\d+'));
      $router->map('global_time_report_partial_generator', 'time/partial-generator', array('controller' => 'global_timetracking', 'action' => 'partial_generator'));
      
      // Project timetracking
      $router->map('project_time', 'projects/:project_id/time', array('controller' => 'timetracking', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('project_time_day', 'projects/:project_id/time/day/:day', array('controller' => 'timetracking', 'action' => 'day'), array('project_id' => '\d+'));
      $router->map('project_time_add', 'projects/:project_id/time/add', array('controller' => 'timetracking', 'action' => 'add'), array('project_id' => '\d+'));
      $router->map('project_time_quick_add', 'projects/:project_id/time/quick-add', array('controller' => 'timetracking', 'action' => 'quick_add'), array('project_id' => '\d+'));
      $router->map('project_time_record', 'projects/:project_id/time/:time_id', array('controller' => 'timetracking', 'action' => 'view'), array('project_id' => '\d+', 'time_id' => '\d+'));
      $router->map('project_time_edit', 'projects/:project_id/time/:time_id/edit', array('controller' => 'timetracking', 'action' => 'edit'), array('project_id' => '\d+', 'time_id' => '\d+'));
      $router->map('project_time_export', 'projects/:project_id/time/export', array('controller' => 'timetracking', 'action' => 'export'), array('project_id' => '\d+'));
      $router->map('project_time_update_billed_state', 'projects/:project_id/time/:time_id/update-billed-state', array('controller' => 'timetracking', 'action' => 'update_billed_state'), array('project_id' => '\d+', 'time_id' => '\d+'));
      
      $router->map('project_time_mass_update', 'projects/:project_id/time/mass-update', array('controller' => 'timetracking', 'action' => 'mass_update'), array('project_id' => '\d+'));
      
      $router->map('project_time_reports', 'projects/:project_id/time/reports', array('controller' => 'project_time_reports', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('project_time_report', 'projects/:project_id/time/reports/:report_id', array('controller' => 'project_time_reports', 'action' => 'report'), array('project_id' => '\d+', 'report_id' => '\d+'));
      $router->map('project_time_report_export', 'projects/:project_id/time/reports/:report_id/export', array('controller' => 'project_time_reports', 'action' => 'report_export'), array('project_id' => '\d+', 'report_id' => '\d+'));
      
      $router->map('timetracking_updates_module', 'admin/modules/timetracking', array('controller' => 'timetracking_module_admin', 'action' => 'module', 'module_name' => 'timetracking'));
    } // defineRoutes
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
      $events->listen('on_get_project_object_types', 'on_get_project_object_types');
      $events->listen('on_project_tabs', 'on_project_tabs');
      $events->listen('on_quick_add', 'on_quick_add');
      $events->listen('on_build_menu', 'on_build_menu');
      $events->listen('on_project_export', 'on_project_export');
      $events->listen('on_user_cleanup', 'on_user_cleanup');
      $events->listen('on_project_permissions', 'on_project_permissions');
      $events->listen('on_system_permissions', 'on_system_permissions');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  (Un)Install
    // ---------------------------------------------------
    
    /**
     * Install module
     *
     * @param void
     * @return boolean
     */
    function install() {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'ENGINE=InnoDB' : 'ENGINE=MyISAM';
      $default_charset = defined('DB_CHARSET') && DB_CHARSET == 'utf8' ? 'DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' : '';
      
      db_execute("CREATE TABLE " . TABLE_PREFIX . "time_reports (
        id smallint(5) unsigned NOT NULL auto_increment,
        name varchar(50) NOT NULL,
        group_name varchar(50) NOT NULL,
        is_default tinyint(1) unsigned NOT NULL default '0',
        user_filter enum('anybody','logged_user','company','selected') NOT NULL default 'anybody',
        user_filter_data text,
        billable_filter enum('all','billable','not_billable','billable_billed','billable_not_billed', 'pending_payment') NOT NULL default 'all',
        date_filter enum('all','today','last_week','this_week','last_month','this_month','selected_date','selected_range') NOT NULL default 'all',
        date_from date default NULL,
        date_to date default NULL,
        sum_by_user tinyint(1) unsigned NOT NULL,
        PRIMARY KEY  (id)
      ) $engine $default_charset");
      
      db_execute("INSERT INTO " . TABLE_PREFIX . "time_reports (id, name, group_name, is_default, user_filter, user_filter_data, billable_filter, date_filter, date_from, date_to, sum_by_user) VALUES 
        (1, 'Last week', 'General', 1, 'anybody', 'N;', 'all', 'last_week', NULL, NULL, 0),
        (2, 'Last week, summarized', 'General', 0, 'anybody', 'N;', 'all', 'last_week', NULL, NULL, 1),
        (3, 'Last month', 'General', 0, 'anybody', 'N;', 'all', 'last_month', NULL, NULL, 0),
        (4, 'Last month, summarized', 'General', 0, 'anybody', 'N;', 'all', 'last_month', NULL, NULL, 1);");
      
      return parent::install();
    } // install
    
    /**
     * Uninstall this module
     *
     * @param void
     * @return boolean
     */
    function uninstall() {
      db_execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'time_reports');
      
      return parent::uninstall();
    } // uninstall
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Time Tracking');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Adds timetracking support to projects. Reports are available globally and per project');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All time records and reports will be deleted');
    } // getUninstallMessage
    
  }

?>