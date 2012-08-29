<?php

  /**
   * Source module definition
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class SourceModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'source';
    
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
    var $version = '1.0';
    
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
      
      // Home
      $router->map('project_repositories', '/projects/:project_id/repositories', array('controller'=>'repository', 'action'=>'index'), array('project_id'=>'\d+'));
      
      // Repositories
      $router->map('repository_add', '/projects/:project_id/repositories/add', array('controller'=>'repository', 'action'=>'add'), array('project_id'=>'\d+'));
      $router->map('repository_edit', '/projects/:project_id/repositories/:repository_id/edit', array('controller'=>'repository', 'action'=>'edit'), array('project_id'=>'\d+', 'repository_id'=>'\d+'));
      $router->map('repository_delete', '/projects/:project_id/repositories/:repository_id/delete', array('controller'=>'repository', 'action'=>'delete'), array('project_id'=>'\d+', 'repository_id'=>'\d+'));
      $router->map('repository_test_connection', '/projects/:project_id/repositories/test_connection', array('controller'=>'repository', 'action'=>'test_repository_connection'), array('project_id'=>'\d+'));
      
      $router->map('repository_history', '/projects/:project_id/repositories/:repository_id/history', array('controller'=>'repository', 'action'=>'history'), array('project_id'=>'\d+', 'repository_id'=>'\d+'));
      $router->map('repository_update', '/projects/:project_id/repositories/:repository_id/update', array('controller'=>'repository', 'action'=>'update'), array('project_id'=>'\d+', 'repository_id'=>'\d+'));
      $router->map('repository_browse', '/projects/:project_id/repositories/:repository_id/browse', array('controller'=>'repository', 'action'=>'browse'), array('project_id'=>'\d+', 'repository_id'=>'\d+'));
      $router->map('repository_compare', '/projects/:project_id/repositories/:repository_id/compare', array('controller'=>'repository', 'action'=>'compare'), array('project_id'=>'\d+', 'repository_id'=>'\d+'));
      
      $router->map('repository_commit', '/projects/:project_id/repositories/:repository_id/revision/:r', array('controller'=>'repository', 'action'=>'commit'), array('project_id'=>'\d+', 'repository_id'=>'\d+', 'r'=>'\d+'));
      $router->map('repository_commit_paths', '/projects/:project_id/repositories/:repository_id/revision/:r/paths', array('controller'=>'repository', 'action'=>'commit_get_paths'), array('project_id'=>'\d+', 'repository_id'=>'\d+', 'r'=>'\d+'));
      
      $router->map('repository_users', '/projects/:project_id/repositories/:repository_id/users', array('controller'=>'repository', 'action'=>'repository_users'), array('project_id'=>'\d+', 'repository_id'=>'\d+'));
      $router->map('repository_user_add', '/projects/:project_id/repositories/:repository_id/users/add', array('controller'=>'repository', 'action'=>'repository_user_add'), array('project_id'=>'\d+', 'repository_id'=>'\d+'));
      $router->map('repository_user_delete', '/projects/:project_id/repositories/:repository_id/users/delete', array('controller'=>'repository', 'action'=>'repository_user_delete'), array('project_id'=>'\d+', 'repository_id'=>'\d+'));
      
      $router->map('repository_item_info', '/projects/:project_id/repositories/:repository_id/info', array('controller'=>'repository', 'action'=>'info'), array('project_id'=>'\d+', 'repository_id'=>'\d+'));
      $router->map('repository_file_history', '/projects/:project_id/repositories/:repository_id/file_history', array('controller'=>'repository', 'action'=>'file_history'), array('project_id'=>'\d+', 'repository_id'=>'\d+'));
      $router->map('repository_file_download', '/projects/:project_id/repositories/:repository_id/file_download', array('controller'=>'repository', 'action'=>'file_download'), array('project_id'=>'\d+', 'repository_id'=>'\d+'));
      $router->map('repository_project_object_commits', '/projects/:project_id/project-object-commits/:object_id', array('controller'=>'repository', 'action'=>'project_object_commits'),  array('project_id'=>'\d+', 'object_id'=>'\d+'));
      
      // Admin
      $router->map('admin_source', '/admin/tools/source', array('controller'=>'source_admin', 'action'=>'index'));
      $router->map('admin_source_test_svn', '/admin/tools/source/test-svn', array('controller'=>'source_admin', 'action'=>'test_svn'));
    } // defineRoutes
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
      $events->listen('on_project_tabs', 'on_project_tabs');
      $events->listen('on_project_permissions', 'on_project_permissions');
      $events->listen('on_portal_permissions', 'on_portal_permissions');
      $events->listen('on_project_object_options', 'on_project_object_options');
      $events->listen('on_project_object_quick_options', 'on_project_object_quick_options');
      $events->listen('on_portal_object_quick_options', 'on_portal_object_quick_options');
      $events->listen('on_get_project_object_types', 'on_get_project_object_types');
      $events->listen('on_hourly', 'on_hourly');
      $events->listen('on_daily', 'on_daily');
      $events->listen('on_frequently', 'on_frequently');
      $events->listen('on_admin_sections', 'on_admin_sections');
      $events->listen('on_object_deleted', 'on_object_deleted');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  (Un)Install
    // ---------------------------------------------------
    
    /**
     * Can this module be installed or not
     *
     * @param array $log
     * @return boolean
     */
    function canBeInstalled(&$log) {
      if(extension_loaded('xml') && function_exists('xml_parser_create')) {
        $log[] = lang('OK: XML extension loaded');
        return true;
      } else {
        $log[] = lang('This module requires XML PHP extension to be installed. Read more about XML extension in PHP documentation: http://www.php.net/manual/en/book.xml.php');
        return false;
      } // if
    } // canBeInstalled
    
    /**
     * Install module
     *
     * @param void
     * @return boolean
     */
    function install() {
      if (defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT) {
        $engine = 'ENGINE=InnoDB';
      } else {
        $engine = '';
      } // if

      if(defined('DB_CHARSET') && DB_CHARSET == 'utf8') {
        $charset = 'DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
      } else {
        $charset = '';
      } // if
      
      /**
       * Create a table for keeping relations between commits and project objects
       */
      db_execute("CREATE TABLE `" . TABLE_PREFIX . "commit_project_objects` (
        `object_id` int(10) unsigned NOT NULL default '0',
        `object_type` varchar(50) NOT NULL,
        `project_id` int(10) unsigned NOT NULL default '0',
        `revision` int(10) unsigned NOT NULL default '0',
        `repository_id` int(10) unsigned NOT NULL default '0',
        PRIMARY KEY  (`object_id`,`revision`)
      ) $engine $charset;");


      /**
       * Create a table for keeping relations between repository users and activeCollab users
       */
      db_execute("CREATE TABLE `".TABLE_PREFIX."source_users` (
        `repository_id` smallint(5) unsigned NOT NULL default '0',
        `repository_user` varchar(50) NOT NULL default '',
        `user_id` smallint(5) unsigned default NULL,
        PRIMARY KEY  (`repository_id`,`repository_user`)
      ) $engine $charset;");
      
      $this->addConfigOption('source_svn_path', SYSTEM_CONFIG_OPTION);
      $this->addConfigOption('source_svn_config_dir', SYSTEM_CONFIG_OPTION);
      
      $this->addEmailTemplate('repository_updated', "[:project_name] ':object_name' :object_type has just been updated", ':details_body
<p>Hi,</p>
<p>:object_type :object_name at :project_name project has just been updated with :commit_count new commits</p>
<div>:commits_body</div>
<p>Best,<br />:owner_company_name</p>', array('commits_body', 'details_body', 'project_name', 'object_name', 'object_type', 'object_url', 'project_url', 'commit_count'));
      
      return parent::install();
    } // install
    
    /**
     * Uninstall this module
     *
     * @param void
     * @return boolean
     */
    function uninstall() {
      db_execute("DROP TABLE IF EXISTS " . TABLE_PREFIX . "commit_project_objects");
      db_execute("DROP TABLE IF EXISTS " . TABLE_PREFIX . "source_users");
      
      return parent::uninstall();
    } // uninstall
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Source');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Adds source version control functionality to projects');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Data received using this module will be removed from local database, but the original content from repositories used by this module will be left intact');
    } // getUninstallMessage
    
  }

?>