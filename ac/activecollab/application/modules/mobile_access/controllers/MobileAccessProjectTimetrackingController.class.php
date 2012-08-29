<?php

  // We need MobileAccessProjectController
  use_controller('mobile_access_project', MOBILE_ACCESS_MODULE);

  /**
   * Mobile Access Project Timetracking controller
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessProjectTimetrackingController extends MobileAccessProjectController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access_project_timetracking';
       
    /**
     * Active page (if exists)
     *
     * @var Page
     */
    var $active_timerecord;
    
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return MobileAccessController extends ApplicationController 
     */
    function __construct($request) {
      parent::__construct($request);
      
      if($this->logged_user->getProjectPermission('timerecord', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->controller_description_name = lang('Time');
      $this->active_project_section = 'timetracking';
      $this->disableCategories();
            
      // active timerecord
      $timerecord_id = $this->request->getId('object_id');
      if($timerecord_id) {
        $this->active_timerecord = ProjectObjects::findById($timerecord_id);
      } // if
      
      if(!instance_of($this->active_timerecord, 'Timerecord')) {
        $this->active_timerecord = new TimeRecord();
      } // if
      
      $this->smarty->assign(array(
        "active_timerecord" => $this->active_timerecord,
        "active_project_section" => $this->active_project_section,
      ));
      
      $this->addBreadcrumb($this->controller_description_name, assemble_url('mobile_access_view_timerecords',array('project_id' => $this->active_project->getId())));
      $this->addBreadcrumb(lang('View'));
    } // __construct
    
    /**
     * List of timerecords
     *
     */
    function index() {
      $per_page = 15;
      if (!$this->active_timerecord->isNew()) {
        $page = ceil(TimeRecords::findTimerecordNum($this->active_timerecord, STATE_VISIBLE, $this->logged_user->getVisibility()) / $per_page);        
      } else {
        $page = (integer) $this->request->get('page');
        if($page < 1){
          $page = 1;
        } // if
      }
      
      list($timerecords, $pagination) = TimeRecords::paginateByProject($this->active_project, $page, $per_page);
      
      $this->smarty->assign(array(
        'timerecords'    => $timerecords,
        'pagination'     => $pagination,
        'pagination_url' => assemble_url('mobile_access_view_timerecords', array('project_id' => $this->active_project->getId())),
        'page_back_url' => assemble_url('mobile_access_view_project', array('project_id' => $this->active_project->getId())),
      ));

    } // index
    
  } // MobileAccessProjectTimetrackingController
?>