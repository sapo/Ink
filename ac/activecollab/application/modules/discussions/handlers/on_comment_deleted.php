<?php

  /**
   * Discussions module on_comment_deleted event handler
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Handle on on_comment_deleted event
   *
   * @param Comment $comment
   * @param Discussion $parent
   * @return null
   */
  function discussions_handle_on_comment_deleted(&$comment, &$parent) {
    if(instance_of($parent, 'Discussion')) {
      $last_comment = $parent->getLastComment($parent->getState(), $comment->getVisibility());
      if(instance_of($last_comment, 'Comment')) {
        $parent->setLastCommentOn($last_comment->getCreatedOn());
      } else {
        $parent->setLastCommentOn(null);
      } // if
      $parent->save();
    } // if
  } // discussions_handle_on_comment_deleted

?>