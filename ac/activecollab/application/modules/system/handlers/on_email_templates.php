<?php

  /**
   * System module on_email_templates event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Register email templates
   *
   * @param array $templates
   * @return null
   */
  function system_handle_on_email_templates(&$templates) {
    $templates[SYSTEM_MODULE] = array(
      new EmailTemplate(SYSTEM_MODULE, 'new_user'),
      new EmailTemplate(SYSTEM_MODULE, 'forgot_password')
    );
  } // system_handle_on_email_templates

?>