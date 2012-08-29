<?php

  /**
   * Resources module on_email_templates event handler
   *
   * @package activeCollab.modules.resources
   * @subpackage handlers
   */
  
  /**
   * Register email templates
   *
   * @param array $templates
   * @return null
   */
  function resources_handle_on_email_templates(&$templates) {
    $templates[RESOURCES_MODULE] = array(
      new EmailTemplate(RESOURCES_MODULE, 'new_comment'),
      new EmailTemplate(RESOURCES_MODULE, 'task_assigned'),
      new EmailTemplate(RESOURCES_MODULE, 'task_completed'),
      new EmailTemplate(RESOURCES_MODULE, 'task_reopened'),
    );
  } // resources_handle_on_email_templates

?>