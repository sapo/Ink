<?php

  /**
   * Role class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Role extends BaseRole {
    
    /**
     * Cached array of role users
     *
     * @var array
     */
    var $users = false;
    
    /**
     * Cached number of users
     *
     * @var integer
     */
    var $users_count = false;
    
    /**
     * Return permission value
     *
     * @param string $permission
     * @param boolean $default
     * @return boolean
     */
    function getPermissionValue($permission, $default = false) {
      $permissions = $this->getPermissions();
      
      if($this->getType() == ROLE_TYPE_PROJECT) {
        return isset($permissions[$permission]) ? (integer) $permissions[$permission] : (integer) $default;
      } else {
        return isset($permissions[$permission]) ? (boolean) $permissions[$permission] : (boolean) $default;
      } // if
    } // getPermissionValue
    
    /**
     * Set value of a specific permission
     *
     * @param string $permission
     * @param boolean $value
     * @return null
     */
    function setPermissionValue($permission, $value) {
      $permissions = $this->getPermissions();
      $permissions[$permission] = (boolean) $value;
      $this->setPermissions($permissions);
    } // setPermissionValue
    
    /**
     * Return true if this is administration role
     *
     * @param void
     * @return boolean
     */
    function isAdministrator() {
      return ($this->getType() == ROLE_TYPE_SYSTEM) && $this->getPermissionValue('admin_access');
    } // isAdministrator
    
    /**
     * Return all users with this role
     *
     * @param void
     * @return array
     */
    function getUsers() {
      if($this->users === false) {
        if($this->getType() == ROLE_TYPE_SYSTEM) {
          $this->users = Users::findByRole($this);
        } else {
          $this->users = ProjectUsers::findByRole($this);
        }
      } // if
      return $this->users;
    } // getUsers
    
    /**
     * Return number of users that have this role
     *
     * @param void
     * @return integer
     */
    function getUsersCount() {
      if($this->users_count === false) {
        if($this->getType() == ROLE_TYPE_SYSTEM) {
          $this->users_count = Users::countByRole($this);
        } else {
          $this->users_count = ProjectUsers::countByRole($this);
        } // if
      } // if
      return $this->users_count;
    } // getUsersCount
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if this route can be deleted
     *
     * @param void
     * @return boolean
     */
    function canDelete() {
      return $this->getUsersCount() == 0;
    } // canDelete
  
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * View role URL
     *
     * @param void
     * @return string
     */
    function getViewUrl() {
      return assemble_url('admin_role', array('role_id' => $this->getId()));
    } // getViewUrl
    
    /**
     * Update role URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('admin_role_edit', array('role_id' => $this->getId()));
    } // getEditUrl
    
    /**
     * Return delete role URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('admin_role_delete', array('role_id' => $this->getId()));
    } // getDeleteUrl
    
    /**
     * Return set as default URL
     *
     * @param void
     * @return string
     */
    function getSetAsDefaultUrl() {
    	return assemble_url('admin_role_set_as_default', array('role_id' => $this->getId()));
    } // getSetAsDefaultUrl
    
    /**
     * Return set permission value URL
     *
     * @param string $permission
     * @return string
     */
    function getSetPermissionValueUrl($permission) {
      return assemble_url('admin_role_set_permission_value', array(
        'role_id' => $this->getId(),
        'permission_name' => $permission,
      ));
    } // getSetPermissionValueUrl
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Cached permissions array
     *
     * @var mixed
     */
    var $permissions = false;
    
    /**
     * Return permissions
     *
     * @param void
     * @return array
     */
    function getPermissions() {
      if($this->permissions === false) {
        $raw = parent::getPermissions();
    	  $this->permissions = empty($raw) ? array() : unserialize($raw);
      } // if
    	return $this->permissions;
    } // getPermissions
    
    /**
     * Set permissions values
     *
     * @param mixed
     * @return mixed
     */
    function setPermissions($value) {
      $this->permissions = $value;
    	return parent::setPermissions(serialize($value));
    } // setPermissions
    
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
      if($this->validatePresenceOf('name', 3)) {
        if(!$this->validateUniquenessOf('name')) {
          $errors->addError(lang('Role name needs to be unique'), 'name');
        } // if
      } else {
        $errors->addError(lang('Role name is required and it needs to be at least 3 characters long'), 'name');
      } // if
      
      if(($this->getType() != ROLE_TYPE_SYSTEM) && ($this->getType() != ROLE_TYPE_PROJECT)) {
        $errors->addError(lang('Please select valid role type'), 'type');
      } // if
    } // validate
    
    /**
     * Delete this role
     *
     * @param void
     * @return boolean
     */
    function delete() {
      if($this->getUsersCount() > 0) {
        return new Error('Role cannot be deleted as long as there are users having it');
      } // if
      
      return parent::delete();
    } // delete
  
  }

?>