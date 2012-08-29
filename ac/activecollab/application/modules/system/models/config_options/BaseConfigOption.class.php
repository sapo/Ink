<?php

  /**
   * BaseConfigOption class
   */
  class BaseConfigOption extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('name', 'module', 'type', 'value');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    var $primary_key = array('name');
    
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
     * @return ConfigOption 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'config_options';
      parent::__construct($id);
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
     * Return value of module field
     *
     * @param void
     * @return string
     */
    function getModule() {
      return $this->getFieldValue('module');
    }
    
    /**
     * Set value of module field
     *
     * @param string $value
     * @return string
     */
    function setModule($value) {
      return $this->setFieldValue('module', $value);
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
     * Return value of value field
     *
     * @param void
     * @return string
     */
    function getValue() {
      return $this->getFieldValue('value');
    }
    
    /**
     * Set value of value field
     *
     * @param string $value
     * @return string
     */
    function setValue($value) {
      return $this->setFieldValue('value', $value);
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
        case 'name':
          $set = strval($value);
          break;
        case 'module':
          $set = strval($value);
          break;
        case 'type':
          $set = strval($value);
          break;
        case 'value':
          $set = strval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>