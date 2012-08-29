<?php

  /**
   * BaseCompany class
   */
  class BaseCompany extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'name', 'created_on', 'updated_on', 'is_owner', 'is_archived');
    
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
     * @return Company 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'companies';
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
     * Return value of is_archived field
     *
     * @param void
     * @return boolean
     */
    function getIsArchived() {
      return $this->getFieldValue('is_archived');
    }
    
    /**
     * Set value of is_archived field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsArchived($value) {
      return $this->setFieldValue('is_archived', $value);
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
        case 'created_on':
          $set = datetimeval($value);
          break;
        case 'updated_on':
          $set = datetimeval($value);
          break;
        case 'is_owner':
          $set = boolval($value);
          break;
        case 'is_archived':
          $set = boolval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>