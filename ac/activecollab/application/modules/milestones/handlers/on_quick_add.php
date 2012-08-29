<?php

  /**
   * Milestones module on_quick_add handler
   *
   * @package activeCollab.modules.milestones
   * @subpackage handlers
   */
  
  /**
   * Milestones handle on_quick_add event
   *
   * @param array $quick_add_urls
   * @return null
   */
  function milestones_handle_on_quick_add(&$quick_add_urls) {
    $quick_add_urls['milestone'] = assemble_url('project_milestones_quick_add', array('project_id' => '-PROJECT-ID-'));
  } // milestones_handle_on_quick_add

?>