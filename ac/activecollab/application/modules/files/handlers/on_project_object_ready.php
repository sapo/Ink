<?php

  /**
   * Files module on_project_object_ready event handler
   *
   * @package activeCollab.modules.files
   * @subpackage handlers
   */
  
  /**
   * Handle even when entire object creation process is done
   *
   * @param ProjectObject $object
   * @return null
   */
  function files_handle_on_project_object_ready(&$object) {
    if(instance_of($object, 'File')) {
      $created_by = $object->getCreatedBy();
      $object->sendToSubscribers('files/new_file', array(
        'created_by_name' => $created_by->getDisplayName(),
        'created_by_url' => $created_by->getViewUrl(),
      ), $object->getCreatedById());
    } // if
  } // files_handle_on_project_object_ready

?>