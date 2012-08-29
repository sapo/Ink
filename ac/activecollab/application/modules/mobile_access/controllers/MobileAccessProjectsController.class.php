<?php

  // We need MobileAccessController
  use_controller('mobile_access', MOBILE_ACCESS_MODULE);

  /**
   * Mobile Access Projects controller
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessProjectsController extends MobileAccessController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access_projects';
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return MobileAccessController extends ApplicationController 
     */
    function __construct($request) {
      parent::__construct($request);
    } // __construct
    
    /**
     * Projects listing
     *
     */
    function index() {
      $per_page = 10;
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
           
      $group = null;
      $group_id = $this->request->getId('group_id');
      if($group_id) {
        $group = ProjectGroups::findById($group_id);
      } // if
      
      if(instance_of($group, 'ProjectGroup')) {
        list($projects, $pagination) = Projects::paginateByUserAndGroup($this->logged_user, $group, array(PROJECT_STATUS_ACTIVE), $page, $per_page);
      } else {
        list($projects, $pagination) = Projects::paginateByUser($this->logged_user, array(PROJECT_STATUS_ACTIVE), $page, $per_page);
      } // if
      
      $this->smarty->assign(array(
        'groups'         => ProjectGroups::findAll($this->logged_user),
        'selected_group_id' => $group_id,
        'pagination'        => $pagination,
        'projects'    => $projects,
        'page_title'  => lang('Projects'),
        'paginator_url' => assemble_url('mobile_access_projects'),
      ));
    } // index

  } // MobileAccessProjectsController
?>