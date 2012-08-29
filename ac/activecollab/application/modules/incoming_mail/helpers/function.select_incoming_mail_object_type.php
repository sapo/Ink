<?php
  /**
   * Render select Mailbox type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_incoming_mail_object_type($params, &$smarty) {
    $mailbox_object_types = array();
    
    if (module_loaded('discussions')) {
      $mailbox_object_types[] = INCOMING_MAIL_OBJECT_TYPE_DISCUSSION;      
    } // if
    
    if (module_loaded('tickets')) {
      $mailbox_object_types[] = INCOMING_MAIL_OBJECT_TYPE_TICKET;
    } // if
    
    if (!array_var($params, 'skip_comment', true)) {
      $mailbox_object_types[] = INCOMING_MAIL_OBJECT_TYPE_COMMENT;
    } // if
   
    $value = null;
    if(isset($params['value'])) {
      $value = $params['value'];
      unset($params['value']);
    } // if
    
    $options = array();
    foreach($mailbox_object_types as $mailbox_object_type) {
      $option_attributes = $mailbox_object_type == $value ? array('selected' => true) : null;
      $options[] = option_tag(Inflector::humanize($mailbox_object_type), $mailbox_object_type, $option_attributes);
    } // foreach
    
    return select_box($options, $params);
  } // smarty_function_select_mailbox_security

?>