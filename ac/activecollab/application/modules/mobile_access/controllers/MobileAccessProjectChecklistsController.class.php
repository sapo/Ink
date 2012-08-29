<?php

  // We need MobileAccessProjectController
  use_controller('mobile_access_project', MOBILE_ACCESS_MODULE);

  /**
   * Mobile Access Project Checklists controller
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessProjectChecklistsController extends MobileAccessProjectController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access_project_checklists';
        
    /**
     * Active checklist (if exists)
     *
     * @var Checklist
     */
    var $active_checklist;
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return MobileAccessController extends ApplicationController 
     */
    function __construct($request) {
      parent::__construct($request);
      
      if($this->logged_user->getProjectPermission('checklist', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if

      $this->controller_description_name = lang('Checklists');
      $this->active_project_section = 'checklists';
      
      $checklist_id = $this->request->getId('object_id');
      if($checklist_id) {
        $this->active_checklist = ProjectObjects::findById($checklist_id);
      } // if
      
      if(!instance_of($this->active_checklist, 'Checklist')) {
        $this->active_checklist = new Checklist();
      } // if
      
      $this->smarty->assign(array(
        "active_checklist" => $this->active_checklist,
        "active_project_section" => $this->active_project_section
      ));
      
      $this->addBreadcrumb($this->controller_description_name, assemble_url('mobile_access_view_checklists',array('project_id' => $this->active_project->getId())));
    } // __construct
    
    /**
     * List of checklists
     *
     */
    function index() {
      $this->addBreadcrumb(lang('List'));
      $checklists = Checklists::findActiveByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility());
      
      $this->smarty->assign(array(
        'checklists'    => $checklists,
        'page_back_url' => assemble_url('mobile_access_view_project', array('project_id' => $this->active_project->getId())),
      ));
    } // index
    
    /**
     * View the checklist
     *
     */
    function view() {
      if ($this->active_checklist->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_checklist->canView($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      ProjectObjectViews::log($this->active_checklist, $this->logged_user);
            
      $this->addBreadcrumb(str_excerpt(clean($this->active_checklist->getName()),10),mobile_access_module_get_view_url($this->active_checklist));
      $this->addBreadcrumb(lang('View'));
     
    } // view
    
  } // MobileAccessProjectChecklistsController
?>