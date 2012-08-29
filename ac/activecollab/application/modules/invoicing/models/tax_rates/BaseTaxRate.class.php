<?php

  /**
   * BaseTaxRate class
   */
  class BaseTaxRate extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'name', 'percentage');
    
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
     * @return TaxRate 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'tax_rates';
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
     * Return value of percentage field
     *
     * @param void
     * @return float
     */
    function getPercentage() {
      return $this->getFieldValue('percentage');
    }
    
    /**
     * Set value of percentage field
     *
     * @param float $value
     * @return float
     */
    function setPercentage($value) {
      return $this->setFieldValue('percentage', $value);
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
        case 'percentage':
          $set = floatval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>