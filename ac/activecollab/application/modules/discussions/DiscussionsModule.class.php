<?php

  /**
   * Discussions module definition
   *
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class DiscussionsModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'discussions';
    
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
      $router->map('project_discussions', 'projects/:project_id/discussions', array('controller' => 'discussions', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('project_discussions_add', 'projects/:project_id/discussions/add', array('controller' => 'discussions', 'action' => 'add'), array('project_id' => '\d+'));
      $router->map('project_discussions_quick_add', 'projects/:project_id/discussions/quick-add', array('controller' => 'discussions', 'action' => 'quick_add'), array('project_id' => '\d+'));
      $router->map('project_discussions_export', 'projects/:project_id/discussions/export', array('controller' => 'discussions', 'action' => 'export'), array('project_id' => '\d+'));
       
      $router->map('project_discussion', 'projects/:project_id/discussions/:discussion_id', array('controller' => 'discussions', 'action' => 'view'), array('project_id' => '\d+', 'discussion_id' => '\d+'));
      $router->map('project_discussion_edit', 'projects/:project_id/discussions/:discussion_id/edit', array('controller' => 'discussions', 'action' => 'edit'), array('project_id' => '\d+', 'discussion_id' => '\d+'));
      
      $router->map('project_discussion_pin', 'projects/:project_id/discussions/:discussion_id/pin', array('controller' => 'discussions', 'action' => 'pin'), array('project_id' => '\d+', 'discussion_id' => '\d+'));
      $router->map('project_discussion_unpin', 'projects/:project_id/discussions/:discussion_id/unpin', array('controller' => 'discussions', 'action' => 'unpin'), array('project_id' => '\d+', 'discussion_id' => '\d+'));
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
      $events->listen('on_portal_milestone_objects', 'on_portal_milestone_objects');
      $events->listen('on_milestone_add_links', 'on_milestone_add_links');
      $events->listen('on_portal_milestone_add_links', 'on_portal_milestone_add_links');
      $events->listen('on_email_templates', 'on_email_templates');
      $events->listen('on_project_object_ready', 'on_project_object_ready');
      $events->listen('on_project_object_options', 'on_project_object_options');
      $events->listen('on_quick_add', 'on_quick_add');
      $events->listen('on_master_categories', 'on_master_categories');
      $events->listen('on_project_export', 'on_project_export');
      $events->listen('on_user_cleanup', 'on_user_cleanup');
      $events->listen('on_project_permissions', 'on_project_permissions');
      $events->listen('on_portal_permissions', 'on_portal_permissions');
      $events->listen('on_comment_added', 'on_comment_added');
      $events->listen('on_comment_deleted', 'on_comment_deleted');
      $events->listen('on_project_object_quick_options', 'on_project_object_quick_options');
      $events->listen('on_portal_object_quick_options', 'on_portal_object_quick_options');
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
      $this->addConfigOption('discussion_categories', SYSTEM_CONFIG_OPTION, array('General'));
      $this->addEmailTemplate('new_discussion', "[:project_name] Discussion ':object_name' has been started", '<p>Hi,</p>
<p><a href=":created_by_url">:created_by_name</a> has started a new discussion in <a href=":project_url">:project_name</a> project.</p>
<hr />
<h1>:object_name</h1>
<p>:last_comment_body</p>
<hr />
<p><a href=":object_url">Click here</a> for more details</p>
<p>Best,<br />:owner_company_name</p>', array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_name', 'created_by_url', 'last_comment_body'));
      
      return parent::install();
    } // install
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Discussions');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Adds discussion boards to projects');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All discussions from all projects will be deleted');
    } // getUninstallMessage
    
  }

?>