<?php

  /**
   * Invoicing module defintiion
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoicingModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'invoicing';
    
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
      $router->map('invoicing_module', 'admin/modules/invoicing', array('controller' => 'invoicing_module_admin', 'action' => 'module', 'module_name' => 'invoicing'));
      
      $router->map('invoices', 'invoices', array('controller' => 'invoices'));
      $router->map('invoices_add', 'invoices/add', array('controller' => 'invoices', 'action' => 'add'));
      
      $router->map('invoices_archive', 'invoices/archive', array('controller' => 'invoices', 'action' => 'archive'));
      $router->map('company_invoices', 'invoices/archive/:company_id', array('controller' => 'invoices_archive', 'action' => 'company'), array('company_id' => '\d+'));
    
      $router->map('invoice_payments', 'invoices/payments', array('controller' => 'invoice_payments', 'action' => 'index'));
    
      $router->map('invoice', 'invoices/:invoice_id', array('controller' => 'invoices', 'action' => 'view'), array('invoice_id' => '\d+'));
      $router->map('invoice_issue', 'invoices/:invoice_id/issue', array('controller' => 'invoices', 'action' => 'issue'), array('invoice_id' => '\d+'));
      $router->map('invoice_edit', 'invoices/:invoice_id/edit', array('controller' => 'invoices', 'action' => 'edit'), array('invoice_id' => '\d+'));
      $router->map('invoice_delete', 'invoices/:invoice_id/delete', array('controller' => 'invoices', 'action' => 'delete'), array('invoice_id' => '\d+'));
      $router->map('invoice_cancel', 'invoices/:invoice_id/cancel', array('controller' => 'invoices', 'action' => 'cancel'), array('invoice_id' => '\d+'));
      $router->map('invoice_company_details', 'invoices/company_details', array('controller' => 'invoices', 'action' => 'company_details'));
      $router->map('invoice_pdf', 'invoices/:invoice_id/pdf', array('controller' => 'invoices', 'action' => 'pdf'), array('invoice_id' => '\d+'));
      $router->map('invoice_time', 'invoices/:invoice_id/time', array('controller' => 'invoices', 'action' => 'time'), array('invoice_id' => '\d+'));
      $router->map('invoice_time_release', 'invoices/:invoice_id/time/release', array('controller' => 'invoices', 'action' => 'time_release'), array('invoice_id' => '\d+'));
      $router->map('invoice_notify', 'invoices/:invoice_id/notify', array('controller' => 'invoices', 'action' => 'notify'), array('invoice_id' => '\d+'));
    
      //$router->map('invoice_payments', 'invoices/:invoice_id/payments', array('controller' => 'invoices', 'action' => 'view'), array('invoice_id' => '\d+'));
      $router->map('invoice_payments_add', 'invoices/:invoice_id/payments', array('controller' => 'invoice_payments', 'action' => 'add'), array('invoice_id' => '\d+'));
    
      //$router->map('invoice_payment', 'invoices/:invoice_id/payments/:payment_id', array('controller' => 'invoice_payments', 'action' => 'view'), array('invoice_id' => '\d+', 'payment_id' => '\d+'));
      $router->map('invoice_payment_edit', 'invoices/:invoice_id/payments/:invoice_payment_id/edit', array('controller' => 'invoice_payments', 'action' => 'edit'), array('invoice_id' => '\d+', 'invoice_payment_id' => '\d+'));
      $router->map('invoice_payment_delete', 'invoices/:invoice_id/payments/:invoice_payment_id/delete', array('controller' => 'invoice_payments', 'action' => 'delete'), array('invoice_id' => '\d+', 'invoice_payment_id' => '\d+'));
      
      $router->map('admin_currencies', 'admin/currencies', array('controller' => 'currencies_admin'));
      $router->map('admin_currencies_add', 'admin/currencies/add', array('controller' => 'currencies_admin', 'action' => 'add'));
      $router->map('admin_currency_edit', 'admin/currencies/:currency_id/edit', array('controller' => 'currencies_admin', 'action' => 'edit'), array('currency_id' => '\d+'));
      $router->map('admin_currency_set_as_default', 'admin/currencies/:currency_id/set-as-default', array('controller' => 'currencies_admin', 'action' => 'set_as_default'), array('currency_id' => '\d+'));
      $router->map('admin_currency_delete', 'admin/currencies/:currency_id/delete', array('controller' => 'currencies_admin', 'action' => 'delete'), array('currency_id' => '\d+'));
    
      $router->map('admin_tax_rates', 'admin/tax-rates', array('controller' => 'tax_rates_admin'));
      $router->map('admin_tax_rate_add', 'admin/tax_rates/add', array('controller' => 'tax_rates_admin', 'action' => 'add'));
      $router->map('admin_tax_rate_edit', 'admin/tax_rates/:tax_rate_id/edit', array('controller' => 'tax_rates_admin', 'action' => 'edit'), array('tax_rate_id' => '\d+'));
      $router->map('admin_tax_rate_delete', 'admin/tax_rates/:tax_rate_id/delete', array('controller' => 'tax_rates_admin', 'action' => 'delete'), array('tax_rate_id' => '\d+'));
      
      $router->map('admin_invoicing_pdf', 'admin/invoicing/pdf', array('controller' => 'pdf_settings_admin'));
      
      $router->map('admin_invoicing_company_identity', 'admin/invoicing/company-identity', array('controller' => 'company_identity_settings_admin', 'action' => 'index'));
      $router->map('admin_invoicing_pdf_settings', 'admin/invoicing/pdf-settings', array('controller' => 'pdf_settings_admin', 'action' => 'index'));
      
      $router->map('admin_invoicing_notes', 'admin/invoicing/notes', array('controller' => 'invoice_note_templates_admin', 'action' => 'index'));
      $router->map('admin_invoicing_notes_reorder', 'admin/invoicing/notes/reorder', array('controller' => 'invoice_note_templates_admin', 'action' => 'reorder'));
      
      $router->map('admin_invoicing_note_add', 'admin/invoicing/notes/add', array('controller' => 'invoice_note_templates_admin', 'action' => 'add'));
      $router->map('admin_invoicing_note_edit', 'admin/invoicing/notes/:note_id/edit', array('controller' => 'invoice_note_templates_admin', 'action' => 'edit'), array('note_id' => '\d+'));
      $router->map('admin_invoicing_note_delete', 'admin/invoicing/notes/:note_id/delete', array('controller' => 'invoice_note_templates_admin', 'action' => 'delete'), array('note_id' => '\d+'));
      
      $router->map('admin_invoicing_items', 'admin/invoicing/items', array('controller' => 'invoice_item_templates_admin', 'action' => 'index'));
      $router->map('admin_invoicing_items_reorder', 'admin/invoicing/items/reorder', array('controller' => 'invoice_item_templates_admin', 'action' => 'reorder'));
      
      $router->map('admin_invoicing_item_add', 'admin/invoicing/items/add', array('controller' => 'invoice_item_templates_admin', 'action' => 'add'));
      $router->map('admin_invoicing_item_edit', 'admin/invoicing/items/:item_id/edit', array('controller' => 'invoice_item_templates_admin', 'action' => 'edit'), array('item_id' => '\d+'));
      $router->map('admin_invoicing_item_delete', 'admin/invoicing/items/:item_id/delete', array('controller' => 'invoice_item_templates_admin', 'action' => 'delete'), array('item_id' => '\d+'));
      
      $router->map('admin_invoicing_number', 'admin/invoicing/number-generator', array('controller' => 'invoice_number_generator_admin'));
      
      // ---------------------------------------------------
      //  Company
      // ---------------------------------------------------
      
      $router->map('people_company_invoices', 'people/:company_id/invoices', array('controller' => 'company_invoices', 'action' => 'index'), array('company_id' => '\d+'));
      $router->map('people_company_invoices_payments', 'people/:company_id/invoices/payments', array('controller' => 'company_invoices', 'action' => 'payments'), array('company_id' => '\d+'));
      $router->map('people_company_invoice', 'people/:company_id/invoices/:invoice_id', array('controller' => 'company_invoices', 'action' => 'view'), array('company_id' => '\d+', 'invoice_id' => '\d+'));
      $router->map('people_company_invoice_pdf', 'people/:company_id/invoices/:invoice_id/pdf', array('controller' => 'company_invoices', 'action' => 'pdf'), array('company_id' => '\d+', 'invoice_id' => '\d+'));
    } // defineRoutes
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
      $events->listen('on_admin_sections', 'on_admin_sections');
      $events->listen('on_build_menu', 'on_build_menu');
      $events->listen('on_system_permissions', 'on_system_permissions');
      $events->listen('on_company_options', 'on_company_options');
      $events->listen('on_user_cleanup', 'on_user_cleanup');
      $events->listen('on_time_report_footer_options', 'on_time_report_footer_options');
      $events->listen('on_object_deleted', 'on_object_deleted');
      $events->listen('on_company_tabs', 'on_company_tabs');
      $events->listen('on_project_object_quick_options', 'on_project_object_quick_options');
      $events->listen('on_dashboard_important_section', 'on_dashboard_important_section');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  Un(Install)
    // ---------------------------------------------------
    
    /**
     * Install this module
     *
     * @param void
     * @return boolean
     */
    function install() {

      // invoices
      $this->createTable('invoices', array(
        'id smallint(5) unsigned NOT NULL auto_increment',
        'company_id smallint(5) unsigned NOT NULL default \'0\'',
        'project_id smallint(5) unsigned default NULL',
        'currency_id tinyint(4) NOT NULL default \'0\'',
        'language_id tinyint(3) NOT NULL default \'0\'',
        'number varchar(50) NOT NULL',
        'company_address text',
        'comment varchar(255) default NULL',
        'note text',
        'status tinyint(4) NOT NULL default \'0\'',
        'issued_on date default NULL',
        'issued_by_id int(11) default NULL',
        'issued_by_name varchar(100) default NULL',
        'issued_by_email varchar(150) default NULL',
        'issued_to_id int(11) default NULL',
        'due_on date default NULL',
        'closed_on datetime default NULL',
        'closed_by_id int(11) default NULL',
        'closed_by_name varchar(100) default NULL',
        'closed_by_email varchar(100) default NULL',
        'created_on datetime default NULL',
        'created_by_id int(10) unsigned default NULL',
        'created_by_name varchar(100) default NULL',
        'created_by_email varchar(150) default NULL',
      ), 'PRIMARY KEY  (id)');

      // Invoice Items
      $this->createTable('invoice_items', array(
        'id int(11) unsigned NOT NULL auto_increment',
        'invoice_id smallint(5) unsigned NOT NULL default \'0\'',
        'position int(11) NOT NULL',
        'tax_rate_id tinyint(3) unsigned NOT NULL default \'0\'',
        'description varchar(255) NOT NULL',
        'quantity double unsigned NOT NULL default \'1\'',
        'unit_cost double(12,2) NOT NULL default \'0.00\'',
      ), array(
        'PRIMARY KEY  (id)',
        'KEY invoice_id (invoice_id,position)'
      ));
      
      $this->createTable('invoice_item_templates', array(
        'id int(11) unsigned NOT NULL auto_increment',
        'tax_rate_id tinyint(3) unsigned NOT NULL default \'0\'',
        'description varchar(255) NOT NULL',
        'quantity double unsigned NOT NULL default \'1\'',
        'unit_cost double(12,2) NOT NULL default \'0.00\'',
        "position int(11) NOT NULL default '0'",
      ), array(
        'PRIMARY KEY  (id)',
      ));
      
      // Invoice payments
      $this->createTable('invoice_payments', array(
        'id int(10) unsigned NOT NULL auto_increment',
        'invoice_id smallint(5) unsigned NOT NULL',
        'amount double(12,2) NOT NULL',
        'paid_on date NOT NULL',
        'comment text',
        'created_on datetime default NULL',
        'created_by_id int(10) unsigned default NULL',
        'created_by_name varchar(100) default NULL',
        'created_by_email varchar(150) default NULL',
      ), array(
        'PRIMARY KEY  (id)',
        'KEY invoice_id (invoice_id)'
      ));
      
      // Invoice note templates
      $this->createTable('invoice_note_templates', array(
        'id int(10) unsigned NOT NULL auto_increment',
        'position int(11) NOT NULL',
        'name varchar(150) default NULL',
        'content text',
      ), 'PRIMARY KEY  (id)');
      
      // Invoice time records
      $this->createTable('invoice_time_records', array(
        'invoice_id smallint(5) unsigned NOT NULL',
        'item_id int(10) unsigned NOT NULL',
        'time_record_id int(10) unsigned NOT NULL',
      ), 'PRIMARY KEY  (invoice_id,time_record_id)');
      
      // invoice_tax_rates
      $this->createTable('tax_rates', array(
        'id tinyint(3) unsigned NOT NULL auto_increment',
        'name varchar(50) NOT NULL',
        'percentage float(4,2) NOT NULL',
      ), 'PRIMARY KEY  (id)');    
      db_execute("INSERT INTO " . TABLE_PREFIX . "tax_rates (id, name, percentage) VALUES
        (1, 'VAT', 17.50);");
      
      $this->createTable('currencies', array(
        'id smallint(6) NOT NULL auto_increment',
        'name varchar(50) NOT NULL',
        'code varchar(3) NOT NULL',
        'default_rate double unsigned NOT NULL',
        'is_default tinyint(1) unsigned NOT NULL default \'0\''
      ), array(
        'PRIMARY KEY  (id)'
      ));
      db_execute("INSERT INTO " . TABLE_PREFIX . "currencies (id, name, code, default_rate, is_default) VALUES
        (1, 'Euro', 'EUR', 1, 0),
        (2, 'US Dollar', 'USD', 1, 1),
        (3, 'British Pound', 'GBP', 1, 0),
        (4, 'Japanese Yen', 'JPY', 1, 0)");

      // config options
      $this->addConfigOption('prefered_currency', SYSTEM_CONFIG_OPTION, null);
      $this->addConfigOption('invoicing_number_pattern', SYSTEM_CONFIG_OPTION, ':invoice_in_year/:current_year');
      $this->addConfigOption('invoicing_number_date_counters', SYSTEM_CONFIG_OPTION, null);

      // create and prepopulate company identity     
      $owner_company = get_owner_company();
      $owner_company_address = $owner_company->getConfigValue('office_address');
      $this->addConfigOption('invoicing_company_name', SYSTEM_CONFIG_OPTION, $owner_company->getName());
      $this->addConfigOption('invoicing_company_details', SYSTEM_CONFIG_OPTION, $owner_company_address);
      
      // default PDF settings 
      $this->addConfigOption('invoicing_pdf_paper_format', SYSTEM_CONFIG_OPTION, 'A4');
      $this->addConfigOption('invoicing_pdf_paper_orientation', SYSTEM_CONFIG_OPTION, 'Portrait');
      $this->addConfigOption('invoicing_pdf_header_text_color', SYSTEM_CONFIG_OPTION, '000000');
      $this->addConfigOption('invoicing_pdf_page_text_color', SYSTEM_CONFIG_OPTION, '000000');
      $this->addConfigOption('invoicing_pdf_border_color', SYSTEM_CONFIG_OPTION, '000000');
      $this->addConfigOption('invoicing_pdf_background_color', SYSTEM_CONFIG_OPTION, 'FFFFFF');
      
      // email templates
      $this->addEmailTemplate('issue', "Invoice #:invoice_number has been issued", "<p>Hi,</p>
<p><a href=\":issued_by_url\">:issued_by_name</a> just issued invoice <b>#:invoice_number</b> to you. Access <a href=\":invoice_url\">invoice details here</a> or <a href=\":pdf_url\">download PDF version here</a>.</p>
<p>Best,<br />:owner_company_name</p>", array('issued_by_name', 'issued_by_url', 'invoice_number', 'invoice_url', 'pdf_url'));
      $this->addEmailTemplate('billed', "Invoice #:invoice_number has been billed", "<p>Hi,</p>
<p><a href=\":closed_by_url\">:closed_by_name</a> just marked invoice <b>#:invoice_number</b> as billed. Access <a href=\":invoice_url\">invoice details and payments here</a>.</p>
<p>Best,<br />:owner_company_name</p>", array('closed_by_name', 'closed_by_url', 'invoice_number', 'invoice_url'));
      $this->addEmailTemplate('cancel', "Invoice #:invoice_number has been canceled", "<p>Hi,</p>
<p><a href=\":closed_by_url\">:closed_by_name</a> just canceled invoice <b>#:invoice_number</b>. Access <a href=\":invoice_url\">invoice details here</a>.</p>
<p>Best,<br />:owner_company_name</p>", array('closed_by_name', 'closed_by_url', 'invoice_number', 'invoice_url'));
      
      recursive_mkdir(WORK_PATH.'/invoices', 0777, WORK_PATH);
      
      return parent::install();
    } // install
    
    /**
     * Uninstall this module
     *
     * @param void
     * @return boolean
     */
    function uninstall() {
      db_execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'invoices');
      db_execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'invoice_items');
      db_execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'invoice_item_templates');
      db_execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'invoice_payments');
      db_execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'invoice_note_templates');
      db_execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'invoice_time_records');
      db_execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'tax_rates');
      db_execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'currencies');
      
      delete_dir(WORK_PATH.'/invoices');
      
      return parent::uninstall();
    } // uninstall
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Invoicing');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Adds invoicing support to activeCollab');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Invoices created using this module will be deleted');
    } // getUninstallMessage
    
  }

?>