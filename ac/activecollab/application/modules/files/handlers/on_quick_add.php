<?php

  /**
   * Files module on_quick_add handler
   *
   * @package activeCollab.modules.files
   * @subpackage handlers
   */
  
  /**
   * Files handle on_quick_add event
   *
   * @param array $quick_add_urls
   * @return null
   */
  function files_handle_on_quick_add(&$quick_add_urls) {
    $quick_add_urls['file'] = assemble_url('project_files_quick_add', array('project_id' => '-PROJECT-ID-'));
  } // files_handle_on_quick_add

?>