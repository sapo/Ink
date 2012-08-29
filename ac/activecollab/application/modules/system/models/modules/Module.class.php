<?php

  /**
   * Module class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Module extends BaseModule {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name;
    
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
    var $version;
    
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
      
    } // defineRoutes
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
      
    } // defineHandlers
    
    // ---------------------------------------------------
    //  (Un)Install
    // ---------------------------------------------------
    
    /**
     * Returns true if this module installed
     *
     * @param void
     * @return boolean
     */
    function isInstalled() {
      return (boolean) Modules::count(array('name = ?', $this->name));
    } // isInstalled
    
    /**
     * Install this module
     *
     * @param void
     * @return boolean
     */
    function install() {
      if($this->isNew()) {
        $this->setName($this->name);
        $this->setIsSystem($this->is_system);
        
        return $this->save();
      } else {
        return new Error('Module already installed');
      } // if
    } // install
    
    /**
     * Uninstall this module
     *
     * @param void
     * @return boolean
     */
    function uninstall() {
      db_begin_work();
      
      $name = $this->name;
      
      ProjectObjects::deleteByModule($name);
      ConfigOptions::deleteByModule($name);
      EmailTemplates::deleteByModule($name);
      
      cache_clear();
      
      $delete = $this->delete();
      if($delete && !is_error($delete)) {
        db_commit();
        return true;
      } else {
        db_rollback();
        return $delete;
      } // if
    } // uninstall
    
    /**
     * Can this module be installed or not
     *
     * @param array $log
     * @return boolean
     */
    function canBeInstalled(&$log) {
      return true;
    } // canBeInstalled
    
    /**
     * Returns true if this module can be uninstalled
     *
     * @param void
     * @return boolean
     */
    function canBeUninstalled() {
      return !$this->getIsSystem();
    } // canBeUninstalled
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return full module path
     *
     * @param void
     * @return string
     */
    function getPath() {
      return APPLICATION_PATH . '/modules/' . $this->name;
    } // getPath
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return Inflector::humanize($this->name);
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('No module description provided');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return null;
    } // getUninstallMessage
    
    /**
     * Return module version
     *
     * @param void
     * @return mixed
     */
    function getVersion() {
      return $this->version;
    } // getVersion
    
    /**
     * Returns true if this is a system module
     *
     * @param void
     * @return boolean
     */
    function getIsSystem() {
      return $this->is_system || parent::getIsSystem();
    } // getIsSystem
    
    // ---------------------------------------------------
    //  Helper methods
    // ---------------------------------------------------
    
    /**
     * Add new configuration option
     *
     * @param string $name
     * @param string $type
     * @param mixed $value
     * @return boolean
     */
    function addConfigOption($name, $type = SYSTEM_CONFIG_OPTION, $value = null) {
      $option = new ConfigOption();
      $option->setAttributes(array(
        'name'   => $name,
        'module' => $this->name,
        'type'   => $type,
        'value'  => serialize($value),
      ));
      return $option->save();
    } // addConfigOption
    
    /**
     * Create new email template for this module
     *
     * @param string $name
     * @param string $subject
     * @param string $body
     * @param array $variables
     * @return boolean
     */
    function addEmailTemplate($name, $subject, $body, $variables = null) {
      $email_template = new EmailTemplate();
      $email_template->setAttributes(array(
        'name'      => $name,
        'module'    => $this->name,
        'subject'   => $subject,
        'body'      => $body,
        'variables' => is_array($variables) ? implode("\n", $variables) : $variables,
      ));
      return $email_template->save();
    } // addEmailTemplate
    
    /**
     * Create database table in system
     *
     * @param string $table_name
     * @param array $fields
     * @param mixed $indexes
     * @param string $engine - specify table engine, if ommited it will use InnoDB if transactions are supported and MyISAM if not
     */
    function createTable($table_name, $fields, $indexes, $engine = false) {
      // default table engine
      if ($engine === false) {
        $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';
      } // if
      $engine = 'ENGINE='.$engine;
      // default charset
      $default_charset = defined('DB_CHARSET') && DB_CHARSET == 'utf8' ? 'DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' : '';
      
      // create sql query
      $sql = "CREATE TABLE `" . TABLE_PREFIX . $table_name . "` (";
      $sql.= implode(',', $fields);
      if (is_foreachable($indexes)) {
        $sql.= ',' . implode(',', $indexes);
      } else {
        $sql.= ',' . $indexes;
      } // if
      $sql.= ") $engine $default_charset";
      
      return db_execute($sql);
    } // createTable
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return module icon URL
     *
     * @param void
     * @return string
     */
    function getIconUrl($size = 'medium') {
      switch ($size) {
      	case 'big':
          return get_image_url('icon_big.gif', $this->name);
      		break;
      		
      	case 'small':
          return get_image_url('icon_small.gif', $this->name);
      		break;        
      
      	default:
          return get_image_url('icon_medium.gif', $this->name);
      		break;
      } // switch
    } // getIconUrl
    
    /**
     * Return details URL
     *
     * @param void
     * @return string
     */
    function getViewUrl() {
      return assemble_url('admin_module', array('module_name' => $this->name));
    } // getViewUrl
    
    /**
     * Return install module URL
     *
     * @param void
     * @return string
     */
    function getInstallUrl() {
      return assemble_url('admin_module_install', array('module_name' => $this->name));
    } // getInstallUrl
    
    /**
     * Return uninstall module URL
     *
     * @param void
     * @return string
     */
    function getUninstallUrl() {
      return assemble_url('admin_module_uninstall', array('module_name' => $this->name));
    } // getUninstallUrl
  
  }

?>