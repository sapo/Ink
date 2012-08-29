<?php

  /**
   * Checklists handle on_project_object_restored event
   *
   * @package activeCollab.modules.checklists
   * @subpackage handlers
   */
  
  /**
   * Handle on_project_object_restored event
   *
   * @param ProjectObject $object
   * @return null
   */
  function checklists_handle_on_project_object_restored(&$object) {
    if(instance_of($object, 'Task')) {
      $parent = $object->getParent();
      if(instance_of($parent, 'Checklist') && $parent->isCompleted()) {
        $parent->open($object->getUpdatedBy());
      } // if
    } // if
  } // checklists_handle_on_project_object_restored

?>