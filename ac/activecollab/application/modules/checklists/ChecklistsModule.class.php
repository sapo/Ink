<?php

  /**
   * Checklists module definition
   *
   * @package activeCollab.modules.checklists
   * @subpackage models
   */
  class ChecklistsModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'checklists';
    
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
      $router->map('project_checklists', 'projects/:project_id/checklists', array('controller' => 'checklists', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('project_checklists_reorder', 'projects/:project_id/checklists/reorder', array('controller' => 'checklists', 'action' => 'reorder'), array('project_id' => '\d+'));
      $router->map('project_checklists_archive', 'projects/:project_id/checklists/archive', array('controller' => 'checklists', 'action' => 'archive'), array('project_id' => '\d+'));
      $router->map('project_checklists_add', 'projects/:project_id/checklists/add', array('controller' => 'checklists', 'action' => 'add'), array('project_id' => '\d+'));
      $router->map('project_checklists_quick_add', 'projects/:project_id/checklists/quick-add', array('controller' => 'checklists', 'action' => 'quick_add'), array('project_id' => '\d+'));
      $router->map('project_checklists_export', 'projects/:project_id/checklists/export', array('controller' => 'checklists', 'action' => 'export'), array('project_id' => '\d+'));
      
      $router->map('project_checklist', 'projects/:project_id/checklists/:checklist_id', array('controller' => 'checklists', 'action' => 'view'), array('project_id' => '\d+', 'checklist_id' => '\d+'));
      $router->map('project_checklist_edit', 'projects/:project_id/checklists/:checklist_id/edit', array('controller' => 'checklists', 'action' => 'edit'), array('project_id' => '\d+', 'checklist_id' => '\d+'));
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
      $events->listen('on_milestone_objects', 'on_milestone_objects');
      $events->listen('on_milestone_objects_by_visibility', 'on_milestone_objects_by_visibility');
      $events->listen('on_project_object_opened', 'on_project_object_opened');
      $events->listen('on_project_object_completed', 'on_project_object_completed');
      $events->listen('on_object_inserted', 'on_object_inserted');
      $events->listen('on_project_object_trashed', 'on_project_object_trashed');
      $events->listen('on_project_object_restored', 'on_project_object_restored');
      $events->listen('on_quick_add', 'on_quick_add');
      $events->listen('on_milestone_add_links', 'on_milestone_add_links');
      $events->listen('on_project_export', 'on_project_export');
      $events->listen('on_copy_project_items', 'on_copy_project_items');
      $events->listen('on_project_permissions', 'on_project_permissions');
      $events->listen('on_get_completable_project_object_types', 'on_get_completable_project_object_types');
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
      return lang('Checklists');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Adds simple task lists to projects');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated and all checklists from all projects will be deleted');
    } // getUninstallMessage
    
  }

?>