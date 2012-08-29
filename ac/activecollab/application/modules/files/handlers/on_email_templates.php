<?php

  /**
   * Pages module on_email_templates event handler
   *
   * @package activeCollab.modules.pages
   * @subpackage handlers
   */
  
  /**
   * Register email templates
   *
   * @param array $templates
   * @return null
   */
  function files_handle_on_email_templates(&$templates) {
    $templates[FILES_MODULE] = array(
      new EmailTemplate(FILES_MODULE, 'new_file'), 
      new EmailTemplate(FILES_MODULE, 'new_revision')
    );
  } // files_handle_on_email_templates

?>