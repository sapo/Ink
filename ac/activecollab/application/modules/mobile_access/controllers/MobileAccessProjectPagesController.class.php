<?php

  // We need MobileAccessProjectController
  use_controller('mobile_access_project', MOBILE_ACCESS_MODULE);

  /**
   * Mobile Access Project Pages controller
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessProjectPagesController extends MobileAccessProjectController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access_project_pages';
    
    /**
     * Main page
     * 
     * @var Page
     */
    var $start_page;
    
    /**
     * Active page (if exists)
     *
     * @var Page
     */
    var $active_page;
    
    /**
     * Array of subpages
     *
     * @var array
     */
    var $active_page_subpages;
    
    /**
     * Array of revisions of current page
     *
     * @var array
     */
    var $active_page_revisions;
    
    /**
     * Parent of currently active page
     *
     * @var Page
     */
    var $active_page_parent;
    
    /**
     * Latest revision of currently active page
     *
     * @var Page
     */
    var $active_page_latest_revision;
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return MobileAccessController extends ApplicationController 
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->enableCategories();
      
      if($this->logged_user->getProjectPermission('page', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->controller_description_name = lang('Pages');
      $this->active_project_section = 'pages';
      // Active page
      
      $this->active_page = ProjectObjects::findById($this->request->getId('object_id'));
      if (!instance_of($this->active_page, 'Page')) {
        $this->active_page = new Page();
      } // if
      
      $this->addBreadcrumb($this->controller_description_name, assemble_url('mobile_access_view_pages',array('project_id' => $this->active_project->getId())));
      
      $this->smarty->assign(array(
        'active_page' => $this->active_page,
      ));
    } // __construct
    
    /**
     * List of pages
     *
     */
    function index() {
      $per_page = 20;
      $page = (integer) $this->request->get('page');
      if($page < 1){
        $page = 1;
      } // if   
      
      $pagination = null;
      if ($this->active_category->isLoaded()) {
        $this->addBreadcrumb($this->active_category->getName());
        $pages = Pages::findByCategory($this->active_category, STATE_VISIBLE, $this->logged_user->getVisibility());
      } else {
        $this->addBreadcrumb(lang('Recently Modified'));
        list($pages, $pagination) = Pages::paginateByProject($this->active_project, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
      } // if
      
      $this->smarty->assign(array(
        'pages' => $pages,
        'pagination' => $pagination,
        'categories' => Categories::findByModuleSection($this->active_project, PAGES_MODULE, 'pages'),
        'pagination_url' => assemble_url('mobile_access_view_pages', array('project_id' => $this->active_project->getId())),
        'page_back_url' => assemble_url('mobile_access_view_project', array('project_id' => $this->active_project->getId())),
      ));

    } // index
    
    /**
     * Render Page
     * 
     * @param void
     * @return null
     */
    function view() {
      if($this->active_page->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if(!$this->active_page->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $this->addBreadcrumb($this->active_page->getName());
           
      $parent = $this->active_page->getParent();
      if (instance_of($parent, 'Page')) {
        $page_back_url = mobile_access_module_get_view_url($parent);
      } else if (instance_of($parent, 'Category')) {
        $page_back_url = assemble_url('mobile_access_view_pages', array('project_id' => $this->active_project->getId(), 'category_id' => $parent->getId()));
      } // if
      
      $this->smarty->assign(array(
        'page_back_url' => $page_back_url,
      ));
    } // page
    
    /**
     * Render Page Version
     * 
     * @param void
     * @return null
     */
    function version() {
      if (!instance_of($this->active_page, 'Page')) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if(!$this->active_page->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
     
      $version = PageVersions::findByPage($this->active_page, $this->request->get('version'));
      if (!instance_of($version, 'PageVersion')) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      $this->addBreadcrumb($this->active_page->getName(), mobile_access_module_get_view_url($this->active_page));
      $this->addBreadcrumb(lang('Version #:version', array('version' => $version->getVersion())));
      
      $this->smarty->assign(array(
        'page_back_url' => mobile_access_module_get_view_url($this->active_page),
        'version'       => $version,
      ));
      
    } // version
    
  } // MobileAccessProjectPagesController
?>