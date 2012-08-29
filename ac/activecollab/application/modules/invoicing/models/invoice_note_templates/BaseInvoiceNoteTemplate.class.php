<?php

  /**
   * BaseInvoiceNoteTemplate class
   */
  class BaseInvoiceNoteTemplate extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'position', 'name', 'content');
    
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
     * @return InvoiceNoteTemplate 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'invoice_note_templates';
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
     * Return value of content field
     *
     * @param void
     * @return string
     */
    function getContent() {
      return $this->getFieldValue('content');
    }
    
    /**
     * Set value of content field
     *
     * @param string $value
     * @return string
     */
    function setContent($value) {
      return $this->setFieldValue('content', $value);
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
        case 'name':
          $set = strval($value);
          break;
        case 'content':
          $set = strval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>