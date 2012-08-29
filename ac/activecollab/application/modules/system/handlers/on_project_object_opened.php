<?php

  /**
   * System handle on_project_object_opened event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Handle situation when object is reopened
   *
   * @param ProjectObject $object
   * @param User $by
   * @return null
   */
  function system_handle_on_project_object_opened(&$object, &$by) {
    if(instance_of($object, 'ProjectObject') && $object->can_be_completed && $object->can_have_subscribers) {
      $created_by = $object->getCreatedBy();
      $reopened_by = $object->getUpdatedBy();
      
      $object->sendToSubscribers('resources/task_reopened', array(
        'created_by_name' => $created_by->getDisplayName(),
        'created_by_url' => $created_by->getViewUrl(),
        'reopened_by_name' => $reopened_by->getDisplayName(),
        'reopened_by_url' => $reopened_by->getViewUrl(),
      ), $by->getId(), $object->getNotificationContext());
    } // if
  } // system_handleon_project_object_opened

?>