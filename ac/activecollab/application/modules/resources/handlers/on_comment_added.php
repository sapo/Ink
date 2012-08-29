<?php

  /**
   * Resources module handle on_comment_added event
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Handle on_comment_added event (send email notifications)
   *
   * @param Comment $comment
   * @param ProjectObject $parent
   * @return null
   */
  function resources_handle_on_comment_added(&$comment, &$parent) {
    if(instance_of($parent, 'ProjectObject')) {
      $parent->refreshCommentsCount();
      
      if($comment->send_notification) {
        $created_by = $comment->getCreatedBy();
        $parent->sendToSubscribers('resources/new_comment', array(
          'comment_body' => $comment->getFormattedBody(),
          'comment_url' => $comment->getViewUrl(),
          'created_by_url' => $created_by->getViewUrl(),
          'created_by_name' => $created_by->getDisplayName(),
        ), $comment->getCreatedById(), $parent);
      } // if
    } // if
  } // resources_handle_on_comment_added

?>