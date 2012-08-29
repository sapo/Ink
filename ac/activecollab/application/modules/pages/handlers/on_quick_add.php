<?php

  /**
   * Pages module on_quick_add handler
   *
   * @package activeCollab.modules.pages
   * @subpackage handlers
   */
  
  /**
   * Pages handle on_quick_add event
   *
   * @param array $quick_add_urls
   * @return null
   */
  function pages_handle_on_quick_add(&$quick_add_urls) {
    $quick_add_urls['page'] = assemble_url('project_pages_quick_add', array('project_id' => '-PROJECT-ID-'));
  } // pages_handle_on_quick_add

?>