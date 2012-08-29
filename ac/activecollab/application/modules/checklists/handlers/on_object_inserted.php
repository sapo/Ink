<?php

  /**
   * Checklists handle on_object_inserted event
   *
   * @package activeCollab.modules.checklists
   * @subpackage handlers
   */
  
  /**
   * Handle on_object_inserted event
   *
   * @param ProjectObject $object
   * @return null
   */
  function checklists_handle_on_object_inserted(&$object) {
    if(instance_of($object, 'Task')) {
      $parent = $object->getParent();
      if(instance_of($parent, 'Checklist') && $parent->isCompleted()) {
        $parent->open($object->getCreatedBy());
      } // if
    } // if
  } // checklists_handle_on_object_inserted

?>