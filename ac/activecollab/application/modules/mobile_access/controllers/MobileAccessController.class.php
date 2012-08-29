<?php
  
  /**
   * Main chat controller
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessController extends ApplicationController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access';
    
    /**
     * Name of the login route
     *
     * @var string
     */
    var $login_route = 'mobile_access_login';
    
    /**
     * Active module
     *
     * @var unknown_type
     */
    var $active_module = MOBILE_ACCESS_MODULE;
    
    /**
     * Active project, if room belongs to the project
     *
     * @var Project
     */
    var $active_project = null;
    
    /**
     * Mobile Device Id
     *
     * @var string
     */
    var $mobile_device;
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return MobileAccessController extends ApplicationController 
     */
    function __construct($request) {
      parent::__construct($request);
      $this->mobile_device = mobile_access_module_get_compatible_device(USER_AGENT);
     
      $this->setLayout('wireframe');
      
      // assign variables to smarty
      $this->smarty->assign(array(
        "mobile_device" => $this->mobile_device,
        "module_assets_url"    => get_asset_url('modules/'.$this->active_module),
      ));

    } // __construct
    
    /**
     * Returns pagination page 
     *
     * @return integer
     */
    function getPaginationPage() {
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
      return $page;
    } // getPaginationPage
    
    /**
     * Mobile Access Homepage
     *
     */
    function index() {
      $pinned_project_ids = PinnedProjects::findProjectIdsByUser($this->logged_user);
      if(is_foreachable($pinned_project_ids)) {
        $pinned_projects = Projects::findByIds($pinned_project_ids);
      } else {
        $pinned_projects = null;
      } // if
      
      $this->smarty->assign(array(
        "pinned_projects" => $pinned_projects,
      ));
    } // 
    
    /**
     * Redirect to category view
     *
     */
    function view_category() {
      $category_id = (integer) $this->request->get('object_id');
      
      if($category_id) {
        $category = Categories::findById($category_id);
      } else {
        $category = new Category();
      } // if
            
      if ($category->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $project = $category->getProject();
      if (!instance_of($project, 'Project')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $base_url = assemble_url('mobile_access_view_'.$category->getModule(),array('project_id'=>$project->getId()));
      $this->redirectToUrl($base_url.'/?category_id='.$category_id);
    } // view_category
    
    /**
     * Redirect to parent view of task
     * 
     */
    function view_parent_object() {
      $object_id = (integer) $this->request->get('object_id');
      
      if($object_id) {
        $object = ProjectObjects::findById($object_id);
      } else {
        $object = new ProjectObject();
      } // if
      
      if ($object->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
 
      $parent = $object->getParent();
      if (!instance_of($parent, 'ProjectObject')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if (instance_of($object, 'Comment')) {
      	$this->redirectToUrl(mobile_access_module_get_view_url($parent).'#comment_'.$object->getId());
      } else {
        $this->redirectToUrl(mobile_access_module_get_view_url($parent));  
      }
    } // view_task
    
    /**
     * Toggles object completed state
     * 
     */
    function toggle_completed() {
      $object_id = (integer) $this->request->get('object_id');
      
      if($object_id) {
        $object = ProjectObjects::findById($object_id);
      } else {
        $object = new ProjectObject();
      } // if
      
      if ($object->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if ($object->isCompleted()) {
        $object->open($this->logged_user);
      } else {
        $object->complete($this->logged_user);
      }
      
      $this->redirectToUrl(mobile_access_module_get_view_url($object));
    } // toggle_completed
    
    function starred() {
      $objects = StarredObjects::findByUser($this->logged_user);
      $this->smarty->assign(array(
        "objects" => $objects,
        "page_title" => lang('Starred'),
      ));
    } // starred
    
    /**
     * Shows a assignments page
     *
     */
    function assignments() {
      $filter_id = $this->request->getId('filter_id');
      if(!$filter_id) {
        $filter_id = UserConfigOptions::getValue('default_assignments_filter', $this->logged_user);
      } // if
      
      if($filter_id) {
        $active_filter = AssignmentFilters::findById($filter_id);
      } // if
      
      $page = $this->getPaginationPage();
      $per_page = 15;
      
      $pagination = null;
      if (instance_of($active_filter, 'AssignmentFilter')) {
        list($assignments, $pagination) = AssignmentFilters::executeFilter($this->logged_user, $active_filter, true, $page, $per_page);
      } else {
        $assignments = array();
      } // if
            
      $this->smarty->assign(array(
        "objects" => $assignments,
        "page_title" => lang('Assignments'),
        'grouped_filters' => AssignmentFilters::findGrouped($this->logged_user),
        "active_filter" => $active_filter,
        'pagination'           => $pagination,
        'pagination_url' => assemble_url('mobile_access_assignments'),
      ));
    } // assignments
    
  } // Mobile Access