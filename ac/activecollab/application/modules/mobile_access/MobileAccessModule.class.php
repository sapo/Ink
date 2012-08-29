<?php

  /**
   * Mobile access module definition
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage models
   */
  class MobileAccessModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'mobile_access';
    
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
      
      // Main
      $router->map('mobile_access', 'm', array('controller' => 'mobile_access', 'action' => 'index'));
      
      $router->map('mobile_access_login', 'm/login', array('controller' => 'mobile_access_auth', 'action' => 'login'));
      $router->map('mobile_access_logout', 'm/logout', array('controller' => 'mobile_access_auth', 'action' => 'logout'));
      $router->map('mobile_access_forgot_password', 'm/forgot-password', array('controller' => 'mobile_access_auth', 'action' => 'forgot_password'));
      
      // Quick Add
      $router->map('mobile_access_quick_add', 'm/starred', array('controller' => 'mobile_access', 'action' => 'quick_add'));
      
      // Assignments
      $router->map('mobile_access_assignments', 'm/assignments', array('controller' => 'mobile_access', 'action' => 'assignments'));
      
      // Starred
      $router->map('mobile_access_starred', 'm/starred', array('controller' => 'mobile_access', 'action' => 'starred'));
      
      // People
      $router->map('mobile_access_people', 'm/people', array('controller' => 'mobile_access_people', 'action' => 'index'));
      $router->map('mobile_access_view_company', 'm/people/:object_id', array('controller' => 'mobile_access_people', 'action' => 'company'), array('object_id' => '\d+'));
      $router->map('mobile_access_view_user', 'm/people/users/:object_id', array('controller' => 'mobile_access_people', 'action' => 'user'), array('object_id' => '\d+'));
      
      
      // Projects
      $router->map('mobile_access_projects', 'm/projects', array('controller' => 'mobile_access_projects', 'action' => 'index'));
      
      // Project
      $router->map('mobile_access_view_project', 'm/project/:project_id', array('controller' => 'mobile_access_project', 'action' => 'index'), array('project_id' => '\d+'));
      
      // Project discusions
      $router->map('mobile_access_view_discussions', 'm/project/:project_id/discussions', array('controller' => 'mobile_access_project_discussions', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('mobile_access_view_discussion', 'm/project/:project_id/discussions/:object_id', array('controller' => 'mobile_access_project_discussions', 'action' => 'view'), array('project_id' => '\d+', 'object_id' => '\d+'));
      
      // Project milestones
      $router->map('mobile_access_view_milestones', 'm/project/:project_id/milestones', array('controller' => 'mobile_access_project_milestones', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('mobile_access_view_milestone', 'm/project/:project_id/milestones/:object_id', array('controller' => 'mobile_access_project_milestones', 'action' => 'view'), array('project_id' => '\d+', 'object_id' => '\d+'));
      
      // Project files
      $router->map('mobile_access_view_files', 'm/project/:project_id/files', array('controller' => 'mobile_access_project_files', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('mobile_access_view_file', 'm/project/:project_id/files/:object_id', array('controller' => 'mobile_access_project_files', 'action' => 'view'), array('project_id' => '\d+', 'object_id' => '\d+'));  
      
      // Project checklists
      $router->map('mobile_access_view_checklists', 'm/project/:project_id/checklists', array('controller' => 'mobile_access_project_checklists', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('mobile_access_view_checklist', 'm/project/:project_id/checklists/:object_id', array('controller' => 'mobile_access_project_checklists', 'action' => 'view'), array('project_id' => '\d+', 'object_id' => '\d+'));  
      
      // Project pages
      $router->map('mobile_access_view_pages', 'm/project/:project_id/pages', array('controller' => 'mobile_access_project_pages', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('mobile_access_view_page', 'm/project/:project_id/pages/:object_id', array('controller' => 'mobile_access_project_pages', 'action' => 'view'), array('project_id' => '\d+', 'object_id' => '\d+'));
      $router->map('mobile_access_view_page_version', 'm/project/:project_id/pages/:object_id/version/:version', array('controller' => 'mobile_access_project_pages', 'action' => 'version'), array('project_id' => '\d+', 'object_id' => '\d+', 'version' => '\d+'));
      
      // Project tickets
      $router->map('mobile_access_view_tickets', 'm/project/:project_id/tickets', array('controller' => 'mobile_access_project_tickets', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('mobile_access_view_ticket', 'm/project/:project_id/tickets/:object_id', array('controller' => 'mobile_access_project_tickets', 'action' => 'view'), array('project_id' => '\d+', 'object_id' => '\d+'));
      
      // Project timerecords
      $router->map('mobile_access_view_timerecords', 'm/project/:project_id/timerecords', array('controller' => 'mobile_access_project_timetracking', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('mobile_access_view_timerecord', 'm/project/:project_id/timerecords/:object_id', array('controller' => 'mobile_access_project_timetracking', 'action' => 'index'), array('project_id' => '\d+', 'object_id' => '\d+'));
        
      // Project subobjects
      $router->map('mobile_access_view_task', 'm/project/:project_id/task/:object_id', array('controller' => 'mobile_access', 'action' => 'view_parent_object'), array('project_id' => '\d+', 'object_id' => '\d+'));
      $router->map('mobile_access_view_comment', 'm/project/:project_id/comment/:object_id', array('controller' => 'mobile_access', 'action' => 'view_parent_object'), array('project_id' => '\d+', 'object_id' => '\d+'));
      $router->map('mobile_access_add_comment', 'm/project/:project_id/comment/add', array('controller' => 'mobile_access_project', 'action' => 'add_comment'));
        
      // Common
      $router->map('mobile_access_view_category', 'm/category/:object_id', array('controller' => 'mobile_access', 'action' => 'view_category', array('object_id' => '\d+')));
      $router->map('mobile_access_toggle_object_completed_status', 'm/project/:project_id/toggle-completed/:object_id', array('controller' => 'mobile_access', 'action' => 'toggle_completed', array('project_id' => '\d+', 'object_id' => '\d+')));
    } // defineRoutes
    
    // ---------------------------------------------------
    //  Names
    // ---------------------------------------------------
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Mobile Access');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Interface optimized for mobile devices (iPhone, Opera, S60 etc)');
    } // getDescription
    
    /**
     * Return uninstall module message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Data inserted using this module will not be deleted');
    } // getUninstallMessage
    
  }

?>