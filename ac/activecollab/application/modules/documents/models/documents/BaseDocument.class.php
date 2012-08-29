<?php

  /**
   * BaseDocument class
   */
  class BaseDocument extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'category_id', 'type', 'name', 'body', 'mime_type', 'visibility', 'is_pinned', 'created_by_id', 'created_by_name', 'created_by_email', 'created_on');
    
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
     * @return Document 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'documents';
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
     * Return value of category_id field
     *
     * @param void
     * @return integer
     */
    function getCategoryId() {
      return $this->getFieldValue('category_id');
    }
    
    /**
     * Set value of category_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCategoryId($value) {
      return $this->setFieldValue('category_id', $value);
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
     * Return value of body field
     *
     * @param void
     * @return string
     */
    function getBody() {
      return $this->getFieldValue('body');
    }
    
    /**
     * Set value of body field
     *
     * @param string $value
     * @return string
     */
    function setBody($value) {
      return $this->setFieldValue('body', $value);
    }

    /**
     * Return value of mime_type field
     *
     * @param void
     * @return string
     */
    function getMimeType() {
      return $this->getFieldValue('mime_type');
    }
    
    /**
     * Set value of mime_type field
     *
     * @param string $value
     * @return string
     */
    function setMimeType($value) {
      return $this->setFieldValue('mime_type', $value);
    }

    /**
     * Return value of visibility field
     *
     * @param void
     * @return integer
     */
    function getVisibility() {
      return $this->getFieldValue('visibility');
    }
    
    /**
     * Set value of visibility field
     *
     * @param integer $value
     * @return integer
     */
    function setVisibility($value) {
      return $this->setFieldValue('visibility', $value);
    }

    /**
     * Return value of is_pinned field
     *
     * @param void
     * @return boolean
     */
    function getIsPinned() {
      return $this->getFieldValue('is_pinned');
    }
    
    /**
     * Set value of is_pinned field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsPinned($value) {
      return $this->setFieldValue('is_pinned', $value);
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
        case 'category_id':
          $set = intval($value);
          break;
        case 'type':
          $set = strval($value);
          break;
        case 'name':
          $set = strval($value);
          break;
        case 'body':
          $set = strval($value);
          break;
        case 'mime_type':
          $set = strval($value);
          break;
        case 'visibility':
          $set = intval($value);
          break;
        case 'is_pinned':
          $set = boolval($value);
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
        case 'created_on':
          $set = datetimeval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>