<?php

  /**
   * Tickets module on_quick_add event handler
   *
   * @package activeCollab.modules.tickets
   * @subpackage handlers
   */
  
  /**
   * Prepare quick add ticket form
   *
   * @param array $quick_add_urls
   * @return null
   */
  function tickets_handle_on_quick_add(&$quick_add_urls) {
    $quick_add_urls['ticket'] = assemble_url('project_tickets_quick_add', array('project_id' => '-PROJECT-ID-'));
  } // tickets_handle_on_quick_add

?>