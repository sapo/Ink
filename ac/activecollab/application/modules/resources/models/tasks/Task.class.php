<?php

  /**
   * Task record class
   *
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class Task extends ProjectObject {
  
    /**
     * Name of the route used for view URL
     *
     * @var string
     */
    var $view_route_name = 'project_task';
    
    /**
     * Name of the portal route used for view task URL
     *
     * @var string
     */
    var $portal_view_route_name = 'portal_task';
    
    /**
     * Name of the route used for delete URL
     *
     * @var string
     */
    var $edit_route_name = 'project_task_edit';
    
    /**
     * Name of the object ID variable used alongside project_id when URL-s are 
     * generated (eg. comment_id, task_id, message_category_id etc)
     *
     * @var string
     */
    var $object_id_param_name = 'task_id';
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    var $fields = array(
      'id', 
      'type', 
      'module', 
      'project_id', 'parent_id', 'parent_type', 'milestone_id', 
      'body', 
      'state', 'visibility', 'priority', 'due_on',
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id',
      'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email',
      'has_time', 'version', 'position',
    );
    
    /**
     * This object can be complted / opened
     *
     * @var boolean
     */
    var $can_be_completed = true;
    
    /**
     * Tasks can have susbscribers
     * 
     * @var boolean
     */
    var $can_have_subscribers = true;
    
    /**
     * Tasks can have assignees
     * 
     * @var boolean
     */
    var $can_have_assignees = true;
    
    /**
     * Run HTML purifier when body is set
     *
     * @var boolean
     */
    var $purify_body = false;
    
    /**
     * Construct tasks
     *
     * @param mixed $id
     * @return Task
     */
    function __construct($id = null) {
      $this->setModule(RESOURCES_MODULE);
      parent::__construct($id);
    } // __construct
    
    /**
     * Return task name (first few words from text)
     *
     * @param void
     * @return string
     */
    function getName() {
      $words = explode(' ', trim($this->getBody()));
      $first_five = array_slice($words, 0, 15);
      
      $name = implode(' ', $first_five);
      return count($words) > 15 ? $name . '...' : $name;
    } // getName
    
    /**
      * Task do not have comments in activity log
      *
      * @param void
      * @return string
      */
    function getActivityLogComment() {
      return null;
    } // getActivityLogComment
    
    /**
     * Return project tab based on parent object
     *
     * @param void
     * @return string
     */
    function getProjectTab() {
    	$parent = $this->getParent();
    	return instance_of($parent, 'ProjectObject') ? $parent->getProjectTab() : 'overview';
    } // getProjectTab
    
    /**
     * Return content in which notifications are sent
     * 
     * Reply to notification will submit comment for context object, if context 
     * is commentable
     *
     * @param void
     * @return ProjectObject
     */
    function getNotificationContext() {
      $parent = $this->getParent();
      return instance_of($parent, 'ProjectObject') && $parent->can_have_comments ? $parent : null;
    } // getNotificationContext
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can access this task
     *
     * @param void
     * @return boolean
     */
    function canView($user) {
    	$parent = $this->getParent();
    	return instance_of($parent, 'ProjectObject') && $parent->canView($user);
    } // canView
    
    /**
     * Returns true only $user can delete parent object
     *
     * @param void
     * @return boolean
     */
    function canDelete($user) {
    	$parent = $this->getParent();
    	return instance_of($parent, 'ProjectObject') ? $parent->canDelete($user) : false;
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view task URL
     *
     * @param void
     * @return string
     */
    function getViewUrl() {
      return assemble_url('project_task', array(
        'project_id' => $this->getProjectId(),
        'task_id' => $this->getId(),
      ));
    } // getViewUrl
    
    /**
     * Return portal view task URL
     *
     * @param Portal $portal
     * @param integer $page
     * @return string
     */
    function getPortalViewUrl($portal, $page = null) {
    	$params = $page === null ? null : array('page' => $page);
    	return parent::getPortalViewUrl($portal, $params);
    } // getPortalViewUrl
    
    /**
     * Return complete task URL
     *
     * @param boolean $async
     * @param boolean $parent
     * @return string
     */
    function getCompleteUrl($async = false, $parent = false) {
      if($parent) {
        return parent::getCompleteUrl($async);
      } // if
      
      $params = array(
        'project_id' => $this->getProjectId(),
        'task_id' => $this->getId(),
      );
      
      if($async) {
        $params['async'] = true;
      } // if
      
      return assemble_url('project_task_complete', $params);
    } // getCompleteUrl
    
    /**
     * Return open task URL
     *
     * @param boolean $async
     * @param boolean $parent
     * @return string
     */
    function getOpenUrl($async = false, $parent = false) {
      if($parent) {
        return parent::getOpenurl($async);
      } // if
      
      $params = array(
        'project_id' => $this->getProjectId(),
        'task_id' => $this->getId(),
      );
      
      if($async) {
        $params['async'] = true;
      } // if
      
      return assemble_url('project_task_open', $params);
    } // getOpenUrl
    
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
      if(!$this->validatePresenceOf('body', 3)) {
        $errors->addError(lang('Task text is required. Min length is 3 letters'), 'body');
      } // if
      
      parent::validate($errors, true);
    } // validate
  
  } // Task

?>