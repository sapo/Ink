<?php

  /**
   * Render select Mailbox type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_mailbox_security($params, &$smarty) {
    $mailbox_security_types = array(MM_SECURITY_NONE, MM_SECURITY_SSL, MM_SECURITY_TLS);
    
    $value = null;
    if(isset($params['value'])) {
      $value = $params['value'];
      unset($params['value']);
    } // if
    
    $options = array();
    foreach($mailbox_security_types as $mailbox_security_type) {
      $option_attributes = $mailbox_security_type == $value ? array('selected' => true) : null;
      $options[] = option_tag($mailbox_security_type, $mailbox_security_type, $option_attributes);
    } // foreach
    
    return select_box($options, $params);
  } // smarty_function_select_mailbox_security

?>