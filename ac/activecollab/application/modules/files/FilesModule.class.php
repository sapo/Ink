<?php

  /**
   * Files module definition
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class FilesModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'files';
    
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
      $router->map('project_files', 'projects/:project_id/files', array('controller' => 'files', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('project_files_quick_add', 'projects/:project_id/files/quick-add', array('controller' => 'files', 'action' => 'quick_add'), array('project_id' => '\d+'));
      $router->map('project_files_export', 'projects/:project_id/files/export', array('controller' => 'files', 'action' => 'export'), array('project_id' => '\d+'));
      $router->map('project_files_mass_edit', 'projects/:project_id/files/mass-edit', array('controller' => 'files', 'action' => 'mass_edit'), array('project_id' => '\d+'));
      
      $router->map('project_files_upload', 'projects/:project_id/files/upload', array('controller' => 'files', 'action' => 'upload'), array('project_id' => '\d+'));
      $router->map('project_files_upload_single', 'projects/:project_id/files/upload-single', array('controller' => 'files', 'action' => 'upload_single'), array('project_id' => '\d+'));
      
      $router->map('project_file', 'projects/:project_id/files/:file_id', array('controller' => 'files', 'action' => 'view'), array('project_id' => '\d+', 'file_id' => '\d+'));
      $router->map('project_file_edit', 'projects/:project_id/files/:file_id/edit', array('controller' => 'files', 'action' => 'edit'), array('project_id' => '\d+', 'file_id' => '\d+'));
      $router->map('project_file_new_version', 'projects/:project_id/files/:file_id/new-version', array('controller' => 'files', 'action' => 'new_version'), array('project_id' => '\d+', 'file_id' => '\d+'));
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
      $events->listen('on_project_object_quick_options', 'on_project_object_quick_options');
      $events->listen('on_email_templates', 'on_email_templates');
      $events->listen('on_new_revision', 'on_new_revision');
      $events->listen('on_project_object_ready', 'on_project_object_ready');
      $events->listen('on_quick_add', 'on_quick_add');
      $events->listen('on_milestone_objects', 'on_milestone_objects');
      $events->listen('on_milestone_objects_by_visibility', 'on_milestone_objects_by_visibility');
      $events->listen('on_milestone_add_links', 'on_milestone_add_links');
      $events->listen('on_master_categories', 'on_master_categories');
      $events->listen('on_project_export', 'on_project_export');
      $events->listen('on_project_permissions', 'on_project_permissions');
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
      $this->addConfigOption('file_categories', SYSTEM_CONFIG_OPTION, array('General'));
      $this->addEmailTemplate('new_file', "[:project_name] File ':object_name' has been uploaded", "<p>Hi,</p>\n
        <p><a href=\":created_by_url\">:created_by_name</a> has uploaded a new file in <a href=\":project_url\">:project_name</a> project.</p>\n
        <p><a href=\":object_url\">Click here</a> for more details</p>\n
        <p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_name', 'created_by_url'));
      
      $this->addEmailTemplate('new_revision', "[:project_name] New version of ':object_name' file is up", "<p>Hi,</p>
        <p><a href=\":created_by_url\">:created_by_name</a> has uploaded a new version of <a href=\":object_url\">:object_name</a> file in <a href=\":project_url\">:project_name</a> project.</p>
        <p>Best,<br />:owner_company_name</p>", array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_url', 'created_by_name'));
      
      return parent::install();
    } // install
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Files');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Adds file repository to each project. Highlights: files support versioning, can have comments, email notifications, can be organized in categories and more...');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All files created with this module will be deleted. Files attached to objects in other modules (tickets, comments etc) will not be deleted');
    } // getUninstallMessage
    
  }

?>