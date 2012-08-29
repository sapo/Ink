<?php

  /**
   * BaseInvoice class
   */
  class BaseInvoice extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'company_id', 'project_id', 'currency_id', 'language_id', 'number', 'company_address', 'comment', 'note', 'status', 'issued_on', 'issued_by_id', 'issued_by_name', 'issued_by_email', 'issued_to_id', 'due_on', 'closed_on', 'closed_by_id', 'closed_by_name', 'closed_by_email', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email');
    
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
     * @return Invoice 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'invoices';
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
     * Return value of currency_id field
     *
     * @param void
     * @return integer
     */
    function getCurrencyId() {
      return $this->getFieldValue('currency_id');
    }
    
    /**
     * Set value of currency_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCurrencyId($value) {
      return $this->setFieldValue('currency_id', $value);
    }

    /**
     * Return value of language_id field
     *
     * @param void
     * @return integer
     */
    function getLanguageId() {
      return $this->getFieldValue('language_id');
    }
    
    /**
     * Set value of language_id field
     *
     * @param integer $value
     * @return integer
     */
    function setLanguageId($value) {
      return $this->setFieldValue('language_id', $value);
    }

    /**
     * Return value of number field
     *
     * @param void
     * @return string
     */
    function getNumber() {
      return $this->getFieldValue('number');
    }
    
    /**
     * Set value of number field
     *
     * @param string $value
     * @return string
     */
    function setNumber($value) {
      return $this->setFieldValue('number', $value);
    }

    /**
     * Return value of company_address field
     *
     * @param void
     * @return string
     */
    function getCompanyAddress() {
      return $this->getFieldValue('company_address');
    }
    
    /**
     * Set value of company_address field
     *
     * @param string $value
     * @return string
     */
    function setCompanyAddress($value) {
      return $this->setFieldValue('company_address', $value);
    }

    /**
     * Return value of comment field
     *
     * @param void
     * @return string
     */
    function getComment() {
      return $this->getFieldValue('comment');
    }
    
    /**
     * Set value of comment field
     *
     * @param string $value
     * @return string
     */
    function setComment($value) {
      return $this->setFieldValue('comment', $value);
    }

    /**
     * Return value of note field
     *
     * @param void
     * @return string
     */
    function getNote() {
      return $this->getFieldValue('note');
    }
    
    /**
     * Set value of note field
     *
     * @param string $value
     * @return string
     */
    function setNote($value) {
      return $this->setFieldValue('note', $value);
    }

    /**
     * Return value of status field
     *
     * @param void
     * @return integer
     */
    function getStatus() {
      return $this->getFieldValue('status');
    }
    
    /**
     * Set value of status field
     *
     * @param integer $value
     * @return integer
     */
    function setStatus($value) {
      return $this->setFieldValue('status', $value);
    }

    /**
     * Return value of issued_on field
     *
     * @param void
     * @return DateValue
     */
    function getIssuedOn() {
      return $this->getFieldValue('issued_on');
    }
    
    /**
     * Set value of issued_on field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setIssuedOn($value) {
      return $this->setFieldValue('issued_on', $value);
    }

    /**
     * Return value of issued_by_id field
     *
     * @param void
     * @return integer
     */
    function getIssuedById() {
      return $this->getFieldValue('issued_by_id');
    }
    
    /**
     * Set value of issued_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setIssuedById($value) {
      return $this->setFieldValue('issued_by_id', $value);
    }

    /**
     * Return value of issued_by_name field
     *
     * @param void
     * @return string
     */
    function getIssuedByName() {
      return $this->getFieldValue('issued_by_name');
    }
    
    /**
     * Set value of issued_by_name field
     *
     * @param string $value
     * @return string
     */
    function setIssuedByName($value) {
      return $this->setFieldValue('issued_by_name', $value);
    }

    /**
     * Return value of issued_by_email field
     *
     * @param void
     * @return string
     */
    function getIssuedByEmail() {
      return $this->getFieldValue('issued_by_email');
    }
    
    /**
     * Set value of issued_by_email field
     *
     * @param string $value
     * @return string
     */
    function setIssuedByEmail($value) {
      return $this->setFieldValue('issued_by_email', $value);
    }

    /**
     * Return value of issued_to_id field
     *
     * @param void
     * @return integer
     */
    function getIssuedToId() {
      return $this->getFieldValue('issued_to_id');
    }
    
    /**
     * Set value of issued_to_id field
     *
     * @param integer $value
     * @return integer
     */
    function setIssuedToId($value) {
      return $this->setFieldValue('issued_to_id', $value);
    }

    /**
     * Return value of due_on field
     *
     * @param void
     * @return DateValue
     */
    function getDueOn() {
      return $this->getFieldValue('due_on');
    }
    
    /**
     * Set value of due_on field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDueOn($value) {
      return $this->setFieldValue('due_on', $value);
    }

    /**
     * Return value of closed_on field
     *
     * @param void
     * @return DateTimeValue
     */
    function getClosedOn() {
      return $this->getFieldValue('closed_on');
    }
    
    /**
     * Set value of closed_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setClosedOn($value) {
      return $this->setFieldValue('closed_on', $value);
    }

    /**
     * Return value of closed_by_id field
     *
     * @param void
     * @return integer
     */
    function getClosedById() {
      return $this->getFieldValue('closed_by_id');
    }
    
    /**
     * Set value of closed_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setClosedById($value) {
      return $this->setFieldValue('closed_by_id', $value);
    }

    /**
     * Return value of closed_by_name field
     *
     * @param void
     * @return string
     */
    function getClosedByName() {
      return $this->getFieldValue('closed_by_name');
    }
    
    /**
     * Set value of closed_by_name field
     *
     * @param string $value
     * @return string
     */
    function setClosedByName($value) {
      return $this->setFieldValue('closed_by_name', $value);
    }

    /**
     * Return value of closed_by_email field
     *
     * @param void
     * @return string
     */
    function getClosedByEmail() {
      return $this->getFieldValue('closed_by_email');
    }
    
    /**
     * Set value of closed_by_email field
     *
     * @param string $value
     * @return string
     */
    function setClosedByEmail($value) {
      return $this->setFieldValue('closed_by_email', $value);
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
        case 'company_id':
          $set = intval($value);
          break;
        case 'project_id':
          $set = intval($value);
          break;
        case 'currency_id':
          $set = intval($value);
          break;
        case 'language_id':
          $set = intval($value);
          break;
        case 'number':
          $set = strval($value);
          break;
        case 'company_address':
          $set = strval($value);
          break;
        case 'comment':
          $set = strval($value);
          break;
        case 'note':
          $set = strval($value);
          break;
        case 'status':
          $set = intval($value);
          break;
        case 'issued_on':
          $set = dateval($value);
          break;
        case 'issued_by_id':
          $set = intval($value);
          break;
        case 'issued_by_name':
          $set = strval($value);
          break;
        case 'issued_by_email':
          $set = strval($value);
          break;
        case 'issued_to_id':
          $set = intval($value);
          break;
        case 'due_on':
          $set = dateval($value);
          break;
        case 'closed_on':
          $set = datetimeval($value);
          break;
        case 'closed_by_id':
          $set = intval($value);
          break;
        case 'closed_by_name':
          $set = strval($value);
          break;
        case 'closed_by_email':
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