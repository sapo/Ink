<?php

  /**
   * InvoicePayments class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoicePayments extends BaseInvoicePayments {

    /**
     * Return payments by invoice
     *
     * @param Invoice $invoice
     * @return array
     */
    function findByInvoice($invoice) {
      return InvoicePayments::find(array(
        'conditions' => array('invoice_id = ?', $invoice->getId()),
        'order' => 'paid_on',
      ));
    } // findByInvoice
    
    /**
     * Return payments by company
     *
     * @param Company $company
     * @return array
     */
    function findByCompany($company) {
      $invoices_table = TABLE_PREFIX . 'invoices';
      $invoice_paymnets_table = TABLE_PREFIX . 'invoice_payments';
      
      return InvoicePayments::findBySQL("SELECT $invoice_paymnets_table.* FROM $invoices_table, $invoice_paymnets_table WHERE $invoice_paymnets_table.invoice_id = $invoices_table.id AND $invoices_table.company_id = ? ORDER BY $invoice_paymnets_table.paid_on DESC", array($company->getId()));
    } // findByCompany

    /**
     * Return paginated payments
     *
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateAll($page, $per_page) {
      return InvoicePayments::paginate(array(
        'conditions' => array(),
        'order' => 'paid_on DESC',
      ), $page, $per_page);
    } // paginateAll

    /**
     * Return total amount paid for a given invoice
     *
     * @param Invoice $invoice
     * @return float
     */
    function sumByInvoice($invoice) {
      return (float) array_var(db_execute_one("SELECT SUM(amount) AS 'amount_paid' FROM " . TABLE_PREFIX . 'invoice_payments WHERE invoice_id = ?', $invoice->getId()), 'amount_paid');
    } // sumByInvoice
    
    /**
     * Drop payments by invoice
     *
     * @param Invoice $invoice
     * @return boolean
     */
    function deleteByInvoice($invoice) {
      return InvoicePayments::delete(array('invoice_id = ?', $invoice->getId()));
    } // deleteByInvoice

  }

?>