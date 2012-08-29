<?php

  // We need MobileAccessController
  use_controller('mobile_access', MOBILE_ACCESS_MODULE);

  /**
   * Mobile Access People controller
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessPeopleController extends MobileAccessController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access_people';
    
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
     * Company listing
     *
     */
    function index() {
      $company_ids = $this->logged_user->visibleCompanyIds();
      foreach($company_ids as $k => $v) {
        if($this->owner_company->getId() == $v) {
          unset($company_ids[$k]);
          break;
        } // if
      } // foreach
      
      $per_page = 50;
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
      
      list($companies, $pagination) = Companies::paginateActive($this->logged_user, $page, $per_page);
           
      $this->smarty->assign(array(
        'companies'   => $companies,
        'pagination'  => $pagination,
        'total_pages' => ceil(count($company_ids) / $per_page),
        'page_title'  => lang('People'),
        'pagination_url' => assemble_url('mobile_access_people'),
      ));
    } // index
    
    /**
     * Display company details
     *
     */
    function company() {
      $current_company = Companies::findById($this->request->get('object_id'));
      if(!instance_of($current_company, 'Company')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$current_company->isOwner() && !in_array($current_company->getId(), $this->logged_user->visibleCompanyIds())) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $users = $current_company->getUsers($this->logged_user->visibleUserIds());
      if (!$current_company->isOwner()) {
        $projects = Projects::findByUserAndCompany($this->logged_user, $current_company);
      }
      $this->smarty->assign(array(
        'current_company' => $current_company,
        'current_company_users' => $users,
        'current_company_projects'  => $projects,
        "page_title"  => $current_company->getName(),
        "page_back_url" => assemble_url('mobile_access_people'),
      ));
    } // company
    
    /**
     * Display user details
     */
    function user() {
    	$user_id = $this->request->get('object_id');
    	if($user_id) {
    		$current_user = Users::findById($user_id);
    	} // if
    	
    	if(!instance_of($current_user,'User')) {
      	$this->httpError(HTTP_ERR_NOT_FOUND);
    	}
    	
  	  if(!in_array($current_user->getId(), $this->logged_user->visibleUserIds())) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	$current_user_company = $current_user->getCompany();
    	
    	$this->smarty->assign(array(
    	 "current_user" => $current_user,
    	 "current_user_company" => $current_user->getCompany(),
    	 "page_title"  => $current_user->getName(),
    	 "page_back_url"   => mobile_access_module_get_view_url($current_user_company),
    	));
    	
    } // user
    
  } // MobileAccessPeopleController
?>