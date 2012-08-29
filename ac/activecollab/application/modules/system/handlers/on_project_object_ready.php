<?php

  /**
   * System module on_project_object_ready event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Handle even when entire object creation process is done
   *
   * @param ProjectObject $object
   * @return null
   */
  function system_handle_on_project_object_ready(&$object) {
    if(instance_of($object, 'ProjectObject') && $object->can_be_completed && $object->can_have_subscribers) {
      $created_by = $object->getCreatedBy();
      
      $object->sendToSubscribers('resources/task_assigned', array(
        'created_by_name' => $created_by->getDisplayName(),
        'created_by_url' => $created_by->getViewUrl(),
      ), $object->getCreatedById(), $object->getNotificationContext());
    } // if
  } // system_handle_on_project_object_ready

?>