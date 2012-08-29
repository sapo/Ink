<?php

  /**
   * BaseAssignmentFilter class
   */
  class BaseAssignmentFilter extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'name', 'group_name', 'is_private', 'user_filter', 'user_filter_data', 'project_filter', 'project_filter_data', 'date_filter', 'date_from', 'date_to', 'status_filter', 'objects_per_page', 'order_by', 'created_by_id');
    
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
     * @return AssignmentFilter 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'assignment_filters';
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
     * Return value of group_name field
     *
     * @param void
     * @return string
     */
    function getGroupName() {
      return $this->getFieldValue('group_name');
    }
    
    /**
     * Set value of group_name field
     *
     * @param string $value
     * @return string
     */
    function setGroupName($value) {
      return $this->setFieldValue('group_name', $value);
    }

    /**
     * Return value of is_private field
     *
     * @param void
     * @return boolean
     */
    function getIsPrivate() {
      return $this->getFieldValue('is_private');
    }
    
    /**
     * Set value of is_private field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsPrivate($value) {
      return $this->setFieldValue('is_private', $value);
    }

    /**
     * Return value of user_filter field
     *
     * @param void
     * @return string
     */
    function getUserFilter() {
      return $this->getFieldValue('user_filter');
    }
    
    /**
     * Set value of user_filter field
     *
     * @param string $value
     * @return string
     */
    function setUserFilter($value) {
      return $this->setFieldValue('user_filter', $value);
    }

    /**
     * Return value of user_filter_data field
     *
     * @param void
     * @return string
     */
    function getUserFilterData() {
      return $this->getFieldValue('user_filter_data');
    }
    
    /**
     * Set value of user_filter_data field
     *
     * @param string $value
     * @return string
     */
    function setUserFilterData($value) {
      return $this->setFieldValue('user_filter_data', $value);
    }

    /**
     * Return value of project_filter field
     *
     * @param void
     * @return string
     */
    function getProjectFilter() {
      return $this->getFieldValue('project_filter');
    }
    
    /**
     * Set value of project_filter field
     *
     * @param string $value
     * @return string
     */
    function setProjectFilter($value) {
      return $this->setFieldValue('project_filter', $value);
    }

    /**
     * Return value of project_filter_data field
     *
     * @param void
     * @return string
     */
    function getProjectFilterData() {
      return $this->getFieldValue('project_filter_data');
    }
    
    /**
     * Set value of project_filter_data field
     *
     * @param string $value
     * @return string
     */
    function setProjectFilterData($value) {
      return $this->setFieldValue('project_filter_data', $value);
    }

    /**
     * Return value of date_filter field
     *
     * @param void
     * @return string
     */
    function getDateFilter() {
      return $this->getFieldValue('date_filter');
    }
    
    /**
     * Set value of date_filter field
     *
     * @param string $value
     * @return string
     */
    function setDateFilter($value) {
      return $this->setFieldValue('date_filter', $value);
    }

    /**
     * Return value of date_from field
     *
     * @param void
     * @return DateValue
     */
    function getDateFrom() {
      return $this->getFieldValue('date_from');
    }
    
    /**
     * Set value of date_from field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDateFrom($value) {
      return $this->setFieldValue('date_from', $value);
    }

    /**
     * Return value of date_to field
     *
     * @param void
     * @return DateValue
     */
    function getDateTo() {
      return $this->getFieldValue('date_to');
    }
    
    /**
     * Set value of date_to field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDateTo($value) {
      return $this->setFieldValue('date_to', $value);
    }

    /**
     * Return value of status_filter field
     *
     * @param void
     * @return string
     */
    function getStatusFilter() {
      return $this->getFieldValue('status_filter');
    }
    
    /**
     * Set value of status_filter field
     *
     * @param string $value
     * @return string
     */
    function setStatusFilter($value) {
      return $this->setFieldValue('status_filter', $value);
    }

    /**
     * Return value of objects_per_page field
     *
     * @param void
     * @return integer
     */
    function getObjectsPerPage() {
      return $this->getFieldValue('objects_per_page');
    }
    
    /**
     * Set value of objects_per_page field
     *
     * @param integer $value
     * @return integer
     */
    function setObjectsPerPage($value) {
      return $this->setFieldValue('objects_per_page', $value);
    }

    /**
     * Return value of order_by field
     *
     * @param void
     * @return string
     */
    function getOrderBy() {
      return $this->getFieldValue('order_by');
    }
    
    /**
     * Set value of order_by field
     *
     * @param string $value
     * @return string
     */
    function setOrderBy($value) {
      return $this->setFieldValue('order_by', $value);
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
        case 'name':
          $set = strval($value);
          break;
        case 'group_name':
          $set = strval($value);
          break;
        case 'is_private':
          $set = boolval($value);
          break;
        case 'user_filter':
          $set = strval($value);
          break;
        case 'user_filter_data':
          $set = strval($value);
          break;
        case 'project_filter':
          $set = strval($value);
          break;
        case 'project_filter_data':
          $set = strval($value);
          break;
        case 'date_filter':
          $set = strval($value);
          break;
        case 'date_from':
          $set = dateval($value);
          break;
        case 'date_to':
          $set = dateval($value);
          break;
        case 'status_filter':
          $set = strval($value);
          break;
        case 'objects_per_page':
          $set = intval($value);
          break;
        case 'order_by':
          $set = strval($value);
          break;
        case 'created_by_id':
          $set = intval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>