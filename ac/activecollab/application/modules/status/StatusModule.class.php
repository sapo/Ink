<?php

  /**
   * Status module definition
   *
   * @package activeCollab.modules.status
   * @subpackage models
   */
  class StatusModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'status';
    
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
      $router->map('status_updates', 'status', array('controller' => 'status', 'action' => 'index'));
      $router->map('status_updates_add', 'status/add', array('controller' => 'status', 'action' => 'add'));
      $router->map('status_updates_rss', 'status/rss', array('controller' => 'status', 'action' => 'rss'));
      $router->map('status_updates_count_new_messages', 'status/count-new-messages', array('controller' => 'status', 'action' => 'count_new_messages'));
      
      $router->map('status_update', 'status/update/:status_update_id/', array('controller' => 'status', 'action' => 'view'), array('status_update_id' => '\d+'));
      
      $router->map('status_updates_module', 'admin/modules/status', array('controller' => 'status_module_admin', 'action' => 'module', 'module_name' => 'status'));
    } // defineRoutes
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
      $events->listen('on_build_menu', 'on_build_menu');
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
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'ENGINE=InnoDB' : '';
      $charset = defined('DB_CHARSET') && DB_CHARSET == 'utf8' ? 'DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' : '';
      
      db_execute("CREATE TABLE " . TABLE_PREFIX . "status_updates (
        id int(10) unsigned NOT NULL auto_increment,
        message varchar(255) NOT NULL,
        created_by_id smallint(5) unsigned NOT NULL,
        created_by_name varchar(100) NOT NULL,
        created_by_email varchar(100) NOT NULL,
        created_on datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY created_on (created_on)
      ) $engine $charset;");
      
      $this->addConfigOption('status_update_last_visited', USER_CONFIG_OPTION);
      
      return parent::install();
    } // install
    
    /**
     * Uninstall this module
     *
     * @param void
     * @return boolean
     */
    function uninstall() {
      db_execute("DROP TABLE IF EXISTS " . TABLE_PREFIX . "status_updates");
      
      return parent::uninstall();
    } // uninstall
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Status');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Adds simple, globally available communication channel. Tell your team members or clients what you are working on or have a quick chat');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. All data generated using it will be deleted');
    } // getUninstallMessage
    
  }

?>