<?php

  /**
   * Documents module definition
   *
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class DocumentsModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'documents';
    
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
      $router->map('documents', 'documents', array('controller' => 'documents', 'action' => 'index'));
      $router->map('documents_add_text', 'documents/add-text', array('controller' => 'documents', 'action' => 'add_text'));
      $router->map('documents_upload_file', 'documents/upload-file', array('controller' => 'documents', 'action' => 'upload_file'));
      
      $router->map('document_view', 'categories/:category_id/documents/:document_id', array('controller' => 'documents', 'action' => 'view'), array('category_id' => '\d+', 'document_id' => '\d+'));
      $router->map('document_edit', 'documents/:document_id/edit', array('controller' => 'documents', 'action' => 'edit'), array('document_id' => '\d+'));
      $router->map('document_pin', 'documents/:document_id/pin', array('controller' => 'documents', 'action' => 'pin'), array('document_id' => '\d+'));
      $router->map('document_unpin', 'documents/:document_id/unpin', array('controller' => 'documents', 'action' => 'unpin'), array('document_id' => '\d+'));
      $router->map('document_delete', 'documents/:document_id/delete', array('controller' => 'documents', 'action' => 'delete'), array('document_id' => '\d+'));
      
      $router->map('document_categories', 'documents/categories', array('controller' => 'document_categories', 'action' => 'index'));
      $router->map('document_categories_add', 'documents/categories/add', array('controller' => 'document_categories', 'action' => 'add'));
      $router->map('document_categories_quick_add', 'documents/categories/quick-add', array('controller' => 'document_categories', 'action' => 'quick_add'));
      
      $router->map('document_category_view', 'documents/categories/:category_id', array('controller' => 'document_categories', 'action' => 'view'), array('category_id' => '\d+'));
      $router->map('document_category_edit', 'documents/categories/:category_id/edit', array('controller' => 'document_categories', 'action' => 'edit'), array('category_id' => '\d+'));
      $router->map('document_category_delete', 'documents/categories/:category_id/delete', array('controller' => 'document_categories', 'action' => 'delete'), array('category_id' => '\d+'));
      
      $router->map('documents_module', 'admin/modules/documents', array('controller' => 'documents_module_admin', 'action' => 'module', 'module_name' => 'documents'));
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
    //  Un(Install)
    // ---------------------------------------------------
    
    /**
     * Install module
     *
     * @param void
     * @return boolean
     */
    function install() {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'ENGINE=InnoDB' : '';
    	$default_charset = defined('DB_CHARSET') && DB_CHARSET == 'utf8' ? 'DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' : '';
    	
    	db_execute("CREATE TABLE " . TABLE_PREFIX . "documents (
    		id int(10) unsigned NOT NULL auto_increment,
    	  category_id tinyint(3) unsigned default NULL,
    	  type enum('text','file') NOT NULL default 'text',
    	  name varchar(100) NOT NULL,
    	  body text,
    	  mime_type varchar(50) default NULL,
    	  visibility tinyint(4) unsigned NOT NULL default '0',
    	  is_pinned tinyint(1) unsigned NOT NULL default '0',
    	  created_by_id smallint(5) unsigned NOT NULL,
    	  created_by_name varchar(100) NOT NULL,
    	  created_by_email varchar(100) NOT NULL,
    	  created_on datetime NOT NULL,
    	  PRIMARY KEY  (id)
    	) $engine $default_charset;");
    	
    	$document_categories_table = TABLE_PREFIX . 'document_categories';
    	
    	db_execute("CREATE TABLE $document_categories_table (
    		id tinyint(3) unsigned NOT NULL auto_increment,
    	  name varchar(100) NOT NULL default '',
    	  PRIMARY KEY  (id)
    	) $engine $default_charset;");
    	
    	db_execute("INSERT INTO $document_categories_table (name) VALUES ('General')");
    	
    	return parent::install();
    } // install
    
    /**
     * Uninstall module
     *
     * @param void
     * @return boolean
     */
    function uninstall() {
      db_execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'documents');
      db_execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'document_categories');
      
      return parent::uninstall();
    } // uninstall
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Documents');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Adds global document management system');
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