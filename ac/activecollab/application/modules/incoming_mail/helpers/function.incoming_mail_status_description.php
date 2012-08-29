<?php

  /**
   * incoming_mail_get_status_description helper
   *
   * @package activeCollab.modules.incoming_mail
   * @subpackage helpers
   */
  
  /**
   * Renders incoming mail status description for provided code
   * 
   * - code - status code
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   * 
   */
  function smarty_function_incoming_mail_status_description($params, &$smarty) {
    return incoming_mail_module_get_status_description(array_var($params, 'code', 255));
  } // incoming_mail_get_status_description

?>