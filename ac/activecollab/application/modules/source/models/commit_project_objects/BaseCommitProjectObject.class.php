<?php

  /**
   * BaseCommitProjectObject class
   */
  class BaseCommitProjectObject extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('object_id', 'object_type', 'project_id', 'revision', 'repository_id');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    var $primary_key = array('object_id');
    
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
     * @return CommitProjectObject 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'commit_project_objects';
      parent::__construct($id);
    }

    /**
     * Return value of object_id field
     *
     * @param void
     * @return integer
     */
    function getObjectId() {
      return $this->getFieldValue('object_id');
    }
    
    /**
     * Set value of object_id field
     *
     * @param integer $value
     * @return integer
     */
    function setObjectId($value) {
      return $this->setFieldValue('object_id', $value);
    }
    
    /**
     * Return value of project_id field
     *
     * @param void
     * @return integer
     */
    function getProjectId() {
      return $this->getFieldValue('project_id');
    }
    
    /**
     * Set value of project_id field
     *
     * @param integer $value
     * @return integer
     */
    function setProjectId($value) {
      return $this->setFieldValue('project_id', $value);
    }

    /**
     * Return value of object_type field
     *
     * @param void
     * @return integer
     */
    function getObjectType() {
      return $this->getFieldValue('object_type');
    }
    
    /**
     * Set value of object_type field
     *
     * @param string $value
     * @return string
     */
    function setObjectType($value) {
      return $this->setFieldValue('object_type', $value);
    }

    /**
     * Return value of revision field
     *
     * @param void
     * @return integer
     */
    function getRevision() {
      return $this->getFieldValue('revision');
    }
    
    /**
     * Set value of revision field
     *
     * @param integer $value
     * @return integer
     */
    function setRevision($value) {
      return $this->setFieldValue('revision', $value);
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
     * @param boolean $value
     * @return boolean
     */
    function setRepositoryId($value) {
      return $this->setFieldValue('repository_id', $value);
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
        case 'object_id':
          $set = intval($value);
          break;
        case 'object_type':
          $set = strval($value);
          break;
        case 'revision':
          $set = intval($value);
          break;
        case 'repository_id':
          $set = intval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>