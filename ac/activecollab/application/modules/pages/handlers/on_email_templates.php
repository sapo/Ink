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
  function pages_handle_on_email_templates(&$templates) {
    $templates[PAGES_MODULE] = array(
      new EmailTemplate(PAGES_MODULE, 'new_page'), 
      new EmailTemplate(PAGES_MODULE, 'new_revision')
    );
  } // pages_handle_on_email_templates

?>