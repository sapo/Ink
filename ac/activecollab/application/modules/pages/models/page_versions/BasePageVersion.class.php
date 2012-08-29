<?php

  /**
   * BasePageVersion class
   */
  class BasePageVersion extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('page_id', 'version', 'name', 'body', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    var $primary_key = array('page_id', 'version');
    
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
     * @return PageVersion 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'page_versions';
      parent::__construct($id);
    }

    /**
     * Return value of page_id field
     *
     * @param void
     * @return integer
     */
    function getPageId() {
      return $this->getFieldValue('page_id');
    }
    
    /**
     * Set value of page_id field
     *
     * @param integer $value
     * @return integer
     */
    function setPageId($value) {
      return $this->setFieldValue('page_id', $value);
    }

    /**
     * Return value of version field
     *
     * @param void
     * @return integer
     */
    function getVersion() {
      return $this->getFieldValue('version');
    }
    
    /**
     * Set value of version field
     *
     * @param integer $value
     * @return integer
     */
    function setVersion($value) {
      return $this->setFieldValue('version', $value);
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
        case 'page_id':
          $set = intval($value);
          break;
        case 'version':
          $set = intval($value);
          break;
        case 'name':
          $set = strval($value);
          break;
        case 'body':
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