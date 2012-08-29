<?php

  /**
   * Invoices class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class Invoices extends BaseInvoices {
    
    /**
     * Paginate all invoices
     *
     * @param array $statuses
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateAll($statuses = null, $page = 1, $per_page = 30, $order_by = 'created_on DESC') {
      if($statuses) {
        return Invoices::paginate(array(
          'conditions' => array('status IN (?)', $statuses),
          'order' => $order_by,
        ), $page, $per_page);
      } else {
        return Invoices::paginate(array(
          'order' => $order_by,
        ), $page, $per_page);
      } // if
    } // paginateAll
    
    
    /**
     * Paginate invoices by company
     *
     * @param Company $company
     * @param array $statuses
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateByCompany(&$company, $statuses = null, $page = 1, $per_page = 30, $order_by = 'created_on DESC') {
      if(is_foreachable($statuses)) {
        return Invoices::paginate(array(
          'conditions' => array('company_id = ? AND status IN (?)', $company->getId(), $statuses),
          'order' => $order_by,
        ), $page, $per_page);
      } else {
        return Invoices::paginate(array(
          'conditions' => array('company_id = ?', $company->getId()),
          'order' => $order_by,
        ), $page, $per_page);
      } // if
    } // findByCompany
    
    /**
     * Return number of draft invoices
     *
     * @param void
     * @return integer
     */
    function countDrafts() {
      return Invoices::count(array('status = ?', INVOICE_STATUS_DRAFT));
    } // countDrafts
    
    /**
     * Count overdue invoices (if company is provided, then it counts for that specified company)
     *
     * @param Copmany $company
     * @return integer
     */
    function countOverdue($company = null) {
      $today = new DateValue(time() + get_user_gmt_offset());
      if ($company) {
        return Invoices::count(array('status = ? AND due_on < ? AND company_id = ?', INVOICE_STATUS_ISSUED, $today, $company->getId()));
      } else {
        return Invoices::count(array('status = ? AND due_on < ?', INVOICE_STATUS_ISSUED, $today));
      } // if
    } // countOverdue
    
    /**
     * Find overdue invoices (if company is provided, only invoices for that companies are returned)
     *
     * @param Copmany $company
     * @return integer
     */
    function findOverdue($company = null) {
      $today = new DateValue(time() + get_user_gmt_offset());
      if ($company) {
        return Invoices::find(array(
          'condition' => array('status = ? AND due_on < ? AND company_id = ?', INVOICE_STATUS_ISSUED, $today, $company->getId()),
          'order' => 'due_on DESC',
        ));
      } else {
        return Invoices::find(array(
          'condition' => array('status = ? AND due_on < ?', INVOICE_STATUS_ISSUED, $today),
          'order' => 'due_on DESC',
        ));
      } // if
    } // findOverdue
    
    /**
     * Count outstanding (overdue invoices are excluded. If company is provided outstanding invoices for that company are counted)
     *
     * @param Company $company
     * @return integer
     */
    function countOutstanding($company = null) {
      $today = new DateValue(time() + get_user_gmt_offset());
      if ($company) {
        return Invoices::count(array('status = ? AND due_on >= ? AND company_id = ?', INVOICE_STATUS_ISSUED, $today, $company->getId()));
      } else {
        return Invoices::count(array('status = ? AND due_on >= ?', INVOICE_STATUS_ISSUED, $today));
      } // if
    } // countOutstanding
    
    /**
     * Return outstanding invoices (overdue invoices are excluded. If company is provided outstanding invoices for that company are counted)
     *
     * @param Company $company
     * @return array;
     */
    function findOutstanding($company = null) {
      $today = new DateValue(time() + get_user_gmt_offset());
      if ($company) {
        return Invoices::find(array(
          'condition' => array('status = ? AND due_on >= ? AND company_id = ?', INVOICE_STATUS_ISSUED, $today, $company->getId()),
          'order' => 'due_on DESC',
        ));
      } else {
        return Invoices::find(array(
          'condition' => array('status = ? AND due_on >= ?', INVOICE_STATUS_ISSUED, $today),
          'order' => 'due_on DESC',
        ));
      } // if
    } // findOutstanding
        
    /**
     * Return invoices by company
     *
     * @param Company $company
     * @param array $statuses
     * @Param string $order_by
     * @return array
     */
    function findByCompany(&$company, $statuses = null, $order_by = 'created_on') {
      if(is_foreachable($statuses)) {
        return Invoices::find(array(
          'conditions' => array('company_id = ? AND status IN (?)', $company->getId(), $statuses),
          'order' => $order_by,
        ));
      } else {
        return Invoices::find(array(
          'conditions' => array('company_id = ?', $company->getId()),
          'order' => $order_by,
        ));
      } // if
    } // findByCompany
    
    /**
     * Count invoices by company
     *
     * @param Company $company
     * @param array $statuses
     * @return integer
     */
    function countByCompany(&$company, $statuses = null) {
      if (is_foreachable($statuses)) {
        return Invoices::count(array('company_id = ? AND status IN (?)', $company->getId(), $statuses));
      } else {
        return Invoices::count(array('company_id = ?', $company->getId()));
      } // if
    } // countByCompany
    
    /**
     * Return summarized company invoices information
     *
     * @param array $statuses
     * @return array
     */
    function findInvoicedCompaniesInfo($statuses = null) {
      $companies_table = TABLE_PREFIX . 'companies';
      $invoices_table = TABLE_PREFIX . 'invoices';
      
      if(is_foreachable($statuses)) {
        return db_execute_all("SELECT $companies_table.id, $companies_table.name, COUNT($invoices_table.id) AS 'invoices_count' FROM $companies_table, $invoices_table WHERE $invoices_table.company_id = $companies_table.id AND $invoices_table.status IN (?) GROUP BY $invoices_table.company_id ORDER BY $companies_table.name ", $statuses);
      } else {
        return db_execute_all("SELECT $companies_table.id, $companies_table.name, COUNT($invoices_table.id) AS 'invoices_count' FROM $companies_table, $invoices_table WHERE $invoices_table.company_id = $companies_table.id GROUP BY $invoices_table.company_id ORDER BY $companies_table.name ");
      } // if
    } // findInvoicedCompaniesInfo
    
    /**
     * Return number of invoices that use $currency
     *
     * @param Currency $currency
     * @return integer
     */
    function countByCurrency($currency) {
      return Invoices::count(array('currency_id = ?', $currency->getId()));
    } // countByCurrency
    
    /**
     * Increment invoice counters
     *
     * @param integer $year
     * @param integer $month
     * @return boolean
     */
    function incrementDateInvoiceCounters($year = null, $month = null) {
      if ($year === null) {
        $year = date('Y');
      } // if
      
      if ($month === null) {
        $month = date('n');
      } // if
      
      $counters = ConfigOptions::getValue('invoicing_number_date_counters');
      
      $previous_month_counter = array_var($counters, $year.'_'.$month, 0);
      $previous_year_counter =  array_var($counters, $year, 0);
      $previous_total_counter = array_var($counters, 'total', 0);
      
      $counters[$year.'_'.$month] = ($previous_month_counter + 1);
      $counters[$year] = ($previous_year_counter + 1);
      $counters['total'] = ($previous_total_counter + 1);
      return ConfigOptions::setValue('invoicing_number_date_counters', $counters);
    } // incrementDateInvoiceCounters
    
    /**
     * Retrieves invoice counters
     *
     * @param integer $year
     * @param integer $month
     * @return array
     */
    function getDateInvoiceCounters($year = null, $month = null) {
      if ($year === null) {
        $year = date('Y');
      } // if
      
      if ($month === null) {
        $month = date('n');
      } // if
      
      $counters = ConfigOptions::getValue('invoicing_number_date_counters');
      
      $previous_month_counter = array_var($counters, $year.'_'.$month, 0);
      $previous_year_counter =  array_var($counters, $year, 0);
      $previous_total_counter = array_var($counters, 'total', 0);
      
      return array($previous_total_counter, $previous_year_counter, $previous_month_counter);
    } // getDateInvoiceCounters
    
    /**
     * Retrieves invoice number generator pattern
     * 
     * @param void
     * @return strings
     */
    function getInvoiceNumberGeneratorPattern() {
      return ConfigOptions::getValue('invoicing_number_pattern');
    } // getinvoiceNumberGeneratorPattern
    
    /**
     * Set invoice number generator pattern
     *
     * @param string $pattern
     * @return boolean
     */
    function setInvoiceNumberGeneratorPattern($pattern) {
      return ConfigOptions::setValue('invoicing_number_pattern', $pattern);
    } // setInvoiceNumberGeneratorPattern
  } // Invoices

?>