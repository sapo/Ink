<?php

  /**
   * BaseRole class
   */
  class BaseRole extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'type', 'permissions', 'name');
    
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
     * @return Role 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'roles';
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
     * Return value of permissions field
     *
     * @param void
     * @return string
     */
    function getPermissions() {
      return $this->getFieldValue('permissions');
    }
    
    /**
     * Set value of permissions field
     *
     * @param string $value
     * @return string
     */
    function setPermissions($value) {
      return $this->setFieldValue('permissions', $value);
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
        case 'type':
          $set = strval($value);
          break;
        case 'permissions':
          $set = strval($value);
          break;
        case 'name':
          $set = strval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>