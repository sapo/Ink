<?php

  /**
   * Clean up after user has been deleted
   *
   * @param array $cleanup
   * @return null
   */
  function invoicing_handle_on_user_cleanup(&$cleanup) {
    if(!isset($cleanup['invoices'])) {
      $cleanup['invoices'] = array();
    } // if
    
    $cleanup['invoices'][] = 'issued_by';
    $cleanup['invoices'][] = 'closed_by';
    $cleanup['invoices'][] = 'created_by';
    
    if(!isset($cleanup['invoice_payments'])) {
      $cleanup['invoice_payments'] = array();
    } // if
    
    $cleanup['invoice_payments'][] = 'created_by';
  } // invoicing_handle_on_user_cleanup

?>