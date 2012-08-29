<?php

  /**
   * Class that all application objects inherit
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ApplicationObject extends DataObject {
    
    /**
     * Variable where we store inline attachments ids
     */
    var $inline_attachments_ids;
    
    /**
     * Filters all additional fields before calling DataObject::setAttributes()
     * 
     * @param array $attributes
     * @return null
     */
    function setAttributes($attributes) {
  	  if(is_array($attributes) && isset($attributes['inline_attachments'])) {
  	    $this->inline_attachments_ids = $attributes['inline_attachments'];
  	  } // if
	    parent::setAttributes($attributes);
    } // setAttributes
    
    /**
     * Return array or property => value pairs that describes this object
     * 
     * $user is an instance of user who requested description - it used to get 
     * only the data this user can see
     *
     * @param User $user
     * @return array
     */
    function describe($user) {
      
    } // describe
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return name
     *
     * @param void
     * @return string
     */
    function getName() {
      return '-- unknown --';
    } // getName
    
    /**
     * Return view object URL
     *
     * @param void
     * @return string
     */
    function getViewUrl() {
      return '#';
    } // getViewUrl
    
    /**
     * Return datetime value of object creation
     *
     * @param void
     * @return DateTimeValue
     */
    function getCreatedOn() {
      return null;
    } // getCreatedOn
    
    /**
     * Cached created by value
     *
     * @var User
     */
    var $created_by = false;
  
    /**
     * Return user who created this object
     *
     * @param void
     * @return User
     */
    function getCreatedBy() {
      if($this->created_by === false) {
        $created_by_id = $this->getCreatedById();
        
        if($created_by_id) {
          $this->created_by = Users::findById($created_by_id);
        } // if
        
        if(!instance_of($this->created_by, 'User')) {
          $this->created_by = new AnonymousUser($this->getCreatedByName(), $this->getCreatedByEmail());
        } // if
      } // if
      return $this->created_by;
    } // getCreatedBy
    
    /**
     * Set person who create this object
     * 
     * $created_by can be an insance of User or AnonymousUser class or null
     *
     * @param mixed $created_by
     * @return null
     */
    function setCreatedBy($created_by) {
      if($created_by === null) {
        $this->setCreatedById(0);
        $this->setCreatedByName('');
        $this->setCreatedByEmail('');
      } elseif(instance_of($created_by, 'User')) {
        $this->setCreatedById($created_by->getId());
        $this->setCreatedByName($created_by->getDisplayName());
        $this->setCreatedByEmail($created_by->getEmail());
      } elseif(instance_of($created_by, 'AnonymousUser')) {
        $this->setCreatedById(0);
        $this->setCreatedByName($created_by->getName());
        $this->setCreatedByEmail($created_by->getEmail());
      } // if
    } // setCreatedBy
    
    /**
     * Return user who last updated this object
     *
     * @param void
     * @return User
     */
    function getUpdatedBy() {
      if($this->updated_by === false) {
        $updated_by_id = $this->getUpdatedById();
        
        if($updated_by_id) {
          $this->updated_by = Users::findById($updated_by_id);
        } else {
          $this->updated_by = new AnonymousUser($this->getUpdatedByName(), $this->getUpdatedByEmail());
        } // if
      } // if
      return $this->updated_by;
    } // getUpdatedBy
    
    /**
     * Set person who updated this object
     * 
     * $updated_by can be an insance of User or AnonymousUser class or null
     *
     * @param mixed $updated_by
     * @return null
     */
    function setUpdatedBy($updated_by) {
      if($updated_by === null) {
        $this->setUpdatedById(0);
        $this->setUpdatedByName('');
        $this->setUpdatedByEmail('');
      } elseif(instance_of($updated_by, 'User')) {
        $this->setUpdatedById($updated_by->getId());
        $this->setUpdatedByName($updated_by->getDisplayName());
        $this->setUpdatedByEmail($updated_by->getEmail());
      } elseif(instance_of($updated_by, 'AnonymousUser')) {
        $this->setUpdatedById(0);
        $this->setUpdatedByName($updated_by->getName());
        $this->setUpdatedByEmail($updated_by->getEmail());
      } // if
    } // setUpdatedBy
    
    /**
     * Save this object into the database
     * 
     * @param void
     * @return boolean
     */
    function save() {
      $save = parent::save();
      if ($save && !is_error($save)) {
        if (is_foreachable($this->inline_attachments_ids)) {
          $save = db_execute(db_prepare_string('UPDATE '.TABLE_PREFIX.'attachments SET parent_id = ?, parent_type = ? WHERE id IN (?)', array($this->getId(), get_class($this), $this->inline_attachments_ids)));
        } // if
      } // if
      return $save;
    } // save
  
  } // ApplicationObject

?>