<?php

  /**
   * BaseIncomingMail class
   */
  class BaseIncomingMail extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'parent_id', 'project_id', 'incoming_mailbox_id', 'subject', 'body', 'headers', 'object_type', 'state', 'created_by_id', 'created_by_name', 'created_by_email', 'created_on');
    
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
     * @return IncomingMail 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'incoming_mails';
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
     * Return value of incoming_mailbox_id field
     *
     * @param void
     * @return integer
     */
    function getIncomingMailboxId() {
      return $this->getFieldValue('incoming_mailbox_id');
    }
    
    /**
     * Set value of incoming_mailbox_id field
     *
     * @param integer $value
     * @return integer
     */
    function setIncomingMailboxId($value) {
      return $this->setFieldValue('incoming_mailbox_id', $value);
    }

    /**
     * Return value of subject field
     *
     * @param void
     * @return string
     */
    function getSubject() {
      return $this->getFieldValue('subject');
    }
    
    /**
     * Set value of subject field
     *
     * @param string $value
     * @return string
     */
    function setSubject($value) {
      return $this->setFieldValue('subject', $value);
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
     * Return value of headers field
     *
     * @param void
     * @return string
     */
    function getHeaders() {
      return $this->getFieldValue('headers');
    }
    
    /**
     * Set value of headers field
     *
     * @param string $value
     * @return string
     */
    function setHeaders($value) {
      return $this->setFieldValue('headers', $value);
    }

    /**
     * Return value of object_type field
     *
     * @param void
     * @return string
     */
    function getObjectType() {
      return $this->getFieldValue('object_type');
    }
    
    /**
     * Set value of object_type field
     *
     * @param string $value
     * @return string
     */
    function setObjectType($value) {
      return $this->setFieldValue('object_type', $value);
    }

    /**
     * Return value of state field
     *
     * @param void
     * @return integer
     */
    function getState() {
      return $this->getFieldValue('state');
    }
    
    /**
     * Set value of state field
     *
     * @param integer $value
     * @return integer
     */
    function setState($value) {
      return $this->setFieldValue('state', $value);
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
        case 'parent_id':
          $set = intval($value);
          break;
        case 'project_id':
          $set = intval($value);
          break;
        case 'incoming_mailbox_id':
          $set = intval($value);
          break;
        case 'subject':
          $set = strval($value);
          break;
        case 'body':
          $set = strval($value);
          break;
        case 'headers':
          $set = strval($value);
          break;
        case 'object_type':
          $set = strval($value);
          break;
        case 'state':
          $set = intval($value);
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