<?php

  /**
   * System module functions
   *
   * @package activeCollab.modules.system
   */

  /**
   * Group objects by given date
   *
   * @param array $objects
   * @return array
   */
  function group_invoices_by_currency($invoices) {
    $result = array();
    if(is_foreachable($invoices)) {
      foreach($invoices as $invoice) {
        if (!isset($result[$invoice->getCurrencyId()])) {
          $result[$invoice->getCurrencyId()]['currency'] = $invoice->getCurrency();
        } // if
        $result[$invoice->getCurrencyId()]['invoices'][] = $invoice;
      } // foreach
    } // if
    return $result;
  } // group_invoices_by_currency
  
  /**
   * Returns company invoicing logo url
   * 
   * @param void
   * @return null
   */
  function get_company_invoicing_logo_url() {
    $logo = get_company_invoicing_logo_path();
    if (is_file($logo)){
      return URL_BASE == ROOT_URL . '/' ? ROOT_URL . '/public/brand/invoicing_logo.jpg' : ROOT_URL . '/brand/invoicing_logo.jpg';
    } else {
      return get_image_url('default-invoice-logo.gif', INVOICING_MODULE);
    } // if
  } // get_invoicing_logo_url
  
  /**
   * Returns company invoicing logo path
   * 
   * @param void
   * @return null
   */
  function get_company_invoicing_logo_path() {
    $logo_path  = PUBLIC_PATH . '/brand/invoicing_logo.jpg';
    return is_file($logo_path) ? $logo_path : null;
  } // get_company_invoicing_logo_path
?>