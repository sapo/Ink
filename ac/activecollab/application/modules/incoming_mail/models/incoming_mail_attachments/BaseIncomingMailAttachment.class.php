<?php

  /**
   * BaseIncomingMailAttachment class
   */
  class BaseIncomingMailAttachment extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'mail_id', 'temporary_filename', 'original_filename', 'content_type', 'file_size');
    
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
     * @return IncomingMailAttachment 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'incoming_mail_attachments';
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
     * Return value of mail_id field
     *
     * @param void
     * @return integer
     */
    function getMailId() {
      return $this->getFieldValue('mail_id');
    }
    
    /**
     * Set value of mail_id field
     *
     * @param integer $value
     * @return integer
     */
    function setMailId($value) {
      return $this->setFieldValue('mail_id', $value);
    }

    /**
     * Return value of temporary_filename field
     *
     * @param void
     * @return string
     */
    function getTemporaryFilename() {
      return $this->getFieldValue('temporary_filename');
    }
    
    /**
     * Set value of temporary_filename field
     *
     * @param string $value
     * @return string
     */
    function setTemporaryFilename($value) {
      return $this->setFieldValue('temporary_filename', $value);
    }

    /**
     * Return value of original_filename field
     *
     * @param void
     * @return string
     */
    function getOriginalFilename() {
      return $this->getFieldValue('original_filename');
    }
    
    /**
     * Set value of original_filename field
     *
     * @param string $value
     * @return string
     */
    function setOriginalFilename($value) {
      return $this->setFieldValue('original_filename', $value);
    }

    /**
     * Return value of content_type field
     *
     * @param void
     * @return string
     */
    function getContentType() {
      return $this->getFieldValue('content_type');
    }
    
    /**
     * Set value of content_type field
     *
     * @param string $value
     * @return string
     */
    function setContentType($value) {
      return $this->setFieldValue('content_type', $value);
    }

    /**
     * Return value of file_size field
     *
     * @param void
     * @return integer
     */
    function getFileSize() {
      return $this->getFieldValue('file_size');
    }
    
    /**
     * Set value of file_size field
     *
     * @param integer $value
     * @return integer
     */
    function setFileSize($value) {
      return $this->setFieldValue('file_size', $value);
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
        case 'mail_id':
          $set = intval($value);
          break;
        case 'temporary_filename':
          $set = strval($value);
          break;
        case 'original_filename':
          $set = strval($value);
          break;
        case 'content_type':
          $set = strval($value);
          break;
        case 'file_size':
          $set = intval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>