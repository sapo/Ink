<?php

  /**
   * People controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class PeopleController extends ApplicationController {
    
    /**
     * Actions available through the API
     *
     * @var array
     */
    var $api_actions = array('index');
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return PeopleController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->wireframe->addBreadCrumb(lang('People'), assemble_url('people'));
      $this->wireframe->current_menu_item = 'people';
      
      if(Company::canAdd($this->logged_user)) {
        $this->wireframe->addPageAction(lang('New Company'), assemble_url('people_companies_add'));
      } // if
    } // __construct
    
    /**
     * Show companies index page
     *
     * @param void
     * @return null
     */
    function index() {
      if($this->request->isApiCall()) {
        $this->serveData(Companies::findByIds($this->logged_user->visibleCompanyIds()), 'companies');
      } else {
        $page = (integer) $this->request->get('page');
        if($page < 1) {
          $page = 1;
        } // if
        
        list($companies, $pagination) = Companies::paginateActive($this->logged_user, $page, 30);
        
        $this->smarty->assign(array(
          'companies' => $companies,
          'pagination' => $pagination,
        ));
      } // if
    } // index
    
    /**
     * Show archive page
     *
     * @param void
     * @return null
     */
    function archive() {
      if($this->logged_user->isPeopleManager()) {
        $this->wireframe->addBreadCrumb(lang('Archive'), assemble_url('people_archive'));
        
        $page = (integer) $this->request->get('page');
        if($page < 1) {
          $page = 1;
        } // if
        
        list($companies, $pagination) = Companies::paginateArchived($this->logged_user, $page, 30);
        
        $this->smarty->assign(array(
          'companies' => $companies,
          'pagination' => $pagination,
        ));
      } else {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
    } // archive
  
  }

?>