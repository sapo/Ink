<?php

  /**
   * BaseInvoiceItemTemplate class
   */
  class BaseInvoiceItemTemplate extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'position', 'tax_rate_id', 'description', 'quantity', 'unit_cost');
    
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
     * @return InvoiceItemTemplate 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'invoice_item_templates';
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
     * Return value of position field
     *
     * @param void
     * @return integer
     */
    function getPosition() {
      return $this->getFieldValue('position');
    }
    
    /**
     * Set value of position field
     *
     * @param integer $value
     * @return integer
     */
    function setPosition($value) {
      return $this->setFieldValue('position', $value);
    }

    /**
     * Return value of tax_rate_id field
     *
     * @param void
     * @return integer
     */
    function getTaxRateId() {
      return $this->getFieldValue('tax_rate_id');
    }
    
    /**
     * Set value of tax_rate_id field
     *
     * @param integer $value
     * @return integer
     */
    function setTaxRateId($value) {
      return $this->setFieldValue('tax_rate_id', $value);
    }

    /**
     * Return value of description field
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return $this->getFieldValue('description');
    }
    
    /**
     * Set value of description field
     *
     * @param string $value
     * @return string
     */
    function setDescription($value) {
      return $this->setFieldValue('description', $value);
    }

    /**
     * Return value of quantity field
     *
     * @param void
     * @return float
     */
    function getQuantity() {
      return $this->getFieldValue('quantity');
    }
    
    /**
     * Set value of quantity field
     *
     * @param float $value
     * @return float
     */
    function setQuantity($value) {
      return $this->setFieldValue('quantity', $value);
    }

    /**
     * Return value of unit_cost field
     *
     * @param void
     * @return float
     */
    function getUnitCost() {
      return $this->getFieldValue('unit_cost');
    }
    
    /**
     * Set value of unit_cost field
     *
     * @param float $value
     * @return float
     */
    function setUnitCost($value) {
      return $this->setFieldValue('unit_cost', $value);
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
        case 'position':
          $set = intval($value);
          break;
        case 'tax_rate_id':
          $set = intval($value);
          break;
        case 'description':
          $set = strval($value);
          break;
        case 'quantity':
          $set = floatval($value);
          break;
        case 'unit_cost':
          $set = floatval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>