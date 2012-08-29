<?php

  /**
   * Public submit module definition
   *
   * @package activeCollab.modules.public_submit
   * @subpackage models
   */
  class PublicSubmitModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'public_submit';
    
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
      $router->map('admin_settings_public_submit', 'admin/tools/public-submit', array('controller' => 'public_submit_admin', 'action' => 'index'));
      
      
      $router->map('public_submit', 'submit', array('controller' => 'public_submit', 'action' => 'index'));
      $router->map('public_submit_unavailable', 'submit/unavailable', array('controller' => 'public_submit', 'action' => 'unavailable'));
      $router->map('public_submit_success', 'submit/success', array('controller' => 'public_submit', 'action' => 'success'));
    } // defineRoutes
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
      $events->listen('on_admin_sections', 'on_admin_sections');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  (Un)Install
    // ---------------------------------------------------
    
    /**
     * Install this module
     *
     * @param void
     * @return boolean
     */
    function install() {
      $this->addConfigOption('public_submit_default_project', SYSTEM_CONFIG_OPTION, 0);
      $this->addConfigOption('public_submit_enabled', SYSTEM_CONFIG_OPTION, false);
      $this->addConfigOption('public_submit_enable_captcha', SYSTEM_CONFIG_OPTION, true);
      
      return parent::install();
    } // install
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Public Submit');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Submit tickets without having an activeCollab account');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Data you create using this module will not be deleted');
    } // getUninstallMessage
    
  }

?>