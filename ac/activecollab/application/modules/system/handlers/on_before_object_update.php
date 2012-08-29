<?php

  /**
   * System module on_before_object_update event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Set created_ properties for a given object if not set
   *
   * @param DataObject $object
   * @return null
   */
  function system_handle_on_before_object_update($object) {
    if($object->fieldExists('updated_on')) {
      $object->setUpdatedOn(new DateTimeValue());
    } // if
    
    $user =& get_logged_user();
    if(!instance_of($user, 'User')) {
      return;
    } // if
    
    if($object->fieldExists('updated_by_id')) {
      $object->setUpdatedById($user->getId());
    } // if
    
    if($object->fieldExists('updated_by_name')) {
      $object->setUpdatedByName($user->getDisplayName());
    } // if
    
    if($object->fieldExists('updated_by_email')) {
      $object->setUpdatedByEmail($user->getEmail());
    } // if
  } // system_handle_on_before_object_insert

?>