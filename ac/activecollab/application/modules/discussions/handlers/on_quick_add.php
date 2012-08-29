<?php

  /**
   * Discussions module on_quick_add event handler
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */
  
  /**
   * Discussions handle on_quick_add event
   *
   * @param array $quick_add_urls
   * @return null
   */
  function discussions_handle_on_quick_add(&$quick_add_urls) {
    $quick_add_urls['discussion'] = assemble_url('project_discussions_quick_add', array('project_id' => '-PROJECT-ID-'));
  } // discussions_handle_on_quick_add

?>