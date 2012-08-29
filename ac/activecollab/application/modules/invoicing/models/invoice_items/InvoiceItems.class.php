<?php

  /**
   * InvoiceItems class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceItems extends BaseInvoiceItems {

    /**
     * Return items by invoice
     *
     * @param Invoice $invoice
     * @return array
     */
    function findByInvoice($invoice) {
      return InvoiceItems::find(array(
        'conditions' => array('invoice_id = ?', $invoice->getId()),
        'order' => 'position'
      ));
    } // findByInvoice
    
    /**
     * Return number of items that user $rate tax rate
     *
     * @param TaxRate $tax_rate
     * @return integer
     */
    function countByTaxRate($tax_rate) {
      return InvoiceItems::count(array('tax_rate_id = ?', $tax_rate->getId()));
    } // countByTaxRate

    /**
     * Delete all items for a invoice
     *
     * @param Invoice $invoice
     * @return null
     */
    function deleteByInvoice($invoice) {
      db_begin_work();
      
      $execute = db_execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_time_records WHERE invoice_id = ?', $invoice->getId());
      if ($execute && !is_error($execute)) {
        $delete = InvoiceItems::delete(array('invoice_id = ?', $invoice->getId()));
        if ($delete && !is_error($delete)) {
          db_commit();
        } else {
          db_rollback();
        } // if
        return $delete;
      } else {
        db_rollback();
        return $execute;
      } // if
    } // deleteByInvoice

  } // class
?>