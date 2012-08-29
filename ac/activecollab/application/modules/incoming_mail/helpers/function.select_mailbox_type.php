<?php

  /**
   * Render select Mailbox type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_mailbox_type($params, &$smarty) {
    $mailbox_types = array(MM_SERVER_TYPE_POP3, MM_SERVER_TYPE_IMAP);
    
    $value = null;
    if(isset($params['value'])) {
      $value = $params['value'];
      unset($params['value']);
    } // if
    
    $options = array();
    foreach($mailbox_types as $mailbox_type) {
      $option_attributes = $mailbox_type == $value ? array('selected' => true) : null;
      $options[] = option_tag($mailbox_type, $mailbox_type, $option_attributes);
    } // foreach
    
    return select_box($options, $params);
  } // smarty_function_select_mailbox_type

?>