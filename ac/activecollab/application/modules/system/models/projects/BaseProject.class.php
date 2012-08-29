<?php

  /**
   * BaseProject class
   */
  class BaseProject extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'company_id', 'group_id', 'name', 'leader_id', 'leader_name', 'leader_email', 'overview', 'status', 'type', 'default_visibility', 'starts_on', 'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email', 'created_on', 'updated_on', 'created_by_id', 'created_by_name', 'created_by_email', 'open_tasks_count', 'total_tasks_count');
    
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
     * @return Project 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'projects';
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
     * Return value of group_id field
     *
     * @param void
     * @return integer
     */
    function getGroupId() {
      return $this->getFieldValue('group_id');
    }
    
    /**
     * Set value of group_id field
     *
     * @param integer $value
     * @return integer
     */
    function setGroupId($value) {
      return $this->setFieldValue('group_id', $value);
    }

    /**
     * Return value of name field
     *
     * @param void
     * @return string
     */
    function getName() {
      return $this->getFieldValue('name');
    }
    
    /**
     * Set value of name field
     *
     * @param string $value
     * @return string
     */
    function setName($value) {
      return $this->setFieldValue('name', $value);
    }

    /**
     * Return value of leader_id field
     *
     * @param void
     * @return integer
     */
    function getLeaderId() {
      return $this->getFieldValue('leader_id');
    }
    
    /**
     * Set value of leader_id field
     *
     * @param integer $value
     * @return integer
     */
    function setLeaderId($value) {
      return $this->setFieldValue('leader_id', $value);
    }

    /**
     * Return value of leader_name field
     *
     * @param void
     * @return string
     */
    function getLeaderName() {
      return $this->getFieldValue('leader_name');
    }
    
    /**
     * Set value of leader_name field
     *
     * @param string $value
     * @return string
     */
    function setLeaderName($value) {
      return $this->setFieldValue('leader_name', $value);
    }

    /**
     * Return value of leader_email field
     *
     * @param void
     * @return string
     */
    function getLeaderEmail() {
      return $this->getFieldValue('leader_email');
    }
    
    /**
     * Set value of leader_email field
     *
     * @param string $value
     * @return string
     */
    function setLeaderEmail($value) {
      return $this->setFieldValue('leader_email', $value);
    }

    /**
     * Return value of overview field
     *
     * @param void
     * @return string
     */
    function getOverview() {
      return $this->getFieldValue('overview');
    }
    
    /**
     * Set value of overview field
     *
     * @param string $value
     * @return string
     */
    function setOverview($value) {
      return $this->setFieldValue('overview', $value);
    }

    /**
     * Return value of status field
     *
     * @param void
     * @return string
     */
    function getStatus() {
      return $this->getFieldValue('status');
    }
    
    /**
     * Set value of status field
     *
     * @param string $value
     * @return string
     */
    function setStatus($value) {
      return $this->setFieldValue('status', $value);
    }

    /**
     * Return value of type field
     *
     * @param void
     * @return string
     */
    function getType() {
      return $this->getFieldValue('type');
    }
    
    /**
     * Set value of type field
     *
     * @param string $value
     * @return string
     */
    function setType($value) {
      return $this->setFieldValue('type', $value);
    }

    /**
     * Return value of default_visibility field
     *
     * @param void
     * @return boolean
     */
    function getDefaultVisibility() {
      return $this->getFieldValue('default_visibility');
    }
    
    /**
     * Set value of default_visibility field
     *
     * @param boolean $value
     * @return boolean
     */
    function setDefaultVisibility($value) {
      return $this->setFieldValue('default_visibility', $value);
    }

    /**
     * Return value of starts_on field
     *
     * @param void
     * @return DateValue
     */
    function getStartsOn() {
      return $this->getFieldValue('starts_on');
    }
    
    /**
     * Set value of starts_on field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setStartsOn($value) {
      return $this->setFieldValue('starts_on', $value);
    }

    /**
     * Return value of completed_on field
     *
     * @param void
     * @return DateTimeValue
     */
    function getCompletedOn() {
      return $this->getFieldValue('completed_on');
    }
    
    /**
     * Set value of completed_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setCompletedOn($value) {
      return $this->setFieldValue('completed_on', $value);
    }

    /**
     * Return value of completed_by_id field
     *
     * @param void
     * @return integer
     */
    function getCompletedById() {
      return $this->getFieldValue('completed_by_id');
    }
    
    /**
     * Set value of completed_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCompletedById($value) {
      return $this->setFieldValue('completed_by_id', $value);
    }

    /**
     * Return value of completed_by_name field
     *
     * @param void
     * @return string
     */
    function getCompletedByName() {
      return $this->getFieldValue('completed_by_name');
    }
    
    /**
     * Set value of completed_by_name field
     *
     * @param string $value
     * @return string
     */
    function setCompletedByName($value) {
      return $this->setFieldValue('completed_by_name', $value);
    }

    /**
     * Return value of completed_by_email field
     *
     * @param void
     * @return string
     */
    function getCompletedByEmail() {
      return $this->getFieldValue('completed_by_email');
    }
    
    /**
     * Set value of completed_by_email field
     *
     * @param string $value
     * @return string
     */
    function setCompletedByEmail($value) {
      return $this->setFieldValue('completed_by_email', $value);
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
     * Return value of created_by_id field
     *
     * @param void
     * @return integer
     */
    function getCreatedById() {
      return $this->getFieldValue('created_by_id');
    }
    
    /**
     * Set value of created_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCreatedById($value) {
      return $this->setFieldValue('created_by_id', $value);
    }

    /**
     * Return value of created_by_name field
     *
     * @param void
     * @return string
     */
    function getCreatedByName() {
      return $this->getFieldValue('created_by_name');
    }
    
    /**
     * Set value of created_by_name field
     *
     * @param string $value
     * @return string
     */
    function setCreatedByName($value) {
      return $this->setFieldValue('created_by_name', $value);
    }

    /**
     * Return value of created_by_email field
     *
     * @param void
     * @return string
     */
    function getCreatedByEmail() {
      return $this->getFieldValue('created_by_email');
    }
    
    /**
     * Set value of created_by_email field
     *
     * @param string $value
     * @return string
     */
    function setCreatedByEmail($value) {
      return $this->setFieldValue('created_by_email', $value);
    }

    /**
     * Return value of open_tasks_count field
     *
     * @param void
     * @return integer
     */
    function getOpenTasksCount() {
      return $this->getFieldValue('open_tasks_count');
    }
    
    /**
     * Set value of open_tasks_count field
     *
     * @param integer $value
     * @return integer
     */
    function setOpenTasksCount($value) {
      return $this->setFieldValue('open_tasks_count', $value);
    }

    /**
     * Return value of total_tasks_count field
     *
     * @param void
     * @return integer
     */
    function getTotalTasksCount() {
      return $this->getFieldValue('total_tasks_count');
    }
    
    /**
     * Set value of total_tasks_count field
     *
     * @param integer $value
     * @return integer
     */
    function setTotalTasksCount($value) {
      return $this->setFieldValue('total_tasks_count', $value);
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
        case 'group_id':
          $set = intval($value);
          break;
        case 'name':
          $set = strval($value);
          break;
        case 'leader_id':
          $set = intval($value);
          break;
        case 'leader_name':
          $set = strval($value);
          break;
        case 'leader_email':
          $set = strval($value);
          break;
        case 'overview':
          $set = strval($value);
          break;
        case 'status':
          $set = strval($value);
          break;
        case 'type':
          $set = strval($value);
          break;
        case 'default_visibility':
          $set = boolval($value);
          break;
        case 'starts_on':
          $set = dateval($value);
          break;
        case 'completed_on':
          $set = datetimeval($value);
          break;
        case 'completed_by_id':
          $set = intval($value);
          break;
        case 'completed_by_name':
          $set = strval($value);
          break;
        case 'completed_by_email':
          $set = strval($value);
          break;
        case 'created_on':
          $set = datetimeval($value);
          break;
        case 'updated_on':
          $set = datetimeval($value);
          break;
        case 'created_by_id':
          $set = intval($value);
          break;
        case 'created_by_name':
          $set = strval($value);
          break;
        case 'created_by_email':
          $set = strval($value);
          break;
        case 'open_tasks_count':
          $set = intval($value);
          break;
        case 'total_tasks_count':
          $set = intval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>