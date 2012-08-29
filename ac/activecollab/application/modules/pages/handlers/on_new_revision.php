<?php

  /**
   * Pages module on_new_revision even handler
   *
   * @package activeCollab.modules.page
   * @subpackage handlers
   */
  
  /**
   * Handle on_new_revision event
   *
   * @param Page $page
   * @param PageVersion $version
   * @param User $by
   * @return null
   */
  function pages_handle_on_new_revision($page, $version, $by) {
    if(instance_of($page, 'Page')) {
      $page->sendToSubscribers('pages/new_revision', array(
        'created_by_url'  => $by->getViewUrl(), 
        'created_by_name' => $by->getDisplayName(), 
        'revision_num'    => $page->getRevisionNum(), 
        'old_url'         => $version->getViewUrl(), 
        'old_name'        => $version->getName(), 
        'old_body'        => $version->getFormattedBody(), 
        'new_url'         => $page->getViewUrl(), 
        'new_name'        => $page->getName(), 
        'new_body'        => $page->getFormattedBody(),
      ), $by->getId());
    } // if
  } // pages_handle_on_new_revision

?>