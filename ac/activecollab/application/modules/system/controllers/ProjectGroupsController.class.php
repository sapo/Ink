<?php

  // Inherit projects controller
  use_controller('projects', SYSTEM_MODULE);

  /**
   * Project groups controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectGroupsController extends ProjectsController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'project_groups';
    
    /**
     * Active module name
     *
     * @var string
     */
    var $active_module = SYSTEM_MODULE;
    
    /**
     * Active project group
     *
     * @var ProjectGroup
     */
    var $active_project_group;
    
    /**
     * Methods that are available through API
     *
     * @var array
     */
    var $api_actions = array('index', 'add', 'edit', 'delete', 'view');
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return ProjectGroupsController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->wireframe->addBreadCrumb(lang('Groups'), assemble_url('project_groups'));
      
      $group_id = $this->request->get('project_group_id');
      if($group_id) {
        $this->active_project_group = ProjectGroups::findById($group_id);
      } // if
      
      if(instance_of($this->active_project_group, 'ProjectGroup')) {
        $this->wireframe->addBreadCrumb($this->active_project_group->getName(), $this->active_project_group->getViewUrl());
      } else {
        $this->active_project_group = new ProjectGroup();
      } // if
      
      $this->smarty->assign(array(
        'active_project_group' => $this->active_project_group,
      ));
    } // __construct
    
    /**
     * Show groups index page
     *
     * @param void
     * @return null
     */
    function index() {
      $project_groups = ProjectGroups::findAll($this->logged_user);
      
      if($this->request->getFormat() == FORMAT_HTML) {
      	$this->smarty->assign(array(
      	  'project_groups' => $project_groups,
      	  'can_add_project_group' => ProjectGroup::canAdd($this->logged_user),
      	));
      } else {
      	$this->serveData($project_groups, 'project_groups');
      } // if
    } // index
    
    /**
     * Browse specific project group
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_project_group->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->request->isApiCall()) {
        $this->serveData($this->active_project_group, 'project_group', array('describe_projects' => true));
      } else {
        $per_page = 10;
        $page = (integer) $this->request->get('page');
        if($page < 1) {
          $page = 1;
        } // if
        
        list($projects, $pagination) = Projects::paginateByGroup($this->active_project_group, $page, $per_page);
        
        $this->smarty->assign(array(
          'projects' => $projects,
          'pagination' => $pagination,
        ));
      } // if
    } // view
    
    /**
     * Create a new group
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      if(!ProjectGroup::canAdd($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $project_group_data = $this->request->post('project_group');
      $this->smarty->assign('project_group_data', $project_group_data);
      
      if($this->request->isSubmitted()) {
        $this->active_project_group = new ProjectGroup(); // just in case...
        $this->active_project_group->setAttributes($project_group_data);
        
        $save = $this->active_project_group->save();
        if($save && !is_error($save)) {
          if($this->request->isApiCall()) {
            $this->serveData($this->active_project_group, 'project_group');
          } elseif($this->request->isAsyncCall()) {
            $this->smarty->assign('project_group', $this->active_project_group);
            print $this->smarty->fetch(get_template_path('_project_group_row', 'project_groups', SYSTEM_MODULE));
            die();
          } else {
            flash_success("Project group ':name' has been created", array('name' => $this->active_project_group->getName()));
            $this->redirectTo('project_groups');
          } // if
        } else {
          if($this->request->isApiCall() || $this->request->isAsyncCall()) {
            $this->serveData($save);
          } else {
            $this->smarty->assign('errors', $save);
          } // if
        } // if
      } else {
        if($this->request->isApiCall()) {
          $this->httpError(HTTP_ERR_BAD_REQUEST);
        } // if
      } // if
    } // add
    
    /**
     * Quick add project group
     *
     * @param void
     * @return null
     */
    function quick_add() {
      if($this->request->isSubmitted() && $this->request->isAsyncCall()) {
        if(!ProjectGroup::canAdd($this->logged_user)) {
          $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
        } // if
        
        $project_group = new ProjectGroup();
        $project_group->setAttributes($this->request->post('project_group'));
        
        $save = $project_group->save();
        if($save && !is_error($save)) {
          print $project_group->getId();
          die();
        } else {
          $this->serveData($save);
        } // if
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // quick_add
    
    /**
     * Update project group
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->active_project_group->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_project_group->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $project_group_data = $this->request->post('project_group');
      if(!is_array($project_group_data)) {
        $project_group_data = array(
          'name' => $this->active_project_group->getName(),
        );
      } // if
      $this->smarty->assign('project_group_data', $project_group_data);
      
      if($this->request->isSubmitted()) {
        $old_name = $this->active_project_group->getName();
        $this->active_project_group->setAttributes($project_group_data);
        $save = $this->active_project_group->save();
        
        if($save && !is_error($save)) {
          if($this->request->isApiCall()) {
            $this->serveData($this->active_project_group, 'project_group');
          } elseif($this->request->isAsyncCall()) {
            print $this->active_project_group->getName();
            die();
          } else {
            flash_success("Project group ':name' has been updated", array('name' => $old_name));
            $this->redirectTo('project_groups');
          } // if
        } else {
          if($this->request->isApiCall() || $this->request->isAsyncCall()) {
            $this->serveData($save);
          } else {
            $this->smarty->assign('errors', $save);
          } // if
        } // if
      } else {
        if($this->request->isApiCall()) {
          $this->httpError(HTTP_ERR_BAD_REQUEST);
        } // if
      } // if
    } // edit
    
    /**
     * Delete specific project group
     *
     * @param void
     * @return null
     */
    function delete() {
      if($this->active_project_group->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_project_group->canDelete($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isSubmitted()) {
        $delete = $this->active_project_group->delete();
        if($delete && !is_error($delete)) {
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success("Project group ':name' has been deleted", array('name' => $this->active_project_group->getName()));
            $this->redirectTo('project_groups');
          } else {
            $this->httpOk();
          } // if
        } else {
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_error("Failed to delete ':name' group. Reason: :reason", array('name' => $this->active_project_group->getName(), 'reason' => $delete->getMessage()));
            $this->redirectTo('project_groups');
          } else {
            $this->serveData($delete);
          } // if
        } // if
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // delete
  
  }

?>