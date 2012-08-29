<?php

  /**
   * Checklist record class
   *
   * @package activeCollab.modules.checklists
   * @subpackage models
   */
  class Checklist extends ProjectObject {
    
    /**
     * Project tab
     *
     * @var string
     */
    var $project_tab = 'checklists';
    
    /**
     * Name of the route used for view URL
     *
     * @var string
     */
    var $view_route_name = 'project_checklist';
    
    /**
     * Name of the route used for delete URL
     *
     * @var string
     */
    var $edit_route_name = 'project_checklist_edit';
    
    /**
     * Name of the object ID variable used alongside project_id when URL-s are 
     * generated (eg. comment_id, task_id, message_category_id etc)
     *
     * @var string
     */
    var $object_id_param_name = 'checklist_id';
    
    /**
     * Permission name
     * 
     * @var string
     */
    var $permission_name = 'checklist';
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    var $fields = array(
      'id', 
      'type', 
      'module', 
      'project_id', 'milestone_id', 'parent_id', 'parent_type', 
      'name', 'body', 'tags', 
      'state', 'visibility', 
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id',
      'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email', 
      'version', 'position',
    );
    
    /**
     * Checklists is made out of tasks
     * 
     * @var boolean
     */
    var $can_have_tasks = true;
    
    /**
     * Is this object completable
     *
     * @var boolean
     */
    var $can_be_completed = true;
    
    /**
     * Checklists are taggable
     *
     * @var boolean
     */
    var $can_be_tagged = true;
    
    /**
     * Checklists can use reminders
     *
     * @var boolean
     */
    var $can_send_reminders = true;
    
    /**
     * Checklists can be copied
     *
     * @var boolean
     */
    var $can_be_copied = true;
    
    /**
     * Checklists can be moved
     *
     * @var boolean
     */
    var $can_be_moved = true;
    
    /**
     * Construct checklist
     *
     * @param mixed $id
     * @return Checklist
     */
    function __construct($id = null) {
      $this->setModule(CHECKLISTS_MODULE);
      parent::__construct($id);
    } // __construct
    
    /**
     * Prepare project section breadcrumb when this object is accessed directly 
     * and not through module controller
     *
     * @param Wireframe $wireframe
     * @return null
     */
    function prepareProjectSectionBreadcrumb(&$wireframe) {
      $wireframe->addBreadCrumb(lang('Checklists'), assemble_url('project_checklists', array('project_id' => $this->getProjectId())));
    } // prepareProjectSectionBreadcrumb
    
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
      return ProjectObject::canAdd($user, $project, 'checklist');
    } // canAdd
    
    /**
     * Returns true if $user can create a new ticket in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canReorder($user, $project) {
      return $user->getProjectPermission('checklist', $project) == PROJECT_PERMISSION_MANAGE;
    } // canReorder
    
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
        $errors->addError(lang('Checklist summary is required'), 'name');
      } // if
      
      parent::validate($errors, true);
    } // validate
  
  } // Checklist

?>