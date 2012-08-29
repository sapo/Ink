<?php

  /**
   * System module on_before_object_insert event handler
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
  function system_handle_on_before_object_insert($object) {
    if($object->fieldExists('created_on')) {
      if(!isset($object->values['created_on'])) {
        $object->setCreatedOn(new DateTimeValue());
      } // if
    } // if
    
    $user =& get_logged_user();
    if(!instance_of($user, 'User')) {
      return;
    } // if
    
    if($object->fieldExists('created_by_id') && !isset($object->values['created_by_id'])) {
      $object->setCreatedById($user->getId());
    } // if
    
    if($object->fieldExists('created_by_name') && !isset($object->values['created_by_name'])) {
      $object->setCreatedByName($user->getDisplayName());
    } // if
    
    if($object->fieldExists('created_by_email') && !isset($object->values['created_by_email'])) {
      $object->setCreatedByEmail($user->getEmail());
    } // if
  } // system_handle_on_before_object_insert

?>