<?php

  /**
   * BaseAttachment class
   */
  class BaseAttachment extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'parent_id', 'parent_type', 'name', 'mime_type', 'size', 'location', 'attachment_type', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email');
    
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
     * @return Attachment 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'attachments';
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
     * Return value of parent_id field
     *
     * @param void
     * @return integer
     */
    function getParentId() {
      return $this->getFieldValue('parent_id');
    }
    
    /**
     * Set value of parent_id field
     *
     * @param integer $value
     * @return integer
     */
    function setParentId($value) {
      return $this->setFieldValue('parent_id', $value);
    }

    /**
     * Return value of parent_type field
     *
     * @param void
     * @return string
     */
    function getParentType() {
      return $this->getFieldValue('parent_type');
    }
    
    /**
     * Set value of parent_type field
     *
     * @param string $value
     * @return string
     */
    function setParentType($value) {
      return $this->setFieldValue('parent_type', $value);
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
     * Return value of size field
     *
     * @param void
     * @return integer
     */
    function getSize() {
      return $this->getFieldValue('size');
    }
    
    /**
     * Set value of size field
     *
     * @param integer $value
     * @return integer
     */
    function setSize($value) {
      return $this->setFieldValue('size', $value);
    }

    /**
     * Return value of location field
     *
     * @param void
     * @return string
     */
    function getLocation() {
      return $this->getFieldValue('location');
    }
    
    /**
     * Set value of location field
     *
     * @param string $value
     * @return string
     */
    function setLocation($value) {
      return $this->setFieldValue('location', $value);
    }

    /**
     * Return value of attachment_type field
     *
     * @param void
     * @return string
     */
    function getAttachmentType() {
      return $this->getFieldValue('attachment_type');
    }
    
    /**
     * Set value of attachment_type field
     *
     * @param string $value
     * @return string
     */
    function setAttachmentType($value) {
      return $this->setFieldValue('attachment_type', $value);
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
        case 'parent_id':
          $set = intval($value);
          break;
        case 'parent_type':
          $set = strval($value);
          break;
        case 'name':
          $set = strval($value);
          break;
        case 'mime_type':
          $set = strval($value);
          break;
        case 'size':
          $set = intval($value);
          break;
        case 'location':
          $set = strval($value);
          break;
        case 'attachment_type':
          $set = strval($value);
          break;
        case 'created_on':
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
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>