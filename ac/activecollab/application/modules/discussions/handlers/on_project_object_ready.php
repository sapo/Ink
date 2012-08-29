<?php

  /**
   * Discussions module on_project_object_ready event handler
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */
  
  /**
   * Handle even when entire object creation process is done
   *
   * @param Discussion $object
   * @return null
   */
  function discussions_handle_on_project_object_ready(&$object) {
    if(instance_of($object, 'Discussion')) {
      $created_by = $object->getCreatedBy();
      
      $object->sendToSubscribers('discussions/new_discussion', array(
        'created_by_name' => $created_by->getDisplayName(),
        'created_by_url' => $created_by->getViewUrl(),
        'last_comment_body' => $object->getFormattedBody(),
      ), $object->getCreatedById(), $object);
    } // if
  } // discussions_handle_on_project_object_ready

?>