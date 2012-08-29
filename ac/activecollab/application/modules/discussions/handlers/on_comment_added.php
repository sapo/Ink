<?php

  /**
   * Even triggered when new comment is posted
   *
   * @param Comment $comment
   * @param ProjectObject $parent
   * @return null
   */
  function discussions_handle_on_comment_added(&$comment, &$parent) {
    if(instance_of($parent, 'Discussion')) {
      $parent->setLastCommentOn(new DateTimeValue());
      $parent->save();
    } // if
  } // discussions_handle_on_comment_added

?>