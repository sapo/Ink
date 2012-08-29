<?php

  /**
   * Resources module on_comment_deleted event handler
   *
   * @package activeCollab.modules.resources
   * @subpackage handlers
   */

  /**
   * Handle on on_comment_deleted event
   *
   * @param Comment $comment
   * @param ProjectObject $parent
   * @return null
   */
  function resources_handle_on_comment_deleted(&$comment, &$parent) {
    if(instance_of($parent, 'ProjectObject')) {
      $parent->refreshCommentsCount();
    } // if
  } // resources_handle_on_comment_deleted

?>