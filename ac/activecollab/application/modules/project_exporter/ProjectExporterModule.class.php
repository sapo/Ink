<?php

  /**
   * Project exporter module definition
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage models
   */
  class ProjectExporterModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'project_exporter';
    
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
      $router->map('project_exporter', 'projects/:project_id/project_exporter', array('controller' => 'project_exporter', 'action' => 'index'), array('project_id' => '\d+'));
      $router->map('project_exporter_finish_export', 'projects/:project_id/project_exporter/finish', array('controller' => 'project_exporter', 'action' => 'finish'), array('project_id' => '\d+'));
      $router->map('project_exporter_download_export', 'projects/:project_id/project_exporter/download', array('controller' => 'project_exporter', 'action' => 'download'), array('project_id' => '\d+'));
    } // defineRoutes
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
      $events->listen('on_project_options', 'on_project_options');
    } // defineHandlers
    
    /**
     * Install this module
     *
     * @param void
     * @return boolean
     */
    function install() {
      mkdir(WORK_PATH.'/export', 0777);
      return parent::install();  
    } // install
    
    // ---------------------------------------------------
    //  Names
    // ---------------------------------------------------
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Project Exporter');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Export project as a static website');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Files created with this module will not be deleted');
    } // getUninstallMessage
    
  }

?>