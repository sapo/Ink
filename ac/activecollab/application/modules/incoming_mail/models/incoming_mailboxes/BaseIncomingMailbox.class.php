<?php

  /**
   * BaseIncomingMailbox class
   */
  class BaseIncomingMailbox extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'project_id', 'object_type', 'mailbox', 'username', 'password', 'host', 'from_name', 'from_email', 'type', 'port', 'security', 'last_status', 'enabled', 'accept_all_registered', 'accept_anonymous');
    
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
     * @return IncomingMailbox 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'incoming_mailboxes';
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
     * Return value of mailbox field
     *
     * @param void
     * @return string
     */
    function getMailbox() {
      return $this->getFieldValue('mailbox');
    }
    
    /**
     * Set value of mailbox field
     *
     * @param string $value
     * @return string
     */
    function setMailbox($value) {
      return $this->setFieldValue('mailbox', $value);
    }

    /**
     * Return value of username field
     *
     * @param void
     * @return string
     */
    function getUsername() {
      return $this->getFieldValue('username');
    }
    
    /**
     * Set value of username field
     *
     * @param string $value
     * @return string
     */
    function setUsername($value) {
      return $this->setFieldValue('username', $value);
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
     * Return value of host field
     *
     * @param void
     * @return string
     */
    function getHost() {
      return $this->getFieldValue('host');
    }
    
    /**
     * Set value of host field
     *
     * @param string $value
     * @return string
     */
    function setHost($value) {
      return $this->setFieldValue('host', $value);
    }

    /**
     * Return value of from_name field
     *
     * @param void
     * @return string
     */
    function getFromName() {
      return $this->getFieldValue('from_name');
    }
    
    /**
     * Set value of from_name field
     *
     * @param string $value
     * @return string
     */
    function setFromName($value) {
      return $this->setFieldValue('from_name', $value);
    }

    /**
     * Return value of from_email field
     *
     * @param void
     * @return string
     */
    function getFromEmail() {
      return $this->getFieldValue('from_email');
    }
    
    /**
     * Set value of from_email field
     *
     * @param string $value
     * @return string
     */
    function setFromEmail($value) {
      return $this->setFieldValue('from_email', $value);
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
     * Return value of port field
     *
     * @param void
     * @return integer
     */
    function getPort() {
      return $this->getFieldValue('port');
    }
    
    /**
     * Set value of port field
     *
     * @param integer $value
     * @return integer
     */
    function setPort($value) {
      return $this->setFieldValue('port', $value);
    }

    /**
     * Return value of security field
     *
     * @param void
     * @return string
     */
    function getSecurity() {
      return $this->getFieldValue('security');
    }
    
    /**
     * Set value of security field
     *
     * @param string $value
     * @return string
     */
    function setSecurity($value) {
      return $this->setFieldValue('security', $value);
    }

    /**
     * Return value of last_status field
     *
     * @param void
     * @return integer
     */
    function getLastStatus() {
      return $this->getFieldValue('last_status');
    }
    
    /**
     * Set value of last_status field
     *
     * @param integer $value
     * @return integer
     */
    function setLastStatus($value) {
      return $this->setFieldValue('last_status', $value);
    }

    /**
     * Return value of enabled field
     *
     * @param void
     * @return integer
     */
    function getEnabled() {
      return $this->getFieldValue('enabled');
    }
    
    /**
     * Set value of enabled field
     *
     * @param integer $value
     * @return integer
     */
    function setEnabled($value) {
      return $this->setFieldValue('enabled', $value);
    }

    /**
     * Return value of accept_all_registered field
     *
     * @param void
     * @return integer
     */
    function getAcceptAllRegistered() {
      return $this->getFieldValue('accept_all_registered');
    }
    
    /**
     * Set value of accept_all_registered field
     *
     * @param integer $value
     * @return integer
     */
    function setAcceptAllRegistered($value) {
      return $this->setFieldValue('accept_all_registered', $value);
    }

    /**
     * Return value of accept_anonymous field
     *
     * @param void
     * @return integer
     */
    function getAcceptAnonymous() {
      return $this->getFieldValue('accept_anonymous');
    }
    
    /**
     * Set value of accept_anonymous field
     *
     * @param integer $value
     * @return integer
     */
    function setAcceptAnonymous($value) {
      return $this->setFieldValue('accept_anonymous', $value);
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
        case 'project_id':
          $set = intval($value);
          break;
        case 'object_type':
          $set = strval($value);
          break;
        case 'mailbox':
          $set = strval($value);
          break;
        case 'username':
          $set = strval($value);
          break;
        case 'password':
          $set = strval($value);
          break;
        case 'host':
          $set = strval($value);
          break;
        case 'from_name':
          $set = strval($value);
          break;
        case 'from_email':
          $set = strval($value);
          break;
        case 'type':
          $set = strval($value);
          break;
        case 'port':
          $set = intval($value);
          break;
        case 'security':
          $set = strval($value);
          break;
        case 'last_status':
          $set = intval($value);
          break;
        case 'enabled':
          $set = intval($value);
          break;
        case 'accept_all_registered':
          $set = intval($value);
          break;
        case 'accept_anonymous':
          $set = intval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>