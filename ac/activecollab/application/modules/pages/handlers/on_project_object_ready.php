<?php

  /**
   * Pages module on_project_object_ready event handler
   *
   * @package activeCollab.modules.pages
   * @subpackage handlers
   */
  
  /**
   * Handle even when entire object creation process is done
   *
   * @param ProjectObject $object
   * @return null
   */
  function pages_handle_on_project_object_ready(&$object) {
    if(instance_of($object, 'Page')) {
      $created_by = $object->getCreatedBy();
      $object->sendToSubscribers('pages/new_page', array(
        'created_by_name' => $created_by->getDisplayName(),
        'created_by_url' => $created_by->getViewUrl(),
      ), $object->getCreatedById());
    } // if
  } // pages_handle_on_project_object_ready

?>