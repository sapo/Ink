<?php

  // We need MobileAccessProjectsController
  use_controller('mobile_access_projects', MOBILE_ACCESS_MODULE);

  /**
   * Mobile Access Project controller
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessProjectController extends MobileAccessController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access_project';
    
    /**
     * Array of breadcrumbs
     *
     * @var array
     */
    var $breadcrumbs ;
    
    /**
     * Current project
     *
     * @var Project
     */
    var $active_project;
    
    /**
     * Available project sections
     * 
     * @var array
     */
    var $project_sections;
    
    /**
     * Currently active project section
     *
     * @var string
     */
    var $active_project_section;
    
    /**
     * Controller description name
     * 
     * @var string
     */
    var $controller_description_name;
    
    /**
     * Turn categories support on or off
     *
     * @var boolean
     */
    var $enable_categories = false;
    
    /**
     * Currently selected category
     *
     * @var Category
     */
    var $active_category;
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return MobileAccessController extends ApplicationController 
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->disableCategories();
      $project_id = $this->request->get('project_id');
      if($project_id) {
        $this->active_project = Projects::findById($project_id);
      } // if
      
      if(!instance_of($this->active_project, 'Project')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->logged_user->isProjectMember($this->active_project)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
              
      if($this->active_project->getType() == PROJECT_TYPE_SYSTEM) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $this->project_sections = array();
      
      $this->project_sections[] = array("name" => "overview", "full_name" => lang("Overview"), "url" => assemble_url('mobile_access_view_project', array('project_id' => $this->active_project->getId())));
      
      if (module_loaded('discussions') && $this->logged_user->getProjectPermission('discussion', $this->active_project)) {
        $this->project_sections[] = array("name" => "discussions", "full_name" => lang("Discussions"), "url" => assemble_url('mobile_access_view_discussions', array('project_id' => $this->active_project->getId())));
      };
      
      if (module_loaded('milestones') && $this->logged_user->getProjectPermission('milestone', $this->active_project)) {
        $this->project_sections[] = array("name" => "milestones", "full_name" => lang("Milestones"), "url" => assemble_url('mobile_access_view_milestones', array('project_id' => $this->active_project->getId())));
      };
      
      if (module_loaded('files') && $this->logged_user->getProjectPermission('file', $this->active_project)) {
        $this->project_sections[] = array("name" => "files", "full_name" => lang("Files"), "url" => assemble_url('mobile_access_view_files', array('project_id' => $this->active_project->getId())));
      };
      
      if (module_loaded('checklists') && $this->logged_user->getProjectPermission('checklist', $this->active_project)) {
        $this->project_sections[] = array("name" => "checklists", "full_name" => lang("Checklists"), "url" => assemble_url('mobile_access_view_checklists', array('project_id' => $this->active_project->getId())));
      };
      
      if (module_loaded('pages') && $this->logged_user->getProjectPermission('page', $this->active_project)) {
        $this->project_sections[] = array("name" => "pages", "full_name" => lang("Pages"), "url" => assemble_url('mobile_access_view_pages', array('project_id' => $this->active_project->getId())));
      };
      
      if (module_loaded('tickets') && $this->logged_user->getProjectPermission('ticket', $this->active_project)) {
        $this->project_sections[] = array("name" => "tickets", "full_name" => lang("Tickets"), "url" => assemble_url('mobile_access_view_tickets', array('project_id' => $this->active_project->getId())));
      };
      
      if (module_loaded('timetracking') && $this->logged_user->getProjectPermission('timerecord', $this->active_project)) {
        $this->project_sections[] = array("name" => "timetracking", "full_name" => lang("Time"), "url" => assemble_url('mobile_access_view_timerecords', array('project_id' => $this->active_project->getId())));
      };
      
      //if($this->active_project->isLoaded() && $this->enable_categories) {

      $this->addBreadcrumb(lang('Project'), assemble_url('mobile_access_view_project', array("project_id" => $this->active_project->getId())));      
      
      $this->smarty->assign(array(
        "page_title"     => $this->active_project->getName(),
        "active_project" => $this->active_project,
        "project_sections" => $this->project_sections,
        "page_breadcrumbs"  => $this->breadcrumbs,
        "active_project_section" => 'overview',
        "active_category" => $this->active_category,
      ));
            
    } // __construct
    
    /**
     * enable Categories
     *
     */
    function enableCategories() {
      $this->enable_categories = false;
      if($this->active_project->isLoaded()) {
        $this->enable_categories = true;
        $category_id = $this->request->get('category_id');
        if($category_id) {
          $this->active_category = Categories::findById($category_id);
        } // if
        
        if(instance_of($this->active_category, 'Category')) {
          if($this->active_category->getProjectId() != $this->active_project->getId()) {
            $this->active_category = new Category(); // this category is not part of selected project
          } // if
        } else {
          $this->active_category = new Category(); // invalid category instance
        } // if
      } else {
        $this->active_category = new Category(); // categories disabled or category not selected
      } // if
      
      $this->smarty->assign(array(
        "enable_categories" => $this->enable_categories,
        "active_category" => $this->active_category,
      ));
    } // enableCategories
    
    
    /**
     * disable categories
     *
     */
    function disableCategories() {
      $this->enable_categories = false;
      $this->active_category = new Category();
      $this->smarty->assign(array(
        "enable_categories" => $this->enable_categories,
        "active_category" => $this->active_category,
      ));
    } // disableCategories
    
    /**
     * Adds breadcrumb into breadcrumbs array
     *
     * @param string $breadcrumb_name
     * @param string $breadcrumb_url
     */
    function addBreadcrumb($breadcrumb_name, $breadcrumb_url = null) {
      $this->breadcrumbs[] = array(
        "name" => $breadcrumb_name,
        "url"  => $breadcrumb_url,
      );
      $this->smarty->assign('page_breadcrumbs', $this->breadcrumbs);
    } // addBreadcrumb
    
    /**
     * Display project info
     *
     */
    function index() {
      $this->addBreadcrumb(lang('Overview'));
      $this->smarty->assign(array(
        "page_back_url" => assemble_url('mobile_access_projects'),
        "project_leader" => $this->active_project->getLeader(),
        "project_group"  => $this->active_project->getGroup(),
        "project_company" => $this->active_project->getCompany(),
        "late_and_today" => ProjectObjects::findLateAndToday($this->logged_user, $this->active_project, get_day_project_object_types()),
        "recent_activities" => ActivityLogs::findProjectActivitiesByUser($this->active_project, $this->logged_user, 15),
        'upcoming_objects' => ProjectObjects::findUpcoming($this->logged_user, $this->active_project, get_day_project_object_types()),
      ));
    } // project
    
    
    /**
     * Add comment to object
     *
     */
    function add_comment() {

      $parent_id = $this->request->get('parent_id');
      $parent = ProjectObjects::findById($parent_id);
      
      if (!instance_of($parent, 'ProjectObject')) {
        $this->httpError(HTTP_NOT_FOUND);
      } // if
      
      if(!$parent->canComment($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $comment_data = $this->request->post('comment');
      
      $this->smarty->assign(array(
        'parent'   => $parent,
        'comment_data'    => $comment_data,
        'recent_comments' => Comments::findRecentObject($parent, 5, STATE_VISIBLE, $this->logged_user->getVisibility()),
        'page_back_url' => mobile_access_module_get_view_url($parent),
      ));
      
      
      $this->addBreadcrumb(ucfirst(lang($parent->getModule())), assemble_url('mobile_access_view_'.$parent->getModule(),array('project_id' => $this->active_project->getId())));
      $this->addBreadcrumb($parent->getName(), mobile_access_module_get_view_url($parent));
      $this->addBreadcrumb('Add Comment');
      if (($this->request->isSubmitted())) {
        db_begin_work();
        $comment = new Comment();
        $comment->setAttributes($comment_data);
        $comment->setParent($parent);
        $comment->setProjectId($this->active_project->getId());
        $comment->setState(STATE_VISIBLE);
        $comment->setVisibility($parent->getVisibility());
        $comment->setCreatedBy($this->logged_user);
        $save = $comment->save();
        
        if($save && !is_error($save)) {
          db_commit();
          flash_success('Comment successfully posted');
          $this->redirectToUrl(mobile_access_module_get_view_url($comment));
        } else {
          db_rollback();
          $this->smarty->assign('errors', $save);
          $this->render();
        } // if
      } // if
    } // add_comment
    
  } // MobileAccessProjectController
?>