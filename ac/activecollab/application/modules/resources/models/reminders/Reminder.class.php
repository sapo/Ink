<?php

  /**
   * Reminder class
   * 
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class Reminder extends BaseReminder {
  
    /**
     * Related object
     *
     * @var ProjectObject
     */
    var $object = false;
    
    /**
     * Return related object
     *
     * @param void
     * @return ProjectObject
     */
    function getObject() {
    	if($this->object === false) {
    	  $this->object = ProjectObjects::findById($this->getObjectId());
    	} // if
    	return $this->object;
    } // getObject
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can dismiss this reminder
     *
     * @param User $user
     * @return boolean
     */
    function canDismiss($user) {
    	return $this->getUserId() == $user->getId();
    } // canDismiss
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view reminder URL
     *
     * @param void
     * @return string
     */
    function getViewUrl() {
    	return assemble_url('reminder_view', array('reminder_id' => $this->getId()));
    } // getViewUrl
    
    /**
     * Return dismiss reminder URL
     *
     * @param void
     * @return string
     */
    function getDismissUrl() {
    	return assemble_url('reminder_dismiss', array('reminder_id' => $this->getId()));
    } // getDismissUrl
  
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
    	if(!$this->validatePresenceOf('user_id')) {
    	  $errors->addError(lang('User needs to be selected'), 'user_id');
    	} // if
    	
    	if(!$this->validatePresenceOf('object_id')) {
    	  $errors->addError(lang('Object needs to be selected'), 'object_id');
    	} // if
    } // validate
  
  }

?>