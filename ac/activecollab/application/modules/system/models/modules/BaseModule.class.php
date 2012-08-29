<?php

  /**
   * BaseModule class
   */
  class BaseModule extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('name', 'is_system', 'position');
    
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
     * @return Module 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'modules';
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
     * Return value of is_system field
     *
     * @param void
     * @return boolean
     */
    function getIsSystem() {
      return $this->getFieldValue('is_system');
    }
    
    /**
     * Set value of is_system field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsSystem($value) {
      return $this->setFieldValue('is_system', $value);
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
        case 'is_system':
          $set = boolval($value);
          break;
        case 'position':
          $set = intval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>