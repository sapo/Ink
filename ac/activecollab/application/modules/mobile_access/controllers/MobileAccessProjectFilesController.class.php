<?php

  // We need MobileAccessProjectController
  use_controller('mobile_access_project', MOBILE_ACCESS_MODULE);

  /**
   * Mobile Access Project Files controller
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessProjectFilesController extends MobileAccessProjectController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access_project_files';
    
    /**
     * Active file (if exists)
     *
     * @var File
     */
    var $active_file;
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return MobileAccessController extends ApplicationController 
     */
    function __construct($request) {      
      parent::__construct($request);
      
      if($this->logged_user->getProjectPermission('file', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->controller_description_name = lang('Files');
      $this->active_project_section = 'files';
      $this->enableCategories();
      
      $file_id = $this->request->getId('object_id');
      if($file_id) {
        $this->active_file = ProjectObjects::findById($file_id);
      } // if
      
      if(!instance_of($this->active_file, 'File')) {
        $this->active_file = new File();
      } // if
      
      $this->smarty->assign(array(
        "active_file" => $this->active_file,
        "active_project_section" => $this->active_project_section
      ));
      
      $this->addBreadcrumb($this->controller_description_name, assemble_url('mobile_access_view_files',array('project_id' => $this->active_project->getId())));
    } // __construct
    
    /**
     * List of files
     *
     */
    function index() {
      $this->addBreadcrumb(lang('List'));
      
      $per_page = 10;
      $page = $this->getPaginationPage();
        
      if (!$this->active_category->isNew()) {
        list($files, $pagination) = Files::paginateByCategory($this->active_category, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
      } else {
        list($files, $pagination) = Files::paginateByProject($this->active_project, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
      } // if
      
      $this->smarty->assign(array(
        'files'          => $files,
        'pagination'     => $pagination,
        'categories'     => Categories::findByModuleSection($this->active_project, 'files', 'files'),
        'pagination_url' => assemble_url('mobile_access_view_files', array('project_id' => $this->active_project->getId())),
        'page_back_url' => assemble_url('mobile_access_view_project', array('project_id' => $this->active_project->getId())),
      ));
    } // index
    
    /**
     * View the file
     *
     */
    function view() {
      if($this->active_file->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_file->canView($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $last_revision = $this->active_file->getLastRevision();
      if(!instance_of($last_revision, 'Attachment')) {
        flash_error('Invalid file - last revision was not found');
        $this->redirectToUrl(assemble_url('mobile_access'));
      } // if
      
      ProjectObjectViews::log($this->active_file, $this->logged_user);
      ProjectObjectViews::log($last_revision, $this->logged_user);
      
      $this->smarty->assign(array(
        'revisions' => $this->active_file->getRevisions(),
        'last_revision' => $last_revision,
        'page_back_url' => assemble_url('mobile_access_view_files', array('project_id' => $this->active_project->getId())),
      ));
      
      $this->addBreadcrumb(str_excerpt(clean($this->active_file->getName()),10),mobile_access_module_get_view_url($this->active_file));
      $this->addBreadcrumb(lang('View'));
     
    } // view
    
  } // MobileAccessProjectFilesController
?>