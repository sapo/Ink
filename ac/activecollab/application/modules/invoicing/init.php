<?php

  /**
   * Init invoicing module
   *
   * @package activeCollab.modules.invoicing
   */

  define('INVOICING_MODULE', 'invoicing');
  define('INVOICING_MODULE_PATH', APPLICATION_PATH . '/modules/invoicing');

  define('INVOICE_STATUS_DRAFT', 0);
  define('INVOICE_STATUS_ISSUED', 1);
  define('INVOICE_STATUS_BILLED', 2);
  define('INVOICE_STATUS_CANCELED', 3);
  
  define('INVOICES_WORK_PATH', WORK_PATH . '/invoices');
  
  define('INVOICE_NUMBER_COUNTER_TOTAL', ':invoice_in_total');
  define('INVOICE_NUMBER_COUNTER_YEAR', ':invoice_in_year');
  define('INVOICE_NUMBER_COUNTER_MONTH', ':invoice_in_month');
  
  define('INVOICE_VARIABLE_CURRENT_YEAR', ':current_year');
  define('INVOICE_VARIABLE_CURRENT_MONTH', ':current_month');
  define('INVOICE_VARIABLE_CURRENT_MONTH_SHORT', ':current_short_month');
  define('INVOICE_VARIABLE_CURRENT_MONTH_LONG', ':current_long_month');

  use_model(array(
    'invoices',
    'invoice_items',
    'invoice_payments',
    'tax_rates',
    'currencies',
    'invoice_item_templates',
    'invoice_note_templates'
  ), INVOICING_MODULE);
  
  require INVOICING_MODULE_PATH . '/functions.php'; 
?>