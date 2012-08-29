<?php

  /**
   * Checklists handle on_project_object_opened event
   *
   * @package activeCollab.modules.checklists
   * @subpackage handlers
   */
  
  /**
   * Handle on_project_object_opened event
   *
   * @param ProjectObject $object
   * @param User $by
   * @return null
   */
  function checklists_handle_on_project_object_opened(&$object, &$by) {
    if(instance_of($object, 'Task')) {
      $parent = $object->getParent();
      if(instance_of($parent, 'Checklist') && $parent->isCompleted()) {
        $parent->open($by);
      } // if
    } // if
  } // checklists_handle_on_project_object_opened

?>