<?php

  /**
   * BaseProjectUser class
   */
  class BaseProjectUser extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('user_id', 'project_id', 'permissions', 'role_id');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    var $primary_key = array('user_id', 'project_id');
    
    /**
     * Name of AI field (if any)
     *
     * @var string
     */
    var $auto_increment = NULL; 
    
    /**
     * Construct the object and if $id is present load record from database
     *
     * @param mixed $id
     * @return ProjectUser 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'project_users';
      parent::__construct($id);
    }

    /**
     * Return value of user_id field
     *
     * @param void
     * @return integer
     */
    function getUserId() {
      return $this->getFieldValue('user_id');
    }
    
    /**
     * Set value of user_id field
     *
     * @param integer $value
     * @return integer
     */
    function setUserId($value) {
      return $this->setFieldValue('user_id', $value);
    }

    /**
     * Return value of project_id field
     *
     * @param void
     * @return integer
     */
    function getProjectId() {
      return $this->getFieldValue('project_id');
    }
    
    /**
     * Set value of project_id field
     *
     * @param integer $value
     * @return integer
     */
    function setProjectId($value) {
      return $this->setFieldValue('project_id', $value);
    }

    /**
     * Return value of permissions field
     *
     * @param void
     * @return string
     */
    function getPermissions() {
      return $this->getFieldValue('permissions');
    }
    
    /**
     * Set value of permissions field
     *
     * @param string $value
     * @return string
     */
    function setPermissions($value) {
      return $this->setFieldValue('permissions', $value);
    }

    /**
     * Return value of role_id field
     *
     * @param void
     * @return integer
     */
    function getRoleId() {
      return $this->getFieldValue('role_id');
    }
    
    /**
     * Set value of role_id field
     *
     * @param integer $value
     * @return integer
     */
    function setRoleId($value) {
      return $this->setFieldValue('role_id', $value);
    }

    /**
     * Set value of specific field
     *
     * @param string $name
     * @param mided $value
     * @return mixed
     */
    function setFieldValue($name, $value) {
      $real_name = $this->realFieldName($name);
      
      $set = $value;
      switch($real_name) {
        case 'user_id':
          $set = intval($value);
          break;
        case 'project_id':
          $set = intval($value);
          break;
        case 'permissions':
          $set = strval($value);
          break;
        case 'role_id':
          $set = intval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>