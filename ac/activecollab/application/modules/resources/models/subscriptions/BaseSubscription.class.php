<?php

  /**
   * BaseSubscription class
   */
  class BaseSubscription extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('user_id', 'parent_id');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    var $primary_key = array('user_id', 'parent_id');
    
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
     * @return Subscription 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'subscriptions';
      parent::__construct($id);
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
     * @param integer $value
     * @return integer
     */
    function setUserId($value) {
      return $this->setFieldValue('user_id', $value);
    }

    /**
     * Return value of parent_id field
     *
     * @param void
     * @return integer
     */
    function getParentId() {
      return $this->getFieldValue('parent_id');
    }
    
    /**
     * Set value of parent_id field
     *
     * @param integer $value
     * @return integer
     */
    function setParentId($value) {
      return $this->setFieldValue('parent_id', $value);
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
        case 'user_id':
          $set = intval($value);
          break;
        case 'parent_id':
          $set = intval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>