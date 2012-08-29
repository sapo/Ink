<?php

  /**
   * Milestone record class
   *
   * @package activeCollab.modules.milestones
   * @subpackage models
   */
  class Milestone extends ProjectObject {
    
    /**
     * Project tab
     *
     * @var string
     */
    var $project_tab = 'milestones';
    
    /**
     * Name of the route used for view URL
     *
     * @var string
     */
    var $view_route_name = 'project_milestone';
    
    /**
     * Name of the route used for portal view URL
     *
     * @var string
     */
    var $portal_view_route_name = 'portal_milestone';
    
    /**
     * Name of the route used for delete URL
     *
     * @var string
     */
    var $edit_route_name = 'project_milestone_edit';
    
    /**
     * Name of the object ID variable used alongside project_id when URL-s are 
     * generated (eg. comment_id, task_id, message_category_id etc)
     *
     * @var string
     */
    var $object_id_param_name = 'milestone_id';
    
    /**
     * Permission name
     * 
     * @var string
     */
    var $permission_name = 'milestone';
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    var $fields = array(
      'id', 
      'type', 
      'module', 
      'project_id', 
      'parent_id', 'parent_type', 
      'name', 'body', 'tags', 
      'state', 'visibility', 'priority', 'due_on', 
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id',
      'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email', 
      'date_field_1', // for start_on
      'version', 'position',
    );
    
    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'start_on' => 'date_field_1',
    );
    
    /**
     * Can this object have assignees
     * 
     * @var boolean
     */
    var $can_have_assignees = true;
    
    /**
     * Milestone is completable
     *
     * @var boolean
     */
    var $can_be_completed = true;
    
    /**
     * Milestone can have subscribers
     *
     * @var boolean
     */
    var $can_have_subscribers = true;
    
    /**
     * Milestones are taggable
     *
     * @var boolean
     */
    var $can_be_tagged = true;
    
    /**
     * Milestones can be copied
     *
     * @var boolean
     */
    var $can_be_copied = true;
    
    /**
     * Milestones can be moved
     *
     * @var boolean
     */
    var $can_be_moved = true;
    
    /**
     * Milestone can use reminders
     *
     * @var boolean
     */
    var $can_send_reminders = true;
  
    /**
     * Constructor
     *
     * @param void
     * @return Milestone
     */
    function __construct($id = null) {
      $this->setModule(MILESTONES_MODULE);
      parent::__construct($id);
    } // __construct
    
    /**
     * Return object that given user can see
     *
     * @param User $user
     * @return array
     */
    function getObjects($user) {
      $objects = array();
      event_trigger('on_milestone_objects', array(&$this, &$objects, &$user));
      return $objects;
    } // getObjects

    /**
     * Describe milestone
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
        'describe_assignees'   => array_var($additional, 'describe_assignees'), 
      ));
      
      unset($result['due_on']);;
      unset($result['milestone_id']);
      
      $result['start_on'] = $this->getStartOn();
      $result['due_on'] = $this->getDueOn();
      
      return $result;
    } // describe
    
    /**
     * Returns true if start on and due on are the same day
     *
     * @param void
     * @return boolean
     */
    function isDayMilestone() {
      $start_on = $this->getStartOn();
      $due_on = $this->getDueOn();
      
      return $start_on->getTimestamp() == $due_on->getTimestamp();
    } // isDayMilestone
    
    /**
     * Advance for give number of seconts
     *
     * @param integer $seconds
     * @param boolean $save
     * @return boolean
     */
    function advance($seconds, $save = false) {
      if($seconds != 0) {
      	$start_on = $this->getStartOn();
      	$due_on = $this->getDueOn();
      	
      	$this->setStartOn($start_on->advance($seconds, false));
      	$this->setDueOn($due_on->advance($seconds, false));
      	
      	if($save) {
      	  return $this->save();
      	} // if
      } // if
    	return true;
    } // advance
    
    /**
     * Reschedule this milestone
     *
     * @param DateValue $new_start_on
     * @param DateValue $new_due_on
     * @param boolean $reschedule_tasks
     * @return boolean
     */
    function reschedule($new_start_on, $new_due_on, $reschedule_tasks = false) {
      $errors = new ValidationErrors();
      
      if(!instance_of($new_start_on, 'DateValue')) {
        $errors->addError(lang('Start date is not valid'), 'start_on');
      } // if
      
      if(!instance_of($new_due_on, 'DateValue')) {
        $errors->addError(lang('Due date is not valid'), 'start_on');
      } // if
      
      if($errors->hasErrors()) {
        return $errors;
      } // if
      
      $old_start_on = $this->getStartOn();
      $old_due_on = $this->getDueOn();
      
      $this->setStartOn($new_start_on);
      $this->setDueOn($new_due_on);
      
      $save = $this->save();
      if($save && !is_error($save)) {
        if($reschedule_tasks) {
          $diff_days = (integer) ceil(($new_due_on->getTimestamp() - $old_due_on->getTimestamp()) / 86400);
          if($diff_days != 0) {
            $project_objects_table = TABLE_PREFIX . 'project_objects';
            $completable_types = get_completable_project_object_types();
            
            $rows = db_execute_all("SELECT id FROM $project_objects_table WHERE milestone_id = ? AND type IN (?)", $this->getId(), $completable_types);
            if(is_foreachable($rows)) {
              $related_object_ids = array();
              foreach($rows as $row) {
                $related_object_ids[] = (integer) $row['id'];
              } // foreach
              
              db_execute("UPDATE $project_objects_table SET due_on = DATE_ADD(due_on, INTERVAL $diff_days DAY) WHERE (id IN (?) OR parent_id IN (?)) AND type IN (?)", $related_object_ids, $related_object_ids, $completable_types);
            } // if
          } // if
        } // if
      } // if
      
      return $save;
    } // reschedule
    
    /**
     * Prepare project section breadcrumb when this object is accessed directly 
     * and not through module controller
     *
     * @param Wireframe $wireframe
     * @return null
     */
    function prepareProjectSectionBreadcrumb(&$wireframe) {
      $wireframe->addBreadCrumb(lang('Milestones'), assemble_url('project_milestones', array('project_id' => $this->getProjectId())));
    } // prepareProjectSectionBreadcrumb
    
    // ---------------------------------------------------
    //  Portal getters and setters
    // ---------------------------------------------------
    
    /**
     * Count portal objects by milestone
     *
     * @param Portal $portal
     * @return integer
     */
    function getPortalObjectsCount($portal) {
    	$portal_objects = $this->getPortalObjects($portal);
    	
    	$total_project_objects = 0;
    	if(is_foreachable($portal_objects)) {
    		foreach($portal_objects as $portal_objects_by_module) {
    			$total_project_objects += count($portal_objects_by_module);
    		} // foreach
    	} // if
    	return $total_project_objects;
    } // getPortalObjectsCount
    
    /**
     * Return all portal objects
     *
     * @param Portal $portal
     * @return array
     */
    function getPortalObjects($portal) {
    	$portal_objects = array();
      event_trigger('on_portal_milestone_objects', array(&$this, &$portal_objects, &$portal));
      return $portal_objects;
    } // getPortalObjects
    
    /**
     * Render milestone subobjects add links
     *
     * @param Portal $portal
     * @return string
     */
    function renderSubobjectsAddLinks($portal) {
    	$links = $this->getPortalAddLinks($portal);
    	
    	$rendered = '';
    	if(is_foreachable($links)) {
    		$total_links = count($links);
    		$counter = 1;
    		foreach($links as $k => $v) {
    			$rendered .= open_html_tag('a', array('href' => $v)) . $k . '</a>';
    			if($counter < ($total_links - 1)) {
    				$rendered .= ', ';
    			} elseif($counter == ($total_links - 1)) {
    				$rendered .= ' ' . lang('or') . ' ';
    			} // if
    			$counter++;
    		} // foreach
    	} // if
    	return $rendered;
    } // renderAddLinks
    
    /**
     * Return portal add links
     *
     * @param Portal $portal
     * @return array
     */
    function getPortalAddLinks($portal) {
    	$links = array();
			event_trigger('on_portal_milestone_add_links', array(&$this, &$links, &$portal));
			return $links;
    } // getPortalAddLinks
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get start_on
     *
     * @param null
     * @return DateValue
     */
    function getStartOn() {
      return $this->getDateField1();
    } // getStartOn
    
    /**
     * Set start_on value
     *
     * @param DateValue $value
     * @return null
     */
    function setStartOn($value) {
      return $this->setDateField1($value);
    } // setStartOn
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return reschedule milestone URL
     *
     * @param void
     * @return string
     */
    function getRescheduleUrl() {
      return assemble_url('project_milestone_reschedule', array(
        'project_id' => $this->getProjectId(),
        'milestone_id' => $this->getId(),
      ));
    } // getRescheduleUrl
    
    /**
     * Return portal milestone view URL
     *
     * @param Portal $portal
     * @param integer $page
     * @return string
     */
    function getPortalViewUrl($portal, $page = null) {
    	$params = $page === null ? null : array('page' => $page);
    	return parent::getPortalViewUrl($portal, $params);
    } // getPortalViewUrl
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can create a new milestone in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canAdd($user, $project) {
      return ProjectObject::canAdd($user, $project, 'milestone');
    } // canAdd
    
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
      if(!$this->validatePresenceOf('name', 3)) {
        $errors->addError(lang('Milestone name is required'), 'name');
      } // if
      
      if(!$this->validatePresenceOf('start_on')) {
        $errors->addError(lang('Start date is required'), 'start_on');
      } // if
      
      if(!$this->validatePresenceOf('due_on')) {
        $errors->addError(lang('Due date is required'), 'due_on');
      } // if
      
      $start_on = $this->getStartOn();
      $due_on = $this->getDueOn();
      
      if(instance_of($start_on, 'DateValue') && instance_of($due_on, 'DateValue')) {
        if($start_on->getTimestamp() > $due_on->getTimestamp()) {
          $errors->addError(lang('Start date needs to be before due date'), 'date_range');
        } // if
      } // if
      
      parent::validate($errors, true);
    } // validate
  
  }

?>