<?php

  /**
   * Backup module definition
   *
   * @package activeCollab.modules.backup
   * @subpackage models
   */
  class BackupModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'backup';
    
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
      $router->map('backup_admin', 'admin/tools/backup', array('controller' => 'backup_admin', 'action' => 'index'));
    } // defineRoutes
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
      $events->listen('on_admin_sections', 'on_admin_sections');
      $events->listen('on_daily', 'on_daily');
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
      $this->addConfigOption('backup_enabled', SYSTEM_CONFIG_OPTION, false);
      $this->addConfigOption('backup_how_many_backups', SYSTEM_CONFIG_OPTION, 5);
      
      return parent::install();
    } // install
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Backup');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Automatically create daily backup of your activeCollab data');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Backups created with this module will not be deleted.');
    } // getUninstallMessage
    
  }

?>