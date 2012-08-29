<?php

  /**
   * Pages module definition
   *
   * @package activeCollab.modules.pages
   * @subpackage models
   */
  class PagesModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'pages';
    
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
      $router->map('project_pages', 'projects/:project_id/pages', array('controller' => 'pages', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('project_pages_add', 'projects/:project_id/pages/add', array('controller' => 'pages', 'action' => 'add'), array('project_id' => '\d+'));
      $router->map('project_pages_quick_add', 'projects/:project_id/pages/quick-add', array('controller' => 'pages', 'action' => 'quick_add'), array('project_id' => '\d+'));
      $router->map('project_pages_export', 'projects/:project_id/pages/export', array('controller' => 'pages', 'action' => 'export'), array('project_id' => '\d+'));
      $router->map('project_pages_reorder', 'projects/:project_id/pages/reorder', array('controller' => 'pages', 'action' => 'reorder'), array('project_id' => '\d+'));
      
      $router->map('project_page', 'projects/:project_id/pages/:page_id', array('controller' => 'pages', 'action' => 'view'), array('project_id' => '\d+', 'page_id' => '\d+'));
      $router->map('project_page_edit', 'projects/:project_id/pages/:page_id/edit', array('controller' => 'pages', 'action' => 'edit'), array('project_id' => '\d+', 'page_id' => '\d+'));
      $router->map('project_page_revert', 'projects/:project_id/pages/:page_id/revert', array('controller' => 'pages', 'action' => 'revert'), array('project_id' => '\d+', 'page_id' => '\d+'));
      $router->map('project_page_compare_versions', 'projects/:project_id/pages/:page_id/compare-versions', array('controller' => 'pages', 'action' => 'compare_versions'), array('project_id' => '\d+', 'page_id' => '\d+'));
      $router->map('project_page_archive', 'projects/:project_id/pages/:page_id/archive', array('controller' => 'pages', 'action' => 'archive'), array('project_id' => '\d+', 'page_id' => '\d+'));
      $router->map('project_page_unarchive', 'projects/:project_id/pages/:page_id/unarchive', array('controller' => 'pages', 'action' => 'unarchive'), array('project_id' => '\d+', 'page_id' => '\d+'));
      
      $router->map('project_page_version_delete', 'projects/:project_id/pages/:page_id/versions/:version/delete', array('controller' => 'page_versions', 'action' => 'delete'), array('project_id' => '\d+', 'page_id' => '\d+', 'version' => '\d+'));
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
      $events->listen('on_new_revision', 'on_new_revision');
      $events->listen('on_email_templates', 'on_email_templates');
      $events->listen('on_milestone_objects', 'on_milestone_objects');
      $events->listen('on_milestone_objects_by_visibility', 'on_milestone_objects_by_visibility');
      $events->listen('on_project_object_ready', 'on_project_object_ready');
      $events->listen('on_quick_add', 'on_quick_add');
      $events->listen('on_milestone_add_links', 'on_milestone_add_links');
      $events->listen('on_project_export', 'on_project_export');
      $events->listen('on_project_permissions', 'on_project_permissions');
      $events->listen('on_master_categories', 'on_master_categories');
      $events->listen('on_project_object_options', 'on_project_object_options');
      $events->listen('on_project_object_quick_options', 'on_project_object_quick_options');
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
      $storage_engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'ENGINE=InnoDB' : '';
      $default_charset = defined('DB_CHARSET') && (DB_CHARSET == 'utf8') ? 'DEFAULT CHARSET=utf8' : '';
    
      db_execute("CREATE TABLE `" . TABLE_PREFIX . "page_versions` (
        `page_id` int(10) unsigned NOT NULL default '0',
        `version` smallint(5) unsigned NOT NULL default '0',
        `name` varchar(255) NOT NULL default '',
        `body` longtext,
        `created_on` datetime default NULL,
        `created_by_id` smallint(5) unsigned default NULL,
        `created_by_name` varchar(100) default NULL,
        `created_by_email` varchar(100) default NULL,
        PRIMARY KEY  (`page_id`,`version`)
      ) $storage_engine $default_charset;");
      
      $this->addConfigOption('pages_categories', SYSTEM_CONFIG_OPTION, array('General'));
      
      $this->addEmailTemplate('new_page', "[:project_name] Page ':object_name' has been created", '<p>Hi,</p>
        <p><a href=":created_by_url">:created_by_name</a> has created a new page in <a href=":project_url">:project_name</a> project - <a  href=":object_url">:object_name</a>. Content:</p>
        <hr />
        <p>:object_body</p>
        :attachments_body
        <hr />
        <p><a href=":object_url">Click here</a> for more details</p>
        <p>Best,<br />:owner_company_name</p>', array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_name', 'created_by_url'));
      
      $this->addEmailTemplate('new_revision', "[:project_name] New version of ':object_name' page is posted", '<p>Hi,</p>
        <p><a href=":created_by_url">:created_by_name</a> has created a new revision of <a href=":old_url">:old_name</a> page in <a href=":project_url">:project_name</a> project. New content:</p>
        <hr />
        <p>:new_body</p>
        :attachments_body
        <hr />
        <p>Best,<br />:owner_company_name</p>', array('owner_company_name', 'project_name', 'project_url', 'object_type', 'object_name', 'object_body', 'object_url', 'created_by_url', 'created_by_name', 'revision_num', 'old_url', 'old_name', 'old_body', 'new_url', 'new_name', 'new_body'));
      
      return parent::install();
    } // install
    
    /**
     * Uninstall this module
     *
     * @param void
     * @return boolean
     */
    function uninstall() {
      db_execute("DROP TABLE IF EXISTS `" . TABLE_PREFIX . "page_versions`");
      
      return parent::uninstall();
    } // uninstall
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Pages');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Adds collaborative writing tool to projects');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All pages from all projects will be deleted');
    } // getUninstallMessage
    
  }

?>