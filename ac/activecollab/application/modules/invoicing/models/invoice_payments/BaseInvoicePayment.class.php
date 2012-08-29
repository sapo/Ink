<?php

  /**
   * BaseInvoicePayment class
   */
  class BaseInvoicePayment extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'invoice_id', 'amount', 'paid_on', 'comment', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email');
    
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
     * @return InvoicePayment 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'invoice_payments';
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
     * Return value of invoice_id field
     *
     * @param void
     * @return integer
     */
    function getInvoiceId() {
      return $this->getFieldValue('invoice_id');
    }
    
    /**
     * Set value of invoice_id field
     *
     * @param integer $value
     * @return integer
     */
    function setInvoiceId($value) {
      return $this->setFieldValue('invoice_id', $value);
    }

    /**
     * Return value of amount field
     *
     * @param void
     * @return float
     */
    function getAmount() {
      return $this->getFieldValue('amount');
    }
    
    /**
     * Set value of amount field
     *
     * @param float $value
     * @return float
     */
    function setAmount($value) {
      return $this->setFieldValue('amount', $value);
    }

    /**
     * Return value of paid_on field
     *
     * @param void
     * @return DateValue
     */
    function getPaidOn() {
      return $this->getFieldValue('paid_on');
    }
    
    /**
     * Set value of paid_on field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setPaidOn($value) {
      return $this->setFieldValue('paid_on', $value);
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
        case 'invoice_id':
          $set = intval($value);
          break;
        case 'amount':
          $set = floatval($value);
          break;
        case 'paid_on':
          $set = dateval($value);
          break;
        case 'comment':
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