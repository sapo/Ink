<?php

  /**
   * Render select currency box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_invoice_status($params, &$smarty){
    $possibilities = array(
      INVOICE_STATUS_DRAFT => lang('Draft'),
      INVOICE_STATUS_ISSUED => lang('Issued'),
      INVOICE_STATUS_BILLED => lang('Billled'),
      INVOICE_STATUS_CANCELED => lang('Canceled'),
    );
    $value = array_var($params, 'value', NULL, True);

    $options = array();
    foreach ( $possibilities as $k=>$v ) {
      $option_attributes = $k == $value ? array('selected' => True) : Null;
      $options[] = option_tag($v, $k, $option_attributes);
    }
    return select_box( $options, $params);
  } // smarty_function_select_invoice_status
  
?>