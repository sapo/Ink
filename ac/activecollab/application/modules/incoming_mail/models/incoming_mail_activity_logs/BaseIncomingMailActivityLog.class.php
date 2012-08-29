<?php

  /**
   * BaseIncomingMailActivityLog class
   */
  class BaseIncomingMailActivityLog extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'mailbox_id', 'state', 'response', 'sender', 'subject', 'incoming_mail_id', 'project_object_id', 'created_on');
    
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
     * @return IncomingMailActivityLog 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'incoming_mail_activity_logs';
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
     * Return value of mailbox_id field
     *
     * @param void
     * @return integer
     */
    function getMailboxId() {
      return $this->getFieldValue('mailbox_id');
    }
    
    /**
     * Set value of mailbox_id field
     *
     * @param integer $value
     * @return integer
     */
    function setMailboxId($value) {
      return $this->setFieldValue('mailbox_id', $value);
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
     * Return value of response field
     *
     * @param void
     * @return string
     */
    function getResponse() {
      return $this->getFieldValue('response');
    }
    
    /**
     * Set value of response field
     *
     * @param string $value
     * @return string
     */
    function setResponse($value) {
      return $this->setFieldValue('response', $value);
    }

    /**
     * Return value of sender field
     *
     * @param void
     * @return string
     */
    function getSender() {
      return $this->getFieldValue('sender');
    }
    
    /**
     * Set value of sender field
     *
     * @param string $value
     * @return string
     */
    function setSender($value) {
      return $this->setFieldValue('sender', $value);
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
     * Return value of incoming_mail_id field
     *
     * @param void
     * @return integer
     */
    function getIncomingMailId() {
      return $this->getFieldValue('incoming_mail_id');
    }
    
    /**
     * Set value of incoming_mail_id field
     *
     * @param integer $value
     * @return integer
     */
    function setIncomingMailId($value) {
      return $this->setFieldValue('incoming_mail_id', $value);
    }

    /**
     * Return value of project_object_id field
     *
     * @param void
     * @return integer
     */
    function getProjectObjectId() {
      return $this->getFieldValue('project_object_id');
    }
    
    /**
     * Set value of project_object_id field
     *
     * @param integer $value
     * @return integer
     */
    function setProjectObjectId($value) {
      return $this->setFieldValue('project_object_id', $value);
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
        case 'mailbox_id':
          $set = intval($value);
          break;
        case 'state':
          $set = intval($value);
          break;
        case 'response':
          $set = strval($value);
          break;
        case 'sender':
          $set = strval($value);
          break;
        case 'subject':
          $set = strval($value);
          break;
        case 'incoming_mail_id':
          $set = intval($value);
          break;
        case 'project_object_id':
          $set = intval($value);
          break;
        case 'created_on':
          $set = datetimeval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>