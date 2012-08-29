<?php

  /**
   * BaseTimeReport class
   */
  class BaseTimeReport extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'name', 'group_name', 'is_default', 'user_filter', 'user_filter_data', 'billable_filter', 'date_filter', 'date_from', 'date_to', 'sum_by_user');
    
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
     * @return TimeReport 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'time_reports';
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
     * Return value of is_default field
     *
     * @param void
     * @return boolean
     */
    function getIsDefault() {
      return $this->getFieldValue('is_default');
    }
    
    /**
     * Set value of is_default field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsDefault($value) {
      return $this->setFieldValue('is_default', $value);
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
     * Return value of billable_filter field
     *
     * @param void
     * @return string
     */
    function getBillableFilter() {
      return $this->getFieldValue('billable_filter');
    }
    
    /**
     * Set value of billable_filter field
     *
     * @param string $value
     * @return string
     */
    function setBillableFilter($value) {
      return $this->setFieldValue('billable_filter', $value);
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
     * Return value of sum_by_user field
     *
     * @param void
     * @return boolean
     */
    function getSumByUser() {
      return $this->getFieldValue('sum_by_user');
    }
    
    /**
     * Set value of sum_by_user field
     *
     * @param boolean $value
     * @return boolean
     */
    function setSumByUser($value) {
      return $this->setFieldValue('sum_by_user', $value);
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
        case 'is_default':
          $set = boolval($value);
          break;
        case 'user_filter':
          $set = strval($value);
          break;
        case 'user_filter_data':
          $set = strval($value);
          break;
        case 'billable_filter':
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
        case 'sum_by_user':
          $set = boolval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>