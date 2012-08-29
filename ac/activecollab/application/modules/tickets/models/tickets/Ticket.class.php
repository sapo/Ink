<?php

  /**
   * Ticket record class
   *
   * @package activeCollab.modules.tickets
   * @subpackage models
   */
  class Ticket extends ProjectObject {
    
    /**
     * Project tab
     *
     * @var string
     */
    var $project_tab = 'tickets';
    
    /**
     * Permission name
     * 
     * @var string
     */
    var $permission_name = 'ticket';
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    var $fields = array(
      'id', 
      'type', 'source', 'module', 
      'project_id', 'milestone_id', 'parent_id', 'parent_type', 
      'name', 'body', 'tags', 'comments_count', 
      'state', 'visibility', 'is_locked', 'priority', 'due_on',
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id', 
      'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email',
      'integer_field_1', // for ticket ID (on project level)
      'has_time', 'position', 'version'
    );
    
    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'ticket_id' => 'integer_field_1'
    );
    
    /**
     * We can have comments for this object
     * 
     * @var boolean
     */
    var $can_have_comments = true;
    
    /**
     * Number of comments per page
     *
     * @var integer
     */
    var $comments_per_page = 25;
    
    /**
     * Can this object have assignees
     * 
     * @var boolean
     */
    var $can_have_assignees = true;
    
    /**
     * Tickets can have subscribers
     *
     * @var boolean
     */
    var $can_have_subscribers = true;
    
    /**
     * Tickets can have subtasks
     *
     * @var boolean
     */
    var $can_have_tasks = true;
    
    /**
     * Tickets can have attachments
     *
     * @var boolean
     */
    var $can_have_attachments = true;
    
    /**
     * Tickets can use reminders
     *
     * @var boolean
     */
    var $can_send_reminders = true;
    
    /**
     * Is this object completable
     *
     * @var boolean
     */
    var $can_be_completed = true;
    
    /**
     * Tickets are taggable
     *
     * @var boolean
     */
    var $can_be_tagged = true;
    
    /**
     * Tickets can be copied
     *
     * @var boolean
     */
    var $can_be_copied = true;
    
    /**
     * Tickets can be moved
     *
     * @var boolean
     */
    var $can_be_moved = true;
    
    /**
     * Construct a new ticket
     *
     * @param mixed $id
     * @return Ticket
     */
    function __construct($id = null) {
      $this->setModule(TICKETS_MODULE);
      parent::__construct($id);
    } // __construct
    
    /**
     * Cached ticket changes
     * 
     * @var array
     */
    var $changes = false;
    
    /**
     * Return ticket changes
     *
     * @param integer $count
     * @return array
     */
    function getChanges($count = null) {
      if($count !== null) {
        return TicketChanges::findByTicket($this, $count);
      } // if
      
      // All!
      if($this->changes === false) {
        $this->changes = TicketChanges::findByTicket($this);
      } // if
      return $this->changes;
    } // getChanges
    
    /**
     * Cached number of ticket changes
     *
     * @var integer
     */
    var $changes_count = false;
    
    /**
     * Return number of ticket changes
     *
     * @param void
     * @return integer
     */
    function countChanges() {
      if($this->changes_count === false) {
        $this->changes_count = TicketChanges::countByTicket($this);
      } // if
      return $this->changes_count;
    } // countChanges
    
    /**
     * Describe ticket
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
      $result['ticket_id'] = $this->getTicketId();
      return $result;
    } // describe
    
    /**
     * Prepare project section breadcrumb when this object is accessed directly 
     * and not through module controller
     *
     * @param Wireframe $wireframe
     * @return null
     */
    function prepareProjectSectionBreadcrumb(&$wireframe) {
      $wireframe->addBreadCrumb(lang('Tickets'), assemble_url('project_tickets', array('project_id' => $this->getProjectId())));
    } // prepareProjectSectionBreadcrumb
    
    /**
     * Prepare portal project section breadcrumb when this object is accessed
     * directly and not through module controller
     *
     * @param Portal $portal
     * @param Wireframe $wireframe
     * @return null
     */
    function preparePortalProjectSectionBreadcrumb($portal, &$wireframe) {
    	$wireframe->addBreadCrumb(lang('Tickets'), assemble_url('portal_tickets', array('portal_name' => $portal->getSlug())));
    } // preparePortalProjectSectionBreadcrumb
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can create a new ticket in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canAdd($user, $project) {
      return ProjectObject::canAdd($user, $project, 'ticket');
    } // canAdd
    
    /**
     * Returns true if tickets can be created through portal
     *
     * @param Portal $portal
     * @return boolean
     */
    function canAddViaPortal($portal) {
    	return parent::canAddViaPortal($portal, 'ticket');
    } // canAddViaPortal
    
    /**
     * Returns true if $user can manage tickets in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canManage($user, $project) {
      return ProjectObject::canManage($user, $project, 'ticket');
    } // canManage
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get ticket_id
     *
     * @param null
     * @return integer
     */
    function getTicketId() {
      return $this->getIntegerField1();
    } // getTicketId
    
    /**
     * Set ticket_id value
     *
     * @param integer $value
     * @return null
     */
    function setTicketId($value) {
      return $this->setIntegerField1($value);
    } // setTicketId
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view ticket URL
     *
     * @param integer $page
     * @return string
     */
    function getViewUrl($page = null) {
      $params = array(
        'project_id' => $this->getProjectId(),
        'ticket_id' => $this->getTicketId(),
      );
      
      if($page) {
        $params['page'] = $page;
      } // if
      
      return assemble_url('project_ticket', $params);
    } // getViewUrl
    
    /**
     * Return portal view ticket URL
     *
     * @param Portal $portal
     * @param integer $page
     * @return string
     */
    function getPortalViewUrl($portal, $page = null) {
    	$params = array(
    		'portal_name' => $portal->getSlug(),
    		'ticket_id'   => $this->getTicketId()
    	);
    	
    	if($page) {
    		$params['page'] = $page;
    	} // if
    	
    	return assemble_url('portal_ticket', $params);
    } // getPortalViewUrl
    
    /**
     * Return edit URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('project_ticket_edit', array(
        'project_id' => $this->getProjectId(),
        'ticket_id' => $this->getTicketId(),
      ));
    } // getEditUrl
    
    /**
     * Return ticket changes URL
     *
     * @param void
     * @return string
     */
    function getChangesUrl() {
      return assemble_url('project_ticket_changes', array(
        'project_id' => $this->getProjectId(),
        'ticket_id' => $this->getTicketId(),
      ));
    } // getChangesUrl
    
    /**
     * Return portal ticket changes URL
     *
     * @param Portal $portal
     * @return string
     */
    function getPortalChangesUrl($portal) {
    	return assemble_url('portal_ticket_changes', array(
    		'portal_name' => $portal->getSlug(),
    		'ticket_id'   =>$this->getTicketId()
    	));
    } // getPortalChangesUrl
    
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
        $errors->addError(lang('Ticket summary should be at least 3 characters long'), 'name');
      } // if
      
      parent::validate($errors, true);
    } // validate
    
    /**
     * Save into database
     * 
     * @return boolean
     */
    function save() {
      if($this->isNew()) {
        $this->setTicketId(Tickets::findNextTicketIdByProject($this->getProjectId()));
      } // if
      
      $changes = null;
      if($this->isLoaded()) {
        $log_fields = array('project_id', 'milestone_id', 'parent_id', 'name', 'body', 'priority', 'due_on', 'completed_on');
        
        $changes = new TicketChange();
        
        $changes->setTicketId($this->getId());
        $changes->setVersion($this->getVersion());
        $changes->setCreatedOn(DateTimeValue::now());
        $changes->setCreatedBy(get_logged_user());
        
        if($this->new_assignees !== false) {
          list($old_assignees, $old_owner_id) = $this->getAssignmentData();
          
          if(is_array($this->new_assignees) && isset($this->new_assignees[0]) && isset($this->new_assignees[1])) {
            $new_assignees = $this->new_assignees[0];
            $new_owner_id = $this->new_assignees[1];
          } else {
            $new_assignees = array();
            $new_owner_id = 0;
          } // if
          
          if($new_owner_id != $old_owner_id) {
            $changes->addChange('owner', $old_owner_id, $new_owner_id);
          } // if
          
          sort($new_assignees);
          sort($old_assignees);
          
          if($new_assignees != $old_assignees) {
            $changes->addChange('assignees', $old_assignees, $new_assignees);
          } // if
        } // if
        
        foreach($this->modified_fields as $field) {
          if(!in_array($field, $log_fields)) {
            continue;
          } // if
          
          $old_value = array_var($this->old_values, $field);
          $new_value = array_var($this->values, $field);
          
          if($old_value != $new_value) {
            $changes->addChange($field, $old_value, $new_value);
          } // if
        } // foreach
      } // if
      
      $save = parent::save();
      if($save && !is_error($save)) {
        if(instance_of($changes, 'TicketChange') && count($changes->changes)) {
          $this->changes = false;
          $changes->save();
        } // if
      } // if
      
      return $save;
    } // save
  
  } // Ticket

?>