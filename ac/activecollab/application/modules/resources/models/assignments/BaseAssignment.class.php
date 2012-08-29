<?php

  /**
   * BaseAssignment class
   */
  class BaseAssignment extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('user_id', 'object_id', 'is_owner');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    var $primary_key = array('user_id', 'object_id');
    
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
     * @return Assignment 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'assignments';
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
     * Return value of object_id field
     *
     * @param void
     * @return integer
     */
    function getObjectId() {
      return $this->getFieldValue('object_id');
    }
    
    /**
     * Set value of object_id field
     *
     * @param integer $value
     * @return integer
     */
    function setObjectId($value) {
      return $this->setFieldValue('object_id', $value);
    }

    /**
     * Return value of is_owner field
     *
     * @param void
     * @return boolean
     */
    function getIsOwner() {
      return $this->getFieldValue('is_owner');
    }
    
    /**
     * Set value of is_owner field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsOwner($value) {
      return $this->setFieldValue('is_owner', $value);
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
        case 'object_id':
          $set = intval($value);
          break;
        case 'is_owner':
          $set = boolval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>