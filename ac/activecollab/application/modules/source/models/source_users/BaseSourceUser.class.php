<?php

  /**
   * BaseSourceUser class
   */
  class BaseSourceUser extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('repository_id', 'repository_user', 'user_id');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    var $primary_key = array('repository_id', 'repository_user');
    
    /**
     * Name of AI field (if any)
     *
     * @var string
     */
    var $auto_increment = null; 
    
    /**
     * Construct the object and if $id is present load record from database
     *
     * @param mixed $id
     * @return SouceUser 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'source_users';
      parent::__construct($id);
    }

    /**
     * Return value of repository_id field
     *
     * @param void
     * @return integer
     */
    function getRepositoryId() {
      return $this->getFieldValue('repository_id');
    }
    
    /**
     * Set value of repository_id field
     *
     * @param integer $value
     * @return integer
     */
    function setRepositoryId($value) {
      return $this->setFieldValue('repository_id', $value);
    }
    
    /**
     * Return value of repository_user field
     *
     * @param void
     * @return string
     */
    function getRepositoryUser() {
      return $this->getFieldValue('repository_user');
    }
    
    /**
     * Set value of repository_user field
     *
     * @param string $value
     * @return integer
     */
    function setRepositoryUser($value) {
      return $this->setFieldValue('repository_user', $value);
    }

    /**
     * Return value of user_id field
     *
     * @param void
     * @return integer
     */
    function getUserId() {
      return $this->getFieldValue('user_id');
    }
    
    /**
     * Set value of user_id field
     *
     * @param string $value
     * @return string
     */
    function setUserId($value) {
      return $this->setFieldValue('user_id', $value);
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
        case 'repository_id':
          $set = intval($value);
          break;
        case 'repository_user':
          $set = strval($value);
          break;
        case 'user_id':
          $set = intval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>