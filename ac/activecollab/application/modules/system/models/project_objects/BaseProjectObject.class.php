<?php

  /**
   * BaseProjectObject class
   */
  class BaseProjectObject extends ApplicationObject {
    
    /**
     * All table fields
     *
     * @var array
     */
    var $fields = array('id', 'type', 'source', 'module', 'project_id', 'milestone_id', 'parent_id', 'parent_type', 'name', 'body', 'tags', 'state', 'visibility', 'priority', 'resolution', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email', 'due_on', 'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email', 'has_time', 'comments_count', 'is_locked', 'varchar_field_1', 'varchar_field_2', 'integer_field_1', 'integer_field_2', 'float_field_1', 'float_field_2', 'text_field_1', 'text_field_2', 'date_field_1', 'date_field_2', 'datetime_field_1', 'datetime_field_2', 'boolean_field_1', 'boolean_field_2', 'position', 'version');
    
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
     * @return ProjectObject 
     */
    function __construct($id = null) {
      $this->table_name = TABLE_PREFIX . 'project_objects';
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
     * Return value of source field
     *
     * @param void
     * @return string
     */
    function getSource() {
      return $this->getFieldValue('source');
    }
    
    /**
     * Set value of source field
     *
     * @param string $value
     * @return string
     */
    function setSource($value) {
      return $this->setFieldValue('source', $value);
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
     * Return value of milestone_id field
     *
     * @param void
     * @return integer
     */
    function getMilestoneId() {
      return $this->getFieldValue('milestone_id');
    }
    
    /**
     * Set value of milestone_id field
     *
     * @param integer $value
     * @return integer
     */
    function setMilestoneId($value) {
      return $this->setFieldValue('milestone_id', $value);
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
     * Return value of parent_type field
     *
     * @param void
     * @return string
     */
    function getParentType() {
      return $this->getFieldValue('parent_type');
    }
    
    /**
     * Set value of parent_type field
     *
     * @param string $value
     * @return string
     */
    function setParentType($value) {
      return $this->setFieldValue('parent_type', $value);
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
     * Return value of tags field
     *
     * @param void
     * @return string
     */
    function getTags() {
      return $this->getFieldValue('tags');
    }
    
    /**
     * Set value of tags field
     *
     * @param string $value
     * @return string
     */
    function setTags($value) {
      return $this->setFieldValue('tags', $value);
    }

    /**
     * Return value of state field
     *
     * @param void
     * @return integer
     */
    function getState() {
      return $this->getFieldValue('state');
    }
    
    /**
     * Set value of state field
     *
     * @param integer $value
     * @return integer
     */
    function setState($value) {
      return $this->setFieldValue('state', $value);
    }

    /**
     * Return value of visibility field
     *
     * @param void
     * @return integer
     */
    function getVisibility() {
      return $this->getFieldValue('visibility');
    }
    
    /**
     * Set value of visibility field
     *
     * @param integer $value
     * @return integer
     */
    function setVisibility($value) {
      return $this->setFieldValue('visibility', $value);
    }

    /**
     * Return value of priority field
     *
     * @param void
     * @return integer
     */
    function getPriority() {
      return $this->getFieldValue('priority');
    }
    
    /**
     * Set value of priority field
     *
     * @param integer $value
     * @return integer
     */
    function setPriority($value) {
      return $this->setFieldValue('priority', $value);
    }

    /**
     * Return value of resolution field
     *
     * @param void
     * @return string
     */
    function getResolution() {
      return $this->getFieldValue('resolution');
    }
    
    /**
     * Set value of resolution field
     *
     * @param string $value
     * @return string
     */
    function setResolution($value) {
      return $this->setFieldValue('resolution', $value);
    }

    /**
     * Return value of created_on field
     *
     * @param void
     * @return DateTimeValue
     */
    function getCreatedOn() {
      return $this->getFieldValue('created_on');
    }
    
    /**
     * Set value of created_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setCreatedOn($value) {
      return $this->setFieldValue('created_on', $value);
    }

    /**
     * Return value of created_by_id field
     *
     * @param void
     * @return integer
     */
    function getCreatedById() {
      return $this->getFieldValue('created_by_id');
    }
    
    /**
     * Set value of created_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCreatedById($value) {
      return $this->setFieldValue('created_by_id', $value);
    }

    /**
     * Return value of created_by_name field
     *
     * @param void
     * @return string
     */
    function getCreatedByName() {
      return $this->getFieldValue('created_by_name');
    }
    
    /**
     * Set value of created_by_name field
     *
     * @param string $value
     * @return string
     */
    function setCreatedByName($value) {
      return $this->setFieldValue('created_by_name', $value);
    }

    /**
     * Return value of created_by_email field
     *
     * @param void
     * @return string
     */
    function getCreatedByEmail() {
      return $this->getFieldValue('created_by_email');
    }
    
    /**
     * Set value of created_by_email field
     *
     * @param string $value
     * @return string
     */
    function setCreatedByEmail($value) {
      return $this->setFieldValue('created_by_email', $value);
    }

    /**
     * Return value of updated_on field
     *
     * @param void
     * @return DateTimeValue
     */
    function getUpdatedOn() {
      return $this->getFieldValue('updated_on');
    }
    
    /**
     * Set value of updated_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setUpdatedOn($value) {
      return $this->setFieldValue('updated_on', $value);
    }

    /**
     * Return value of updated_by_id field
     *
     * @param void
     * @return integer
     */
    function getUpdatedById() {
      return $this->getFieldValue('updated_by_id');
    }
    
    /**
     * Set value of updated_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setUpdatedById($value) {
      return $this->setFieldValue('updated_by_id', $value);
    }

    /**
     * Return value of updated_by_name field
     *
     * @param void
     * @return string
     */
    function getUpdatedByName() {
      return $this->getFieldValue('updated_by_name');
    }
    
    /**
     * Set value of updated_by_name field
     *
     * @param string $value
     * @return string
     */
    function setUpdatedByName($value) {
      return $this->setFieldValue('updated_by_name', $value);
    }

    /**
     * Return value of updated_by_email field
     *
     * @param void
     * @return string
     */
    function getUpdatedByEmail() {
      return $this->getFieldValue('updated_by_email');
    }
    
    /**
     * Set value of updated_by_email field
     *
     * @param string $value
     * @return string
     */
    function setUpdatedByEmail($value) {
      return $this->setFieldValue('updated_by_email', $value);
    }

    /**
     * Return value of due_on field
     *
     * @param void
     * @return DateValue
     */
    function getDueOn() {
      return $this->getFieldValue('due_on');
    }
    
    /**
     * Set value of due_on field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDueOn($value) {
      return $this->setFieldValue('due_on', $value);
    }

    /**
     * Return value of completed_on field
     *
     * @param void
     * @return DateTimeValue
     */
    function getCompletedOn() {
      return $this->getFieldValue('completed_on');
    }
    
    /**
     * Set value of completed_on field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setCompletedOn($value) {
      return $this->setFieldValue('completed_on', $value);
    }

    /**
     * Return value of completed_by_id field
     *
     * @param void
     * @return integer
     */
    function getCompletedById() {
      return $this->getFieldValue('completed_by_id');
    }
    
    /**
     * Set value of completed_by_id field
     *
     * @param integer $value
     * @return integer
     */
    function setCompletedById($value) {
      return $this->setFieldValue('completed_by_id', $value);
    }

    /**
     * Return value of completed_by_name field
     *
     * @param void
     * @return string
     */
    function getCompletedByName() {
      return $this->getFieldValue('completed_by_name');
    }
    
    /**
     * Set value of completed_by_name field
     *
     * @param string $value
     * @return string
     */
    function setCompletedByName($value) {
      return $this->setFieldValue('completed_by_name', $value);
    }

    /**
     * Return value of completed_by_email field
     *
     * @param void
     * @return string
     */
    function getCompletedByEmail() {
      return $this->getFieldValue('completed_by_email');
    }
    
    /**
     * Set value of completed_by_email field
     *
     * @param string $value
     * @return string
     */
    function setCompletedByEmail($value) {
      return $this->setFieldValue('completed_by_email', $value);
    }

    /**
     * Return value of has_time field
     *
     * @param void
     * @return boolean
     */
    function getHasTime() {
      return $this->getFieldValue('has_time');
    }
    
    /**
     * Set value of has_time field
     *
     * @param boolean $value
     * @return boolean
     */
    function setHasTime($value) {
      return $this->setFieldValue('has_time', $value);
    }

    /**
     * Return value of comments_count field
     *
     * @param void
     * @return integer
     */
    function getCommentsCount() {
      return $this->getFieldValue('comments_count');
    }
    
    /**
     * Set value of comments_count field
     *
     * @param integer $value
     * @return integer
     */
    function setCommentsCount($value) {
      return $this->setFieldValue('comments_count', $value);
    }

    /**
     * Return value of is_locked field
     *
     * @param void
     * @return boolean
     */
    function getIsLocked() {
      return $this->getFieldValue('is_locked');
    }
    
    /**
     * Set value of is_locked field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsLocked($value) {
      return $this->setFieldValue('is_locked', $value);
    }

    /**
     * Return value of varchar_field_1 field
     *
     * @param void
     * @return string
     */
    function getVarcharField1() {
      return $this->getFieldValue('varchar_field_1');
    }
    
    /**
     * Set value of varchar_field_1 field
     *
     * @param string $value
     * @return string
     */
    function setVarcharField1($value) {
      return $this->setFieldValue('varchar_field_1', $value);
    }

    /**
     * Return value of varchar_field_2 field
     *
     * @param void
     * @return string
     */
    function getVarcharField2() {
      return $this->getFieldValue('varchar_field_2');
    }
    
    /**
     * Set value of varchar_field_2 field
     *
     * @param string $value
     * @return string
     */
    function setVarcharField2($value) {
      return $this->setFieldValue('varchar_field_2', $value);
    }

    /**
     * Return value of integer_field_1 field
     *
     * @param void
     * @return integer
     */
    function getIntegerField1() {
      return $this->getFieldValue('integer_field_1');
    }
    
    /**
     * Set value of integer_field_1 field
     *
     * @param integer $value
     * @return integer
     */
    function setIntegerField1($value) {
      return $this->setFieldValue('integer_field_1', $value);
    }

    /**
     * Return value of integer_field_2 field
     *
     * @param void
     * @return integer
     */
    function getIntegerField2() {
      return $this->getFieldValue('integer_field_2');
    }
    
    /**
     * Set value of integer_field_2 field
     *
     * @param integer $value
     * @return integer
     */
    function setIntegerField2($value) {
      return $this->setFieldValue('integer_field_2', $value);
    }

    /**
     * Return value of float_field_1 field
     *
     * @param void
     * @return float
     */
    function getFloatField1() {
      return $this->getFieldValue('float_field_1');
    }
    
    /**
     * Set value of float_field_1 field
     *
     * @param float $value
     * @return float
     */
    function setFloatField1($value) {
      return $this->setFieldValue('float_field_1', $value);
    }

    /**
     * Return value of float_field_2 field
     *
     * @param void
     * @return float
     */
    function getFloatField2() {
      return $this->getFieldValue('float_field_2');
    }
    
    /**
     * Set value of float_field_2 field
     *
     * @param float $value
     * @return float
     */
    function setFloatField2($value) {
      return $this->setFieldValue('float_field_2', $value);
    }

    /**
     * Return value of text_field_1 field
     *
     * @param void
     * @return string
     */
    function getTextField1() {
      return $this->getFieldValue('text_field_1');
    }
    
    /**
     * Set value of text_field_1 field
     *
     * @param string $value
     * @return string
     */
    function setTextField1($value) {
      return $this->setFieldValue('text_field_1', $value);
    }

    /**
     * Return value of text_field_2 field
     *
     * @param void
     * @return string
     */
    function getTextField2() {
      return $this->getFieldValue('text_field_2');
    }
    
    /**
     * Set value of text_field_2 field
     *
     * @param string $value
     * @return string
     */
    function setTextField2($value) {
      return $this->setFieldValue('text_field_2', $value);
    }

    /**
     * Return value of date_field_1 field
     *
     * @param void
     * @return DateValue
     */
    function getDateField1() {
      return $this->getFieldValue('date_field_1');
    }
    
    /**
     * Set value of date_field_1 field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDateField1($value) {
      return $this->setFieldValue('date_field_1', $value);
    }

    /**
     * Return value of date_field_2 field
     *
     * @param void
     * @return DateValue
     */
    function getDateField2() {
      return $this->getFieldValue('date_field_2');
    }
    
    /**
     * Set value of date_field_2 field
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setDateField2($value) {
      return $this->setFieldValue('date_field_2', $value);
    }

    /**
     * Return value of datetime_field_1 field
     *
     * @param void
     * @return DateTimeValue
     */
    function getDatetimeField1() {
      return $this->getFieldValue('datetime_field_1');
    }
    
    /**
     * Set value of datetime_field_1 field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setDatetimeField1($value) {
      return $this->setFieldValue('datetime_field_1', $value);
    }

    /**
     * Return value of datetime_field_2 field
     *
     * @param void
     * @return DateTimeValue
     */
    function getDatetimeField2() {
      return $this->getFieldValue('datetime_field_2');
    }
    
    /**
     * Set value of datetime_field_2 field
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setDatetimeField2($value) {
      return $this->setFieldValue('datetime_field_2', $value);
    }

    /**
     * Return value of boolean_field_1 field
     *
     * @param void
     * @return boolean
     */
    function getBooleanField1() {
      return $this->getFieldValue('boolean_field_1');
    }
    
    /**
     * Set value of boolean_field_1 field
     *
     * @param boolean $value
     * @return boolean
     */
    function setBooleanField1($value) {
      return $this->setFieldValue('boolean_field_1', $value);
    }

    /**
     * Return value of boolean_field_2 field
     *
     * @param void
     * @return boolean
     */
    function getBooleanField2() {
      return $this->getFieldValue('boolean_field_2');
    }
    
    /**
     * Set value of boolean_field_2 field
     *
     * @param boolean $value
     * @return boolean
     */
    function setBooleanField2($value) {
      return $this->setFieldValue('boolean_field_2', $value);
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
     * Return value of version field
     *
     * @param void
     * @return integer
     */
    function getVersion() {
      return $this->getFieldValue('version');
    }
    
    /**
     * Set value of version field
     *
     * @param integer $value
     * @return integer
     */
    function setVersion($value) {
      return $this->setFieldValue('version', $value);
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
        case 'source':
          $set = strval($value);
          break;
        case 'module':
          $set = strval($value);
          break;
        case 'project_id':
          $set = intval($value);
          break;
        case 'milestone_id':
          $set = intval($value);
          break;
        case 'parent_id':
          $set = intval($value);
          break;
        case 'parent_type':
          $set = strval($value);
          break;
        case 'name':
          $set = strval($value);
          break;
        case 'body':
          $set = strval($value);
          break;
        case 'tags':
          $set = strval($value);
          break;
        case 'state':
          $set = intval($value);
          break;
        case 'visibility':
          $set = intval($value);
          break;
        case 'priority':
          $set = intval($value);
          break;
        case 'resolution':
          $set = strval($value);
          break;
        case 'created_on':
          $set = datetimeval($value);
          break;
        case 'created_by_id':
          $set = intval($value);
          break;
        case 'created_by_name':
          $set = strval($value);
          break;
        case 'created_by_email':
          $set = strval($value);
          break;
        case 'updated_on':
          $set = datetimeval($value);
          break;
        case 'updated_by_id':
          $set = intval($value);
          break;
        case 'updated_by_name':
          $set = strval($value);
          break;
        case 'updated_by_email':
          $set = strval($value);
          break;
        case 'due_on':
          $set = dateval($value);
          break;
        case 'completed_on':
          $set = datetimeval($value);
          break;
        case 'completed_by_id':
          $set = intval($value);
          break;
        case 'completed_by_name':
          $set = strval($value);
          break;
        case 'completed_by_email':
          $set = strval($value);
          break;
        case 'has_time':
          $set = boolval($value);
          break;
        case 'comments_count':
          $set = intval($value);
          break;
        case 'is_locked':
          $set = boolval($value);
          break;
        case 'varchar_field_1':
          $set = strval($value);
          break;
        case 'varchar_field_2':
          $set = strval($value);
          break;
        case 'integer_field_1':
          $set = intval($value);
          break;
        case 'integer_field_2':
          $set = intval($value);
          break;
        case 'float_field_1':
          $set = floatval($value);
          break;
        case 'float_field_2':
          $set = floatval($value);
          break;
        case 'text_field_1':
          $set = strval($value);
          break;
        case 'text_field_2':
          $set = strval($value);
          break;
        case 'date_field_1':
          $set = dateval($value);
          break;
        case 'date_field_2':
          $set = dateval($value);
          break;
        case 'datetime_field_1':
          $set = datetimeval($value);
          break;
        case 'datetime_field_2':
          $set = datetimeval($value);
          break;
        case 'boolean_field_1':
          $set = boolval($value);
          break;
        case 'boolean_field_2':
          $set = boolval($value);
          break;
        case 'position':
          $set = intval($value);
          break;
        case 'version':
          $set = intval($value);
          break;
      } // switch
      return parent::setFieldValue($real_name, $set);
    } // switch
  
  }

?>