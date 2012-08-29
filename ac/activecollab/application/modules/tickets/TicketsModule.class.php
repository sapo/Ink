<?php

  /**
   * Tickets module definition
   *
   * @package activeCollab.modules.tickets
   * @subpackage models
   */
  class TicketsModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'tickets';
    
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
      $router->map('project_tickets', 'projects/:project_id/tickets', array('controller' => 'tickets', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('project_tickets_archive', 'projects/:project_id/tickets/archive', array('controller' => 'tickets', 'action' => 'archive'), array('project_id' => '\d+'));
      $router->map('project_tickets_mass_edit', 'projects/:project_id/tickets/mass-edit', array('controller' => 'tickets', 'action' => 'mass_update'), array('project_id' => '\d+'));
      $router->map('project_tickets_add', 'projects/:project_id/tickets/add', array('controller' => 'tickets', 'action' => 'add'), array('project_id' => '\d+'));
      $router->map('project_tickets_quick_add', 'projects/:project_id/tickets/quick-add', array('controller' => 'tickets', 'action' => 'quick_add'), array('project_id' => '\d+'));
      $router->map('project_tickets_export', 'projects/:project_id/tickets/export', array('controller' => 'tickets', 'action' => 'export'), array('project_id' => '\d+'));
      $router->map('project_tickets_reorder', 'projects/:project_id/tickets/reorder', array('controller' => 'tickets', 'action' => 'reorder_tickets'), array('project_id' => '\d+'));
      
      $router->map('project_ticket', 'projects/:project_id/tickets/:ticket_id', array('controller' => 'tickets', 'action' => 'view'), array('project_id' => '\d+', 'ticket_id' => '\d+'));
      $router->map('project_ticket_edit', 'projects/:project_id/tickets/:ticket_id/edit', array('controller' => 'tickets', 'action' => 'edit'), array('project_id' => '\d+', 'ticket_id' => '\d+'));
      $router->map('project_ticket_changes', 'projects/:project_id/tickets/:ticket_id/changes', array('controller' => 'tickets', 'action' => 'changes'), array('project_id' => '\d+', 'ticket_id' => '\d+'));
    } // defineRoutes
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
      $events->listen('on_get_project_object_types', 'on_get_project_object_types');
      $events->listen('on_get_completable_project_object_types', 'on_get_completable_project_object_types');
      $events->listen('on_project_tabs', 'on_project_tabs');
      $events->listen('on_milestone_objects', 'on_milestone_objects');
      $events->listen('on_milestone_objects_by_visibility', 'on_milestone_objects_by_visibility');
      $events->listen('on_portal_milestone_objects', 'on_portal_milestone_objects');
      $events->listen('on_milestone_add_links', 'on_milestone_add_links');
      $events->listen('on_portal_milestone_add_links', 'on_portal_milestone_add_links');
      $events->listen('on_user_cleanup', 'on_user_cleanup');
      $events->listen('on_quick_add', 'on_quick_add');
      $events->listen('on_master_categories', 'on_master_categories');
      $events->listen('on_project_export', 'on_project_export');
      $events->listen('on_project_object_moved', 'on_project_object_moved');
      $events->listen('on_copy_project_items', 'on_copy_project_items');
      $events->listen('on_project_permissions', 'on_project_permissions');
      $events->listen('on_portal_permissions', 'on_portal_permissions');
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
    
      db_execute("CREATE TABLE " . TABLE_PREFIX . "ticket_changes (
        id int(10) unsigned NOT NULL auto_increment,
        ticket_id int(10) unsigned NOT NULL default '0',
        version int(10) unsigned NOT NULL default '0',
        old_value text,
        new_value text,
        changes longtext,
        created_on datetime default NULL,
        created_by_id int(11) default NULL,
        created_by_name varchar(100) default NULL,
        created_by_email varchar(150) default NULL,
        PRIMARY KEY  (id)
      ) $storage_engine $default_charset;");
      
      $this->addConfigOption('ticket_categories', SYSTEM_CONFIG_OPTION, array('General'));
      
      return parent::install();
    } // install
    
    /**
     * Uninstall this module
     *
     * @param void
     * @return boolean
     */
    function uninstall() {
      db_execute("DROP TABLE IF EXISTS " . TABLE_PREFIX . "ticket_changes");
      
      return parent::uninstall();
    } // uninstall
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Tickets');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Adds issue tracking support to projects');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All tickets from all projects will be deleted');
    } // getUninstallMessage
    
  }

?>