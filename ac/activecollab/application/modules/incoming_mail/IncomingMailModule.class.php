<?php

  /**
   * Incoming mail module definition
   *
   * @package activeCollab.modules.incoming_mail
   * @subpackage models
   */
  class IncomingMailModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'incoming_mail';
    
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
      $router->map('incoming_mail_admin', 'admin/incoming-mail', array('controller' => 'incoming_mail_admin', 'action' => 'index'));
      $router->map('incoming_mail_admin_add_mailbox', 'admin/incoming-mail/mailboxes/add', array('controller' => 'incoming_mail_admin', 'action' => 'add_mailbox'));
      $router->map('incoming_mail_admin_view_mailbox', 'admin/incoming-mail/mailboxes/:mailbox_id/view', array('controller' => 'incoming_mail_admin', 'action' => 'view_mailbox'));
      $router->map('incoming_mail_admin_edit_mailbox', 'admin/incoming-mail/mailboxes/:mailbox_id/edit', array('controller' => 'incoming_mail_admin', 'action' => 'edit_mailbox'), array('mailbox_id' => '\d+'));
      $router->map('incoming_mail_admin_delete_mailbox', 'admin/incoming-mail/mailboxes/:mailbox_id/delete', array('controller' => 'incoming_mail_admin', 'action' => 'delete_mailbox'), array('mailbox_id' => '\d+'));
      $router->map('incoming_mail_admin_mailbox_list_messages', 'admin/incoming-mail/mailboxes/:mailbox_id/list', array('controller' => 'incoming_mail_admin', 'action' => 'list_messages'), array('mailbox_id' => '\d+'));
      $router->map('incoming_mail_admin_test_mailbox_connection', 'admin/incoming-mail/test-mailbox-connection', array('controller' => 'incoming_mail_admin', 'action' => 'test_mailbox_connection'));
      
      $router->map('incoming_mail', 'incoming-mail', array('controller' => 'incoming_mail_frontend', 'action' => 'index'));
      $router->map('incoming_mail_count_pending', 'incoming-mail/count-pending', array('controller' => 'incoming_mail_frontend', 'action' => 'count_pending'));
      $router->map('incoming_mail_edit_mail', 'incoming-mail/mail/:mail_id/edit', array('controller' => 'incoming_mail_frontend', 'action' => 'edit'), array('mailbox_id' => '\d+'));
      $router->map('incoming_mail_delete_mail', 'incoming-mail/mail/:mail_id/delete', array('controller' => 'incoming_mail_frontend', 'action' => 'delete'), array('mailbox_id' => '\d+'));
      $router->map('incoming_mail_import_mail', 'incoming-mail/mail/:mail_id/solve-conflict', array('controller' => 'incoming_mail_frontend', 'action' => 'conflict'), array('mailbox_id' => '\d+'));
      $router->map('incoming_mail_additional_form_fields', 'incoming-mail/mail/additional-fields', array('controller' => 'incoming_mail_frontend', 'action' => 'conflict_form_additional_fields'));
      
      $router->map('incoming_mail_mass_conflict_resolution', 'incoming-mail/mail/mass-conflict-resolution', array('controller' => 'incoming_mail_frontend', 'action' => 'mass_conflict_resolution'));
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
      $events->listen('on_frequently', 'on_frequently');
      $events->listen('on_build_menu', 'on_build_menu');
      $events->listen('on_system_permissions', 'on_system_permissions');
      $events->listen('on_prepare_email', 'on_prepare_email');
      $events->listen('on_object_deleted', 'on_object_deleted');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  (Un)Install
    // ---------------------------------------------------
    
    /**
     * Returns true if this module can be installed
     *
     * @param array $log
     * @return boolean
     */
    function canBeInstalled(&$log) {
      if(extension_loaded('imap')) {
        $log[] = lang('OK: IMAP extension loaded');
      } else {
        $log[] = lang('This module requires IMAP PHP extension to be installed. Read more about IMAP extension in PHP documentation: http://www.php.net/imap');
        return false;
      } // if
      
      return true;
    } // canBeInstalled
    
    /**
     * Install module
     *
     * @param void
     * @return boolean
     */
    function install() {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'ENGINE=InnoDB' : '';
    	$default_charset = defined('DB_CHARSET') && DB_CHARSET == 'utf8' ? 'DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' : '';
      
      db_execute("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."incoming_mailboxes` (
        `id` tinyint(3) unsigned NOT NULL auto_increment,
        `project_id` smallint(5) unsigned default NULL,
        `object_type` enum('discussion','ticket') NOT NULL default 'ticket',
        `mailbox` varchar(100) default NULL,
        `username` varchar(50) default NULL,
        `password` varchar(50) default NULL,
        `host` varchar(255) default NULL,
        `from_name` varchar(50) default NULL,
        `from_email` varchar(100) default NULL,
        `type` enum('POP3','IMAP') NOT NULL default 'POP3',
        `port` int(10) unsigned default NULL,
        `security` enum('NONE','TLS','SSL') NOT NULL default 'NONE',
        `last_status` tinyint(3) unsigned NOT NULL default '0',
        `enabled` tinyint(3) unsigned NOT NULL default '0',
        `accept_all_registered` tinyint(3) unsigned NOT NULL default '0',
        `accept_anonymous` tinyint(3) unsigned NOT NULL default '0',
        PRIMARY KEY  (`id`)
      ) $engine $charset;");
      
      db_execute("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."incoming_mails` (
        `id` int(10) unsigned NOT NULL auto_increment,
        `parent_id` int(10) unsigned default NULL,
        `project_id` smallint(5) unsigned default NULL,
        `incoming_mailbox_id` smallint(5) unsigned default NULL,
        `subject` varchar(255) default NULL,
        `body` text default NULL,
        `headers` longtext default NULL,
        `object_type` enum('discussion','comment','ticket') NOT NULL default 'ticket',
        `state` tinyint(3) unsigned NOT NULL default '0',
        `created_by_id` smallint(5) unsigned default NULL,
        `created_by_name` varchar(255) default NULL,
        `created_by_email` varchar(255) default NULL,
        `created_on` datetime default NULL,
        PRIMARY KEY  (`id`)
      ) $engine $charset;");
      
      db_execute("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."incoming_mail_attachments` (
        `id` int(10) unsigned NOT NULL auto_increment,
        `mail_id` int(10) unsigned default NULL,
        `temporary_filename` varchar(255) default NULL,
        `original_filename` varchar(255) default NULL,
        `content_type` varchar(255) default NULL,
        `file_size` int(10) unsigned NOT NULL default '0',
        PRIMARY KEY  (`id`)
      ) $engine $charset;");
      
      db_execute("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."incoming_mail_activity_logs` (
        `id` int(10) unsigned NOT NULL auto_increment,
        `mailbox_id` smallint(5) unsigned default NULL,
        `state` tinyint(3) unsigned NOT NULL default '0',
        `response` varchar(255) default NULL,
        `sender` varchar(255) default NULL,
        `subject` varchar(255) default NULL,
        `incoming_mail_id` int(10) unsigned default NULL,
        `project_object_id` int(10) unsigned default NULL,
        `created_on` datetime default NULL,
        PRIMARY KEY  (`id`),
        KEY `created_on` (`created_on`)
      ) $engine $charset;");
      
      $this->addConfigOption('email_splitter_translations', SYSTEM_CONFIG_OPTION, array());
      
      return parent::install();
    } // install
    
    /**
     * Uninstall this module
     *
     * @param void
     * @return boolean
     */
    function uninstall() {
      db_execute('DROP TABLE IF EXISTS `' . TABLE_PREFIX . 'incoming_mailboxes`');
      db_execute('DROP TABLE IF EXISTS `' . TABLE_PREFIX . 'incoming_mails`');
      db_execute('DROP TABLE IF EXISTS `' . TABLE_PREFIX . 'incoming_mail_attachments`');
      db_execute('DROP TABLE IF EXISTS `' . TABLE_PREFIX . 'incoming_mail_activity_logs`');
      
      return parent::uninstall();
    } // uninstall
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Incoming Mail');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Check POP and IMAP mailboxes and import emails as discussions, tickets and comments');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Discussions, tickets and comments imported with this module will not be deleted');
    } // getUninstallMessage
    
  }

?>