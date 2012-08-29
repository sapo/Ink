<?php

  // We need MobileAccessProjectController
  use_controller('mobile_access_project', MOBILE_ACCESS_MODULE);

  /**
   * Mobile Access Project Discussions controller
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessProjectDiscussionsController extends MobileAccessProjectController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access_project_discussions';
        
    /**
     * Active discussion (if exists)
     *
     * @var Discussion
     */
    var $active_discussion;
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return MobileAccessController extends ApplicationController 
     */
    function __construct($request) {
      parent::__construct($request);
      if($this->logged_user->getProjectPermission('discussion', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->controller_description_name = lang('Discussions');
      $this->active_project_section = 'discussions';
      $this->enableCategories();
      
      $discussion_id = $this->request->getId('object_id');
      if($discussion_id) {
        $this->active_discussion = ProjectObjects::findById($discussion_id);
      } // if
      
      if(!instance_of($this->active_discussion, 'Discussion')) {
        $this->active_discussion = new Discussion();
      } // if
      
      $this->smarty->assign(array(
        "active_discussion" => $this->active_discussion,
        "active_project_section" => $this->active_project_section
      ));
      
      $this->addBreadcrumb($this->controller_description_name, assemble_url('mobile_access_view_discussions',array('project_id' => $this->active_project->getId())));
    } // __construct
    
    /**
     * List of discussions
     *
     */
    function index() {
      $this->addBreadcrumb(lang('List'));
      
      $page = $this->getPaginationPage();
      $per_page = 30; // discussions per page

      if(!$this->active_category->isNew()) {
        list($discussions, $pagination) = Discussions::paginateByCategory($this->active_category, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
      } else {
        list($discussions, $pagination) = Discussions::paginateByProject($this->active_project, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
      } // if
      
      $this->smarty->assign(array(
        'discussions'    => $discussions,
        'pagination'     => $pagination,
        'categories'     => Categories::findByModuleSection($this->active_project, 'discussions', 'discussions'),
        'pagination_url' => assemble_url('mobile_access_view_discussions', array('project_id' => $this->active_project->getId())),
        'page_back_url' => assemble_url('mobile_access_view_project', array('project_id' => $this->active_project->getId())),
      ));
    } // index
    
    /**
     * View the discussion
     *
     */
    function view() {
      if($this->active_discussion->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_discussion->canView($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      ProjectObjectViews::log($this->active_discussion, $this->logged_user);
      
      $parent = $this->active_discussion->getParent();
      if(instance_of($parent, 'Category')) {
        $this->active_category = $parent;
        $this->smarty->assign('active_category', $parent);
      } // if
      
      $page = $this->request->get('page');
      if ($page <1) {
        $page = 1;
      } // if
           
      $this->smarty->assign(array(
        'page_back_url' => assemble_url('mobile_access_view_discussions', array('project_id' => $this->active_project->getId())),
        'page'        => $page
      ));
      
      $this->addBreadcrumb(str_excerpt(clean($this->active_discussion->getName()),10),mobile_access_module_get_view_url($this->active_discussion));
      $this->addBreadcrumb(lang('View'));
     
    } // view
    
  } // MobileAccessProjectDiscussionsController
?>