<?php

  /**
   * Checklists module on_project_object_completed event handler
   *
   * @package activeCollab.modules.checklists
   * @subpackage handlers
   */
  
  /**
   * Handle on_project_object_completed event
   *
   * @param ProjectObject $object
   * @param User $by
   * @param string $comment
   * @return null
   */
  function checklists_handle_on_project_object_completed(&$object, &$by, $comment) {
    if(instance_of($object, 'Task')) {
      $parent = $object->getParent();
      if(instance_of($parent, 'Checklist') && $parent->isOpen() && ($parent->countOpenTasks() == 0)) {
        $parent->complete($object->getCompletedBy());
      } // if
    } // if
  } // checklists_handle_on_project_object_completed

?>