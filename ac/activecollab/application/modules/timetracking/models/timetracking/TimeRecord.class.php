<?php

  /**
   * TimeRecord class
   * 
   * Class that represents single timetracking record row in project_objects 
   * table
   * 
   * @package activeCollab.modules.timetracking
   * @subpackage models
   */
  class TimeRecord extends ProjectObject {
    
    /**
     * Project tab
     *
     * @var string
     */
    var $project_tab = 'time';
    
    /**
     * Name of the route used for view URL
     *
     * @var string
     */
    var $view_route_name = 'project_time';
    
    /**
     * Name of the route used for edit URL
     *
     * @var string
     */
    var $edit_route_name = 'project_time_edit';
    
    /**
     * Name of the object ID variable used alongside project_id when URL-s are 
     * generated (eg. comment_id, task_id, message_category_id etc)
     *
     * @var string
     */
    var $object_id_param_name = 'time_id';
    
    /**
     * Permission name
     * 
     * @var string
     */
    var $permission_name = 'timerecord';
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    var $fields = array(
      'id', 
      'type', 
      'module', 
      'project_id', 'parent_id', 'parent_type', 
      'body',
      'state', 'visibility', 
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id',
      'varchar_field_1', // user name
      'varchar_field_2', // user email
      'integer_field_1', // user who spent some time
      'integer_field_2', // billable status
      'float_field_1', // we will store value here...
      'date_field_1', // record date
      'position', 'version', 
    );
    
    /**
     * Additional field map
     *
     * @var array
     */
    var $field_map = array(
      'user_id'         => 'integer_field_1',
      'billable_status' => 'integer_field_2',
      'user_name'       => 'varchar_field_1',
      'user_email'      => 'varchar_field_2',
      'value'           => 'float_field_1',
      'record_date'     => 'date_field_1',
    );
    
    /**
     * Construct a new TimeRecord
     *
     * @param mixed $id
     * @return TimeRecord
     */
    function __construct($id = null) {
      $this->setModule(TIMETRACKING_MODULE);
      parent::__construct($id);
    } // __construct
    
    /**
     * Return name string
     *
     * @param void
     * @return string
     */
    function getName() {
      $user = $this->getUser();
      $value = $this->getValue();
      
      if(instance_of($user, 'User')) {
        return $value == 1 ? 
          lang(':value hour by :name', array('value' => $value, 'name' => $user->getDisplayName(true))) : 
          lang(':value hours by :name', array('value' => $value, 'name' => $user->getDisplayName(true)));
      } else {
        return $value == 1 ? lang(':value hour', array('value' => $value)) : lang(':value hours', array('value' => $value));
      } // if
    } // getName
    
    /**
     * Return activity log comment (record summary)
     *
     * @param void
     * @return string
     */
    function getActivityLogComment() {
      return $this->getBody();
    } // getActivityLogComment
    
    /**
     * Utility method that will update parents has_time flag
     *
     * @param void
     * @return boolean
     */
    function refreshParentHasTime() {
      $parent = $this->getParent();
      if(instance_of($parent, 'ProjectObject')) {
        $parent->setHasTime((boolean) TimeRecords::sumObjectTime($parent));
        return $parent->save();
      } // if
      return true;
    } // refreshParentHasTime
    
    /**
     * Set record properties based on form / API attributes array
     *
     * @param array $attributes
     * @return null
     */
    function setAttributes($attributes) {
      
      // Compatibility code: In activeCollab 1.1.5 is_billable and is_billed 
      // were replaced with billable_status field. This code maintains API 
      // compatibility with the old, activeCollab 1.1.4 behavior
      if((isset($attributes['is_billable']) || isset($attributes['is_billed'])) && !isset($attributes['billable_status'])) {
        if(array_var($attributes, 'is_billable')) {
          $attributes['billable_status'] = array_var($attributes, 'is_billed') ? BILLABLE_STATUS_BILLED : BILLABLE_STATUS_BILLABLE;
        } else {
          $attributes['billable_status'] = BILLABLE_STATUS_NOT_BILLABLE;
        } // if
        
        if(isset($attributes['is_billable'])) {
          unset($attributes['is_billable']);
        } // if
        
        if(isset($attributes['is_billed'])) {
          unset($attributes['is_billed']);
        } // if
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get user_id
     *
     * @param null
     * @return integer
     */
    function getUserId() {
      return $this->getIntegerField1();
    } // getUserId
    
    /**
     * Set user_id value
     *
     * @param integer $value
     * @return null
     */
    function setUserId($value) {
      return $this->setIntegerField1($value);
    } // setUserId
    
    /**
     * Get user_name
     *
     * @param null
     * @return string
     */
    function getUserName() {
      return $this->getVarcharField1();
    } // getUserName
    
    /**
     * Set user_name value
     *
     * @param string $value
     * @return null
     */
    function setUserName($value) {
      return $this->setVarcharField1($value);
    } // setUserName
    
    /**
     * Get user_email
     *
     * @param null
     * @return string
     */
    function getUserEmail() {
      return $this->getVarcharField2();
    } // getUserEmail
    
    /**
     * Set user_email value
     *
     * @param string $value
     * @return null
     */
    function setUserEmail($value) {
      return $this->setVarcharField2($value);
    } // setUserEmail
    
    /**
     * Return User
     *
     * @param void
     * @return User
     */
    function getUser() {
      $user_id = $this->getUserId();
      
      if($user_id) {
        $user = Users::findById($user_id);
      } else {
        $user = null;
      } // if
      
      if(instance_of($user, 'User')) {
        return $user;
      } else {
        return new AnonymousUser($this->getUserName(), $this->getUserEmail());
      } // if
    } // getUser
    
    /**
     * Set user
     *
     * @param User $user
     * @return null
     */
    function setUser($user) {
    	if(instance_of($user, 'User')) {
    	  $this->setUserId($user->getId());
    	  $this->setUserName($user->getDisplayName());
    	  $this->setUserEmail($user->getEmail());
    	} elseif(instance_of($user, 'AnonymousUser')) {
    	  $this->setUserId(0);
    	  $this->setUserName($user->getName());
    	  $this->setUserEmail($user->getEmail());
    	} else {
    	  $this->setUserId(0);
    	  $this->setUserName(null);
    	  $this->setUserEmail(null);
    	} // if
    } // setUser
    
    /**
     * Get value
     *
     * @param null
     * @return float
     */
    function getValue() {
      return $this->getFloatField1();
    } // getValue
    
    /**
     * Set value value
     *
     * @param float $value
     * @return null
     */
    function setValue($value) {
      return $this->setFloatField1($value);
    } // setValue
    
    /**
     * Get record_date
     *
     * @param null
     * @return DateValue
     */
    function getRecordDate() {
      return $this->getDateField1();
    } // getRecordDate
    
    /**
     * Set record_date value
     *
     * @param DateValue $value
     * @return null
     */
    function setRecordDate($value) {
      return $this->setDateField1($value);
    } // setRecordDate
    
    /**
     * Return billable status
     *
     * @param void
     * @return integer
     */
    function getBillableStatus() {
      return $this->getIntegerField2();
    } // getBillableStatus
    
    /**
     * Set billable status
     *
     * @param integer $value
     * @return integer
     */
    function setBillableStatus($value) {
      return $this->setIntegerField2($value);
    } // setBillableStatus
    
    /**
     * Returns true if this record is billable
     *
     * @param void
     * @return boolean
     */
    function isBillable() {
      return $this->getBillableStatus() >= BILLABLE_STATUS_BILLABLE;
    } // isBillable
    
    /**
     * Returns true if this record is marked as billed
     *
     * @param void
     * @return boolean
     */
    function isBilled() {
      return $this->getBillableStatus() >= BILLABLE_STATUS_BILLED;
    } // isBilled
    
    /**
     * Describe time record
     *
     * @param User $user
     * @param array $additional
     * @return array
     */
    function describe($user, $additional = null) {
      $result = parent::describe($user, array(
        'describe_project'     => array_var($additional, 'describe_project'), 
        'describe_parent'      => array_var($additional, 'describe_parent'), 
        'describe_milestone'   => array_var($additional, 'describe_milestone'), 
        'describe_comments'    => array_var($additional, 'describe_comments'), 
        'describe_tasks'       => array_var($additional, 'describe_tasks'), 
        'describe_attachments' => array_var($additional, 'describe_attachments'), 
      ));
      
      $result['billable_status'] = $this->getBillableStatus();
      $result['value'] = $this->getValue();
      $result['record_date'] = $this->getRecordDate();
      
      // Fields kept for compatibility reasons. In activeCollab 1.1.5 
      // is_billable and is_billed were replaced with billable_status flag and 
      // are considerated deprecated
      $result['is_billable'] = $this->isBillable();
      $result['is_billed'] = $this->isBilled();
      
      $record_user = $this->getUser();
      if(instance_of($record_user , 'User')) {
        $result['user'] = $record_user ->describe($user);
      } else {
        $result['user_id'] = $this->getUserId();
      } // if

      return $result;
    } // describe
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view day records (statically availables)
     *
     * @param Project $project
     * @param DateValue $day
     * @param integer $page
     * @return string
     * @static 
     */
    function getViewDayUrl($project, $day, $page = null) {
      $params = array(
        'project_id' => $project->getId(), 
        'day' => $day->getYear() . '-' . $day->getMonth() . '-' . $day->getDay(),
      );
      if($page !== null) {
        $params['page'] = $page;
      } // if
    	return assemble_url('project_time_day', $params);
    } // getViewDayUrl
    
    /**
     * Return update billed state URL
     *
     * @param boolean $to
     * @return string
     */
    function getUpdateBilledStateUrl($to = true) {
      return assemble_url('project_time_update_billed_state', array(
        'project_id' => $this->getProjectId(),
        'time_id' => $this->getId(),
        'to' => (boolean) $to,
      ));
    } // getUpdateBilledStateUrl
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can add new time records to $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canAdd($user, $project) {
    	return ProjectObject::canAdd($user, $project, 'timerecord');
    } // canAdd
    
    /**
     * Returns true if $user can track time for $object
     *
     * @param User $user
     * @param ProjectObject $object
     * @return boolean
     */
    function canAddFor($user, $object) {
    	return (instance_of($object, 'Task') || instance_of($object, 'Ticket')) && TimeRecord::canAdd($user, $object->getProject());
    } // canAddFor
    
    /**
     * Returns true if $user can change billable status of specific record
     *
     * @param User $user
     * @return boolean
     */
    function canChangeBillableStatus($user) {
      return $user->getProjectPermission('timerecord', $this->getProject()) >= PROJECT_PERMISSION_MANAGE;
    } // canChangeBillableStatus
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if($this->isNew()) {
        if(!$this->validatePresenceOf('integer_field_1')) {
          $errors->addError(lang('Please select user'), 'user_id');
        } // if
      } // if
      
    	if($this->validatePresenceOf('float_field_1')) {
    	  if($this->getValue() <= 0) {
    	    $errors->addError(lang('You need to enter more than 0 hours'), 'value');
    	  } // if
    	} else {
    	  $errors->addError(lang('Time spent value is required'), 'value');
    	} // if
    	
    	if(!$this->validatePresenceOf('date_field_1')) {
    	  $errors->addError(lang('Please select record date'), 'record_date');
    	} // if
    	
    	parent::validate($errors, true);
    } // validate
    
    /**
     * Override save method
     *
     * @param void
     * @return boolean
     */
    function save() {
      $save = parent::save();
      if($save && !is_error($save)) {
        $this->refreshParentHasTime();
      } // if
      return $save;
    } // save
    
    /**
     * Delete row from database
     *
     * @param void
     * @return boolean
     */
    function delete() {
      $delete = parent::delete();
      if($delete && !is_error($delete)) {
        $this->refreshParentHasTime();
      } // if
      return $delete;
    } // delete
    
  } // TimeRecord

?>