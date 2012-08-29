<?php

  /**
   * BaseUser class
   */
  class BaseUser extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'company_id', 'role_id', 'first_name', 'last_name', 'email', 'password', 'token', 'created_on', 'updated_on', 'last_login_on', 'last_visit_on', 'last_activity_on', 'auto_assign', 'auto_assign_role_id', 'auto_assign_permissions', 'password_reset_key', 'password_reset_on');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    var $primary_key = array('id');
    
    /**
     * Name of AI field (if any)
     *
     * @var string
     */
    var $auto_increment = 'id'; 
    
    /**
     * Construct the object and if $id is present load record from database
     *
     * @param mixed $id
     * @return User 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'users';
      parent::__construct($id);
    }

    /**
     * Return value of id field
     *
     * @param void
     * @return integer
     */
    function getId() {
      return $this->getFieldValue('id');
    }
    
    /**
     * Set value of id field
     *
     * @param integer $value
     * @return integer
     */
    function setId($value) {
      return $this->setFieldValue('id', $value);
    }

    /**
     * Return value of company_id field
     *
     * @param void
     * @return integer
     */
    function getCompanyId() {
      return $this->getFieldValue('company_id');
    }
    
    /**
     * Set value of company_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCompanyId($value) {
      return $this->setFieldValue('company_id', $value);
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
     * Return value of first_name field
     *
     * @param void
     * @return string
     */
    function getFirstName() {
      return $this->getFieldValue('first_name');
    }
    
    /**
     * Set value of first_name field
     *
     * @param string $value
     * @return string
     */
    function setFirstName($value) {
      return $this->setFieldValue('first_name', $value);
    }

    /**
     * Return value of last_name field
     *
     * @param void
     * @return string
     */
    function getLastName() {
      return $this->getFieldValue('last_name');
    }
    
    /**
     * Set value of last_name field
     *
     * @param string $value
     * @return string
     */
    function setLastName($value) {
      return $this->setFieldValue('last_name', $value);
    }

    /**
     * Return value of email field
     *
     * @param void
     * @return string
     */
    function getEmail() {
      return $this->getFieldValue('email');
    }
    
    /**
     * Set value of email field
     *
     * @param string $value
     * @return string
     */
    function setEmail($value) {
      return $this->setFieldValue('email', $value);
    }

    /**
     * Return value of password field
     *
     * @param void
     * @return string
     */
    function getPassword() {
      return $this->getFieldValue('password');
    }
    
    /**
     * Set value of password field
     *
     * @param string $value
     * @return string
     */
    function setPassword($value) {
      return $this->setFieldValue('password', $value);
    }

    /**
     * Return value of token field
     *
     * @param void
     * @return string
     */
    function getToken() {
      return $this->getFieldValue('token');
    }
    
    /**
     * Set value of token field
     *
     * @param string $value
     * @return string
     */
    function setToken($value) {
      return $this->setFieldValue('token', $value);
    }

    /**
     * Return value of created_on field
     *
     * @param void
     * @return DateTimeValue
     */
    function getCreatedOn() {
      return $this->getFieldValue('created_on');
    }
    
    /**
     * Set value of created_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setCreatedOn($value) {
      return $this->setFieldValue('created_on', $value);
    }

    /**
     * Return value of updated_on field
     *
     * @param void
     * @return DateTimeValue
     */
    function getUpdatedOn() {
      return $this->getFieldValue('updated_on');
    }
    
    /**
     * Set value of updated_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setUpdatedOn($value) {
      return $this->setFieldValue('updated_on', $value);
    }

    /**
     * Return value of last_login_on field
     *
     * @param void
     * @return DateTimeValue
     */
    function getLastLoginOn() {
      return $this->getFieldValue('last_login_on');
    }
    
    /**
     * Set value of last_login_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setLastLoginOn($value) {
      return $this->setFieldValue('last_login_on', $value);
    }

    /**
     * Return value of last_visit_on field
     *
     * @param void
     * @return DateTimeValue
     */
    function getLastVisitOn() {
      return $this->getFieldValue('last_visit_on');
    }
    
    /**
     * Set value of last_visit_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setLastVisitOn($value) {
      return $this->setFieldValue('last_visit_on', $value);
    }

    /**
     * Return value of last_activity_on field
     *
     * @param void
     * @return DateTimeValue
     */
    function getLastActivityOn() {
      return $this->getFieldValue('last_activity_on');
    }
    
    /**
     * Set value of last_activity_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setLastActivityOn($value) {
      return $this->setFieldValue('last_activity_on', $value);
    }

    /**
     * Return value of auto_assign field
     *
     * @param void
     * @return boolean
     */
    function getAutoAssign() {
      return $this->getFieldValue('auto_assign');
    }
    
    /**
     * Set value of auto_assign field
     *
     * @param boolean $value
     * @return boolean
     */
    function setAutoAssign($value) {
      return $this->setFieldValue('auto_assign', $value);
    }

    /**
     * Return value of auto_assign_role_id field
     *
     * @param void
     * @return integer
     */
    function getAutoAssignRoleId() {
      return $this->getFieldValue('auto_assign_role_id');
    }
    
    /**
     * Set value of auto_assign_role_id field
     *
     * @param integer $value
     * @return integer
     */
    function setAutoAssignRoleId($value) {
      return $this->setFieldValue('auto_assign_role_id', $value);
    }

    /**
     * Return value of auto_assign_permissions field
     *
     * @param void
     * @return string
     */
    function getAutoAssignPermissions() {
      return $this->getFieldValue('auto_assign_permissions');
    }
    
    /**
     * Set value of auto_assign_permissions field
     *
     * @param string $value
     * @return string
     */
    function setAutoAssignPermissions($value) {
      return $this->setFieldValue('auto_assign_permissions', $value);
    }

    /**
     * Return value of password_reset_key field
     *
     * @param void
     * @return string
     */
    function getPasswordResetKey() {
      return $this->getFieldValue('password_reset_key');
    }
    
    /**
     * Set value of password_reset_key field
     *
     * @param string $value
     * @return string
     */
    function setPasswordResetKey($value) {
      return $this->setFieldValue('password_reset_key', $value);
    }

    /**
     * Return value of password_reset_on field
     *
     * @param void
     * @return DateTimeValue
     */
    function getPasswordResetOn() {
      return $this->getFieldValue('password_reset_on');
    }
    
    /**
     * Set value of password_reset_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setPasswordResetOn($value) {
      return $this->setFieldValue('password_reset_on', $value);
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
        case 'id':
          $set = intval($value);
          break;
        case 'company_id':
          $set = intval($value);
          break;
        case 'role_id':
          $set = intval($value);
          break;
        case 'first_name':
          $set = strval($value);
          break;
        case 'last_name':
          $set = strval($value);
          break;
        case 'email':
          $set = strval($value);
          break;
        case 'password':
          $set = strval($value);
          break;
        case 'token':
          $set = strval($value);
          break;
        case 'created_on':
          $set = datetimeval($value);
          break;
        case 'updated_on':
          $set = datetimeval($value);
          break;
        case 'last_login_on':
          $set = datetimeval($value);
          break;
        case 'last_visit_on':
          $set = datetimeval($value);
          break;
        case 'last_activity_on':
          $set = datetimeval($value);
          break;
        case 'auto_assign':
          $set = boolval($value);
          break;
        case 'auto_assign_role_id':
          $set = intval($value);
          break;
        case 'auto_assign_permissions':
          $set = strval($value);
          break;
        case 'password_reset_key':
          $set = strval($value);
          break;
        case 'password_reset_on':
          $set = datetimeval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>