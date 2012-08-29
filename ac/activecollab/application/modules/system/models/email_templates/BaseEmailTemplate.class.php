<?php

  /**
   * BaseEmailTemplate class
   */
  class BaseEmailTemplate extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('name', 'module', 'subject', 'body', 'variables');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    var $primary_key = array('name', 'module');
    
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
     * @return EmailTemplate 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'email_templates';
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
     * Return value of subject field
     *
     * @param void
     * @return string
     */
    function getSubject() {
      return $this->getFieldValue('subject');
    }
    
    /**
     * Set value of subject field
     *
     * @param string $value
     * @return string
     */
    function setSubject($value) {
      return $this->setFieldValue('subject', $value);
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
     * Return value of variables field
     *
     * @param void
     * @return string
     */
    function getVariables() {
      return $this->getFieldValue('variables');
    }
    
    /**
     * Set value of variables field
     *
     * @param string $value
     * @return string
     */
    function setVariables($value) {
      return $this->setFieldValue('variables', $value);
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
        case 'subject':
          $set = strval($value);
          break;
        case 'body':
          $set = strval($value);
          break;
        case 'variables':
          $set = strval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>