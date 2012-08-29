<?php

  /**
   * Discussions module on_email_templates event handler
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */
  
  /**
   * Register email templates
   *
   * @param array $templates
   * @return null
   */
  function discussions_handle_on_email_templates(&$templates) {
    $templates[DISCUSSIONS_MODULE] = array(
      new EmailTemplate(DISCUSSIONS_MODULE, 'new_discussion'), 
    );
  } // discussions_handle_on_email_templates

?>