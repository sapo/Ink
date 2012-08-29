<?php

  /**
   * Milestones module definition
   *
   * @package activeCollab.modules.milestones
   * @subpackage models
   */
  class MilestonesModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'milestones';
    
    /**
     * Is system module flag
     *
     * @var boolean
     */
    var $is_system = true;
    
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
      $router->map('project_milestones', 'projects/:project_id/milestones', array('controller' => 'milestones', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('project_milestones_archive', 'projects/:project_id/milestones/archive', array('controller' => 'milestones', 'action' => 'archive'), array('project_id' => '\d+'));
      $router->map('project_milestones_add', 'projects/:project_id/milestones/add', array('controller' => 'milestones', 'action' => 'add'), array('project_id' => '\d+'));
      $router->map('project_milestones_quick_add', 'projects/:project_id/milestones/quick_add', array('controller' => 'milestones', 'action' => 'quick_add'), array('project_id' => '\d+'));
      $router->map('project_milestones_export', 'projects/:project_id/milestones/export', array('controller' => 'milestones', 'action' => 'export'), array('project_id' => '\d+'));
      
      $router->map('project_milestone', 'projects/:project_id/milestones/:milestone_id', array('controller' => 'milestones', 'action' => 'view'), array('project_id' => '\d+', 'milestone_id' => '\d+'));
      $router->map('project_milestone_edit', 'projects/:project_id/milestones/:milestone_id/edit', array('controller' => 'milestones', 'action' => 'edit'), array('project_id' => '\d+', 'milestone_id' => '\d+'));
      $router->map('project_milestone_reschedule', 'projects/:project_id/milestones/:milestone_id/reschedule', array('controller' => 'milestones', 'action' => 'reschedule'), array('project_id' => '\d+', 'milestone_id' => '\d+'));
    } // defineRoutes
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
      $events->listen(array(
        'on_get_project_object_types', 
        'on_get_completable_project_object_types', 
        'on_get_day_project_object_types', 
      ), 'register_milestone_type');
      $events->listen('on_project_tabs', 'on_project_tabs');
      $events->listen('on_project_object_options', 'on_project_object_options');
      $events->listen('on_project_object_quick_options', 'on_project_object_quick_options');
      $events->listen('on_quick_add', 'on_quick_add');
      $events->listen('on_project_export', 'on_project_export');
      $events->listen('on_project_permissions', 'on_project_permissions');
      $events->listen('on_portal_permissions', 'on_portal_permissions');
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
      return lang('Milestones');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Add milestones to projects');
    } // getDescription
    
  }

?>