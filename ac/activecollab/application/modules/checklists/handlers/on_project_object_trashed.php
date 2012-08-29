<?php

  /**
   * Checklists module handle on_project_object_trashed event
   *
   * @package activeCollab.modules.checklists
   * @subpackage handlers
   */
  
  /**
   * Handle on_project_object_trashed event
   *
   * @param ProjectObject $object
   * @return null
   */
  function checklists_handle_on_project_object_trashed(&$object) {
    if(instance_of($object, 'Task')) {
      $parent = $object->getParent();
      if(instance_of($parent, 'Checklist') && $parent->isOpen() && ($parent->countOpenTasks() == 0)) {
        $parent->complete($object->getUpdatedBy());
      } // if
    } // if
  } // checklists_handle_on_project_object_trashed

?>