<?php

  /**
   * BaseCurrency class
   */
  class BaseCurrency extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'name', 'code', 'default_rate', 'is_default');
    
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
     * @return Currency 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'currencies';
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
     * Return value of code field
     *
     * @param void
     * @return string
     */
    function getCode() {
      return $this->getFieldValue('code');
    }
    
    /**
     * Set value of code field
     *
     * @param string $value
     * @return string
     */
    function setCode($value) {
      return $this->setFieldValue('code', $value);
    }

    /**
     * Return value of default_rate field
     *
     * @param void
     * @return float
     */
    function getDefaultRate() {
      return $this->getFieldValue('default_rate');
    }
    
    /**
     * Set value of default_rate field
     *
     * @param float $value
     * @return float
     */
    function setDefaultRate($value) {
      return $this->setFieldValue('default_rate', $value);
    }

    /**
     * Return value of is_default field
     *
     * @param void
     * @return boolean
     */
    function getIsDefault() {
      return $this->getFieldValue('is_default');
    }
    
    /**
     * Set value of is_default field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsDefault($value) {
      return $this->setFieldValue('is_default', $value);
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
        case 'code':
          $set = strval($value);
          break;
        case 'default_rate':
          $set = floatval($value);
          break;
        case 'is_default':
          $set = boolval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>