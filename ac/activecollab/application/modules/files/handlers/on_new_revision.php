<?php

  /**
   * Files module handle on_new_revision event
   *
   * @package activeCollab.modules.files
   * @subpackage handlers
   */
  
  /**
   * Handle on_new_revision event
   *
   * @param File $new
   * @param Attachment $old
   * @param User $by
   * @return null
   */
  function files_handle_on_new_revision($new, $old, $by) {
    if(instance_of($new, 'File')) {
      $new->sendToSubscribers('files/new_revision', array(
        'created_by_url' => $by->getViewUrl(), 
        'created_by_name' => $by->getDisplayName(),
      ), $by->getId());
    } // if
  } // files_handle_on_new_revision

?>