<?php

  // We need MobileAccessProjectController
  use_controller('mobile_access_project', MOBILE_ACCESS_MODULE);

  /**
   * Mobile Access Project Milestones controller
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessProjectMilestonesController extends MobileAccessProjectController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access_project_milestones';
        
    /**
     * Active milestone (if exists)
     *
     * @var Milestone
     */
    var $active_milestone;
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return MobileAccessController extends ApplicationController 
     */
    function __construct($request) {
      parent::__construct($request);
      
      if($this->logged_user->getProjectPermission('milestone', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->controller_description_name = lang('Milestones');
      $this->active_project_section = 'milestones';
      
      $milestone_id = $this->request->getId('object_id');
      if($milestone_id) {
        $this->active_milestone = ProjectObjects::findById($milestone_id);
      } // if
      
      if(!instance_of($this->active_milestone, 'Milestone')) {
        $this->active_milestone = new Milestone();
      } // if
      
      $this->smarty->assign(array(
        "active_milestone" => $this->active_milestone,
        "active_project_section" => $this->active_project_section,
      ));
      
      $this->addBreadcrumb($this->controller_description_name, assemble_url('mobile_access_view_milestones',array('project_id' => $this->active_project->getId())));
    } // __construct
    
    /**
     * List of discussions
     *
     */
    function index() {
      $this->addBreadcrumb(lang('List'));
      $milestones = Milestones::findActiveByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility());
      
      $this->smarty->assign(array(
        "milestones" => $milestones,
        "page_back_url" => assemble_url('mobile_access_view_project' ,array('project_id' => $this->active_project->getId())),
      ));
    } // index
    
    /**
     * View the discussion
     *
     */
    function view() {
      if($this->active_milestone->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_milestone->canView($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      ProjectObjectViews::log($this->active_milestone, $this->logged_user);
      
      $total_objects = 0;
      $objects = $this->active_milestone->getObjects($this->logged_user);
      if(is_foreachable($objects)) {
        foreach($objects as $objects_by_module) {
          $total_objects += count($objects_by_module);
        } // foreach
      } // if
      
      $this->smarty->assign(array(
        'objects' => $objects,
        'total_objects' => $total_objects,
        'page_back_url' => assemble_url('mobile_access_view_milestones', array('project_id' => $this->active_project->getId())),
      ));
      
      $this->addBreadcrumb(str_excerpt(clean($this->active_milestone->getName()),10),mobile_access_module_get_view_url($this->active_milestone));
      $this->addBreadcrumb(lang('View'));
     
    } // view
    
  } // MobileAccessProjectDiscussionsController
?>