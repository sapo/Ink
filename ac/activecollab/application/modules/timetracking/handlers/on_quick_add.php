<?php

  /**
   * Timetracking module on_quick_add even handler
   *
   * @package activeCollab.modules.timetracking
   * @subpackage handlers
   */
  
  /**
   * Timetracking handle on_quick_add event
   *
   * @param array $quick_add_urls
   * @return null
   */
  function timetracking_handle_on_quick_add(&$quick_add_urls) {
    $quick_add_urls['timerecord'] = assemble_url('project_time_quick_add', array('project_id' => '-PROJECT-ID-'));
  } // timetracking_handle_on_quick_add

?>