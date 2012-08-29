<?php

  /**
   * ProjectUser class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectUser extends BaseProjectUser {
    
    /**
     * Return user instance
     *
     * @param void
     * @return User
     */
    function getUser() {
      return Users::findById($this->getUserId());
    } // getUser
    
    /**
     * Return project
     *
     * @param void
     * @return Project
     */
    function getProject() {
      return Projects::findById($this->getProjectId());
    } // getProject
    
    /**
     * Cached role instance
     *
     * @var Role
     */
    var $role = false;
    
    /**
     * Return role
     *
     * @param void
     * @return Role
     */
    function getRole() {
      if($this->role === false) {
        $role_id = $this->getRoleId();
        $this->role = $role_id ? Roles::findById($this->getRoleId()) : null;
      } // if
      return $this->role;
    } // getRole
    
    /**
     * Return permission value
     *
     * @param string $permission
     * @param boolean $default
     * @return boolean
     */
    function getPermissionValue($permission, $default = PROJECT_PERMISSION_NONE) {
      if($this->getRoleId()) {
        $role = $this->getRole();
        return instance_of($role, 'Role') ? $role->getPermissionValue($permission, $default) : $default;
      } // if
      
      $permissions = $this->getPermissions();
      return isset($permissions[$permission]) ? $permissions[$permission] : $default;
    } // getPermissionValue
    
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
      if($this->validatePresenceOf('project_id') && $this->validatePresenceOf('user_id')) {
        if(!$this->validateUniquenessOf('project_id', 'user_id')) {
          $errors->addError(lang('User is already member of selected project'));
        } // if
      } else {
        $errors->addError(lang('Project and user are required'));
      } // if
    } // validate
  
  }

?>