<?php

  /**
   * Handle on_object_deleted event
   *
   * @package activeCollab.modules.resources
   * @subpackage handlers
   */

  /**
   * Handle on_object_deleted event
   *
   * @param ApplicationObject $object
   * @return null
   */
  function resources_handle_on_object_deleted($object) {
    if(instance_of($object, 'User')) {
      AssignmentFilters::cleanByUser($object);
    } // if
  } // resources_handle_on_object_deleted

?>