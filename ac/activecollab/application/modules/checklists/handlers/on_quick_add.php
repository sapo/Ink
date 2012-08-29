<?php

  /**
   * Checklists module on_quick_add event handler
   *
   * @package activeCollab.modules.checklist
   * @subpackage handlers
   */
  
  /**
   * Checklists handle on_quick_add event
   *
   * @param array $quick_add_urls
   * @return null
   */
  function checklists_handle_on_quick_add(&$quick_add_urls) {
    $quick_add_urls['checklist'] = assemble_url('project_checklists_quick_add', array('project_id' => '-PROJECT-ID-'));
  } // checklists_handle_on_quick_add

?>