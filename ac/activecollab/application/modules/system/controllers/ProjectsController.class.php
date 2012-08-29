<?php

  /**
   * Project controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectsController extends ApplicationController {
    
    /**
     * Active module name, will be 
     *
     * @var string
     */
    var $active_module = SYSTEM_MODULE;
    
    /**
     * Methods that are available through API
     *
     * @var array
     */
    var $api_actions = array('index');
    
    /**
     * Construct project controller
     *
     * @param Request $request
     * @return ProjectController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->wireframe->addBreadCrumb(lang('Projects'), assemble_url('projects'));
      $this->wireframe->current_menu_item = 'projects';
      
      if($this->controller_name == 'projects' || $this->controller_name == 'project_groups') {
        if(Project::canAdd($this->logged_user)) {
          $this->wireframe->addPageAction(lang('New Project'), assemble_url('projects_add'));
        } // if
      } // if
    } // __construct
    
    // ---------------------------------------------------
    //  General project management actions
    // ---------------------------------------------------
    
    /**
     * List all project this user have access to
     *
     * @param void
     * @return null
     */
    function index() {
      if($this->request->isApiCall()) {
        $this->serveData(Projects::findByUser($this->logged_user), 'projects');
      } else {
        $per_page = 10;
        $page = (integer) $this->request->get('page');
        if($page < 1) {
          $page = 1;
        } // if
        
        if(!$this->logged_user->isOwner()) {
          $group_by = 'group';
        } else {
          $group_by = $this->request->get('group_by');
          if($group_by != 'client') {
            $group_by = 'group';
          } // if
        } // if
        
        $this->smarty->assign('group_by', $group_by);
        
        if($group_by == 'group') {
          $group = null;
          $group_id = $this->request->getId('group_id');
          if($group_id) {
            $group = ProjectGroups::findById($group_id);
          } // if
          
          if(instance_of($group, 'ProjectGroup')) {
            list($projects, $pagination) = Projects::paginateByUserAndGroup($this->logged_user, $group, array(PROJECT_STATUS_ACTIVE), $page, $per_page, true);
          } else {
            list($projects, $pagination) = Projects::paginateByUser($this->logged_user, array(PROJECT_STATUS_ACTIVE), $page, $per_page, true);
          } // if
          
          $this->smarty->assign(array(
            'projects'       => $projects,
            'pagination'     => $pagination,
            'groups'         => ProjectGroups::findAll($this->logged_user),
            'selected_group' => $group,
          ));
        } else {
          $company = null;
          $company_id = $this->request->getId('company_id');
          if($company_id) {
            $company = Companies::findById($company_id);
          } // if
          
          if(!instance_of($company, 'Company')) {
            $company = $this->owner_company;
          } // if
          
          list($projects, $pagination) = Projects::paginateByUserAndCompany($this->logged_user, $company, array(PROJECT_STATUS_ACTIVE), $page, $per_page, true);
          
          $this->smarty->assign(array(
            'projects'         => $projects,
            'pagination'       => $pagination,
            'companies'        => Companies::findClients($this->logged_user),
            'selected_company' => $company,
          ));
        } // if
      } // if
    } // index
    
    /**
     * Projects Arhive
     *
     * @param void
     * @return null
     */
    function archive() {
      if($this->request->isApiCall()) {
        $this->serveData(Projects::findByUser($this->logged_user), 'projects');
      } // if
      
      $per_page = 10;
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
      
      if(!$this->logged_user->isOwner()) {
        $group_by = 'group';
      } else {
        $group_by = $this->request->get('group_by');
        if($group_by != 'client') {
          $group_by = 'group';
        } // if
      } // if
      
      $filter_by_status = $this->request->get('filter');
      if (is_null($filter_by_status)) {
        $filter_by_status = 'all';
      } // if
      
      switch ($filter_by_status) {
        case 'all':
          $statuses = array(PROJECT_STATUS_COMPLETED, PROJECT_STATUS_CANCELED, PROJECT_STATUS_PAUSED);
          break;
        case 'completed': 
          $statuses = array(PROJECT_STATUS_COMPLETED);
          break;
        case 'paused': 
          $statuses = array(PROJECT_STATUS_PAUSED);
          break;
        case 'canceled': 
          $statuses = array(PROJECT_STATUS_CANCELED);
          break;
        default:
          $statuses = array(PROJECT_STATUS_COMPLETED, PROJECT_STATUS_CANCELED, PROJECT_STATUS_PAUSED);
          break;
      } // switch project status filter
      
      $this->smarty->assign(array(
        'group_by'  => $group_by,
        'filter'    => $filter_by_status
      ));
      
      if($group_by == 'group') {
        $group = null;
        $group_id = $this->request->getId('group_id');
        if($group_id) {
          $group = ProjectGroups::findById($group_id);
        } // if
        
        if(instance_of($group, 'ProjectGroup')) {
          list($projects, $pagination) = Projects::paginateByUserAndGroup($this->logged_user, $group, $statuses, $page, $per_page, true);
        } else {
          list($projects, $pagination) = Projects::paginateByUser($this->logged_user, $statuses, $page, $per_page, true);
        } // if
        
        $this->smarty->assign(array(
          'projects'       => $projects,
          'pagination'     => $pagination,
          'groups'         => ProjectGroups::findAll($this->logged_user),
          'selected_group' => $group,
        ));
      } else {
        $company = null;
        $company_id = $this->request->getId('company_id');
        if($company_id) {
          $company = Companies::findById($company_id);
        } // if
        
        if(!instance_of($company, 'Company')) {
          $company = $this->owner_company;
        } // if
        
        list($projects, $pagination) = Projects::paginateByUserAndCompany($this->logged_user, $company, $statuses, $page, $per_page);
        
        $this->smarty->assign(array(
          'projects'         => $projects,
          'pagination'       => $pagination,
          'companies'        => Companies::findClients($this->logged_user),
          'selected_company' => $company,
        ));
      } // if
    } // arhive
  
  } // ProjectsController

?>