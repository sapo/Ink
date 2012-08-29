<?php

  // Use people controller
  use_controller('people', SYSTEM_MODULE);

  /**
   * Company profile controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class CompaniesController extends PeopleController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'companies';
    
    /**
     * Name of the parent module
     *
     * @var mixed
     */
    var $active_module = SYSTEM_MODULE;
    
    /**
     * Selected company
     *
     * @var Company
     */
    var $active_company;
    
    /**
     * Actions available through API
     *
     * @var array
     */
    var $api_actions = array('index', 'view', 'add', 'edit', 'delete');
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return CompanyProfileController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $company_id = $this->request->getId('company_id');
      if($company_id) {
        $this->active_company = Companies::findById($company_id);
      } // if
      
      if(instance_of($this->active_company, 'Company')) {
        $this->wireframe->page_actions = array();
        
        if(!$this->active_company->canView($this->logged_user)) {
          $this->httpError(HTTP_ERR_FORBIDDEN);
        } // if
        
        if($this->active_company->getIsArchived() && $this->logged_user->isPeopleManager()) {
          $this->wireframe->addBreadCrumb(lang('Archive'), assemble_url('people_archive'));
        } // if
        $this->wireframe->addBreadCrumb($this->active_company->getName(), $this->active_company->getViewUrl());
        
        // Collect company tabs
        $tabs = new NamedList();
        $tabs->add('overview', array(
          'text' => str_excerpt($this->active_company->getName(), 25),
          'url' => $this->active_company->getViewUrl()
        ));
        $tabs->add('people', array(
          'text' => lang('People'),
          'url' => $this->active_company->getViewUrl()
        ));
        $tabs->add('projects', array(
          'text' => lang('Projects'),
          'url' => $this->active_company->getViewUrl()
        ));
        
        event_trigger('on_company_tabs', array(&$tabs, &$this->logged_user, &$this->active_company));
        $this->smarty->assign(array(
          'company_tabs' => $tabs,
          'company_tab' => 'overview', 
        ));
      } else {
        $this->active_company = new Company();
      } // if
      
      $this->smarty->assign(array(
        'active_company' => $this->active_company,
      ));
    } // __construct
    
    /**
     * Show company details
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_company->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->request->isApiCall()) {
        $this->serveData($this->active_company, 'company', array(
      	  'describe_users' => true,
      	  'describe_logo' => true
      	));
      } else {
        if(User::canAdd($this->logged_user, $this->active_company)) {
          $this->wireframe->addPageAction(lang('New User'), $this->active_company->getAddUserUrl());
        } // if
        
        $this->smarty->assign(array(
      	  'users' => $this->active_company->getUsers($this->logged_user->visibleUserIds()), 
      	  'add_user_url' => User::canAdd($this->logged_user, $this->active_company) ? $this->active_company->getAddUserUrl() : false,
      	));
      } // if
    } // index
    
    /**
     * Create new company
     *
     * @param void
     * @return null
     */
    function add() {
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST, null, true, true);
      } // if
      
      if(!Company::canAdd($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $company = new Company();
      $options = array('office_address', 'office_phone', 'office_fax', 'office_homepage');
      
      $company_data = $this->request->post('company');
      $this->smarty->assign(array(
        'company_data' => $company_data,
        'active_company' => $company,
      ));
    	
      if ($this->request->isSubmitted()) {
      	db_begin_work();
      	
      	$company = new Company();
      	$company->setAttributes($company_data);
      	$company->setIsOwner(false);
      	
        $save = $company->save();
        
        if($save && !is_error($save)){
          foreach($options as $option) {
            $value = trim(array_var($company_data, $option));
            
            if($option == 'office_homepage' && $value && strpos($value, '://') === false) {
              $value = 'http://' . $value;
            } // if
            
            if($value != '') {
              CompanyConfigOptions::setValue($option, $value, $company);
            } // if
          } // foreach
          
          db_commit();
          
          if($this->request->getFormat() == FORMAT_HTML) {
          	flash_success("Company ':name' has been created", array('name' => $company->getName()));
          	$this->redirectToUrl($company->getViewUrl());
          } else {
          	$this->serveData($company, 'company');
          } // if
        } else {
          db_rollback();
          
        	if($this->request->getFormat() == FORMAT_HTML) {
        		$this->smarty->assign('errors', $save);
        	} else {
        		$this->serveData($save);
        	} // if
        } // if
      } // if
    } // add
    
    /**
     * Quick add company
     *
     * @param void
     * @return null
     */
    function quick_add() {
      if($this->request->isSubmitted() && $this->request->isAsyncCall()) {
        if(!Company::canAdd($this->logged_user)) {
          $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
        } // if
        
        $company = new Company();
        $company->setAttributes($this->request->post('company'));
        
        $save = $company->save();
        if($save && !is_error($save)) {
          print $company->getId();
          die();
        } else {
          $this->serveData($save);
        } // if
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // quick_add
    
    /**
     * Edit Company Info
     * 
     * @param void
     * @return null
     */
    function edit() {
      if($this->active_company->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $this->wireframe->print_button = false;
      
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      if(!$this->active_company->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->active_company->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $options = array('office_address', 'office_phone', 'office_fax', 'office_homepage');
      
      $company_data = $this->request->post('company');
      if(!is_array($company_data)) {
      	$company_data = array_merge(array(      	
      	  'name' => $this->active_company->getName(),
      	), CompanyConfigOptions::getValues($options, $this->active_company));
      } // if
      $this->smarty->assign('company_data',  $company_data);
      
      if($this->request->isSubmitted()) {
      	db_begin_work();
      	
      	$old_name = $this->active_company->getName();
      	
      	$this->active_company->setAttributes($company_data);
      	$save = $this->active_company->save();
      	
      	if($save && !is_error($save)){
      	  foreach($options as $option) {
            $value = trim(array_var($company_data, $option));
            
            if($option == 'office_homepage' && $value && strpos($value, '://') === false) {
              $value = 'http://' . $value;
            } // if
            
            if($value == '') {
              CompanyConfigOptions::removeValue($option, $this->active_company);
            } else {
              CompanyConfigOptions::setValue($option, $value, $this->active_company);
            } // if
          } // foreach
          
          if($this->active_company->getIsOwner()) {
            cache_remove('owner_company'); // force cache refresh on next load
          } // if
          
      	  db_commit();
      	  
        	if($this->request->getFormat() == FORMAT_HTML) {
        	  flash_success("Company :name has been updated", array('name' => $old_name));
        	  $this->redirectToUrl($this->active_company->getViewUrl());
        	} else {
        	  $this->serveData($this->active_company, 'company');
        	} // if
      	} else {
      		db_rollback();
      		
      		if($this->request->getFormat() == FORMAT_HTML) {
      			$this->smarty->assign('errors', $save);
      		} else {
      			$this->serveData($save);
      		} // if
      	} // if
      } // if
    } // edit
    
    /**
     * Edit user avatar
     *
     * @param void
     * @return null
     */
    function edit_logo() {
      if($this->active_company->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $this->wireframe->print_button = false;
      
      if(!$this->active_company->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if(!extension_loaded('gd')) {
        $message = lang('<b>GD not Installed</b> - GD extension is not installed on your system. You will not be able to upload project icons, company logos and avatars!');
        if ($this->request->isAsyncCall()) {
          echo "<p>$message</p>";
          die();
        } else {
          $this->wireframe->addPageMessage($message, PAGE_MESSAGE_ERROR);  
        } // if
      } // if
      
    	if($this->request->isSubmitted()) {
    	  if(!isset($_FILES['logo']) || !is_uploaded_file($_FILES['logo']['tmp_name'])) {
    	    $message = lang('Please select an image');
    	    if ($this->request->isAsyncCall()) {
    	      $this->httpError(HTTP_ERR_OPERATION_FAILED, $message);
    	    } else {
      	    flash_error($message);
      	    $this->redirectToUrl($this->active_company->getEditLogoUrl());
    	    } // if
    	  } // if
    	  
    		if(can_resize_images()) {
    		  $errors = new ValidationErrors();
    		  do {
    		    $from = WORK_PATH.'/'.make_password(10).'_'.$_FILES['logo']['name'];
    		  } while (is_file($from));
    		  
    		  if (!move_uploaded_file($_FILES['logo']['tmp_name'], $from)) {
            $errors->addError(lang("Can't copy image to work path"), 'icon');
    		  } else {
      		  $to = $this->active_company->getLogoPath();
      		  $small = scale_image($from, $to, 16, 16, IMAGETYPE_JPEG,100);
      		  
      		  $to = $this->active_company->getLogoPath(true);
      		  $large = scale_image($from, $to, 40, 40, IMAGETYPE_JPEG,100);
      		  
    		    @unlink($from);
    		  } // if
    		  
    		  if(!$small || !$large || empty($from)) {
    		  	$errors->addError('Failed to create avatar', 'logo');
    		  } // if
    		  
    		  if($errors->hasErrors()) {
      	    $this->smarty->assign('errors', $errors);
      	    $this->render();
      	  } // if
      	  
      	  cache_remove('project_icons');
    		} // if
    	} // if
    } // edit_avatar
    
    /**
     * Delete Company Logo
     *
     * @param void
     * @return null
     */
    function delete_logo() {
      if($this->active_company->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_company->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
    	if($this->request->isSubmitted()) {
    	  unlink($this->active_company->getLogoPath());
    	  unlink($this->active_company->getLogoPath(true));
    	  
    	  cache_remove('project_icons');
    	  
    	  if ($this->request->isAsyncCall()) {
    	    $this->serveData(array(
    	     'message' => lang('Icon successfully removed'),
           'icon' => $this->active_company->getLogoUrl(true)
    	    ), 'delete', null, FORMAT_JSON);
    	  } else {
    	     $this->redirectToUrl($this->active_company->getEditLogoUrl());
    	  }
    	} else {
    	  $this->httpError(HTTP_ERR_BAD_REQUEST);
    	} // if
    } // delete-logo
    
    /**
     * Archive / unarchive this company
     *
     * @param void
     * @return null
     */
    function archive() {
      if($this->active_company->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_company->canArchive($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null);
      } // if
      
      if($this->request->isSubmitted()) {
        $new_value = (boolean) $this->request->get('set_value');
        if($new_value) {
          $success_message = lang('":name" has been successfully archived', array('name' => $this->active_company->getName()));
          $error_message = lang('Failed to archive ":name"', array('name' => $this->active_company->getName()));
        } else {
          $success_message = lang('":name" has been successfully moved from archive to the list of active companies', array('name' => $this->active_company->getName()));
          $error_message = lang('Failed to move ":name" from the archive to the list of active companies', array('name' => $this->active_company->getName()));
        } // if
        
        $this->active_company->setIsArchived($new_value);
        $save = $this->active_company->save();
        
        if($save && !is_error($save)) {
          flash_success($success_message);
        } else {
          flash_error($error_message);
        } // if
        
        $this->redirectToUrl($this->active_company->getViewUrl());
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // archive
    
    /**
     * Delete Company
     *
     * @param void
     * @return null
     */
    function delete() {
      if($this->active_company->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_company->canDelete($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      if($this->active_company->isNew() || $this->active_company->isOwner()) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if($this->request->isSubmitted()) {
        $old_name = $this->active_company->getName();
        
      	$delete = $this->active_company->delete();
      	
      	if($delete && !is_error($delete)) {
      	  if($this->request->isApiCall()) {
      	    $this->httpOk();
      	  } else {
      	    flash_success("Company ':name' has been deleted", array('name' => $old_name));
      	  	$this->redirectTo('people');
      	  } // if
      	} else {
      		if($this->request->isApiCall()) {
      		  $this->httpError(HTTP_ERR_OPERATION_FAILED, null, true, $this->request->isApiCall());
      		} else {
      			flash_error("Failed to delete :name", array('name' => $old_name));
            $this->redirectTo('people');
      		} // if
      	} // if
      } else {
      	$this->httpError(HTTP_ERR_BAD_REQUEST, null, true, $this->request->isApiCall());
      } // if
    } // delete
  
  }

?>