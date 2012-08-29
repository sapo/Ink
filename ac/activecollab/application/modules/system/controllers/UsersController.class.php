<?php

  // Use company profile module
  use_controller('companies', SYSTEM_MODULE);

  /**
   * User profile controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class UsersController extends CompaniesController {
    
    /**
     * Name of this controller
     *
     * @var string
     */
    var $controller_name = 'users';
    
    /**
     * Name of the parent module
     *
     * @var mixed
     */
    var $active_module = SYSTEM_MODULE;
      
    /**
     * Selected use
     *
     * @var User
     */
    var $active_user;
    
    /**
     * Array of controller actions that can be accessed through API
     *
     * @var array
     */
    var $api_actions = array('view', 'add', 'edit', 'delete');
       
    /**
     * Construct Profile Controller
     *
     * @param void
     * @return null
     */
    function __construct($request){
    	parent::__construct($request);
    	
    	$user_id = $this->request->get('user_id');
    	if($user_id) {
    		$this->active_user = Users::findById($user_id);
    	} // if
    	
    	if(instance_of($this->active_user,'User')) {
    	  if(!in_array($this->active_user->getId(), $this->logged_user->visibleUserIds())) {
      	  $this->httpError(HTTP_ERR_NOT_FOUND);
      	} // if
      	$this->wireframe->addBreadCrumb($this->active_user->getName(), $this->active_user->getViewUrl());
      	if ($this->active_user->getId() == $this->logged_user->getId()) {
      	  $this->wireframe->current_menu_item = 'profile';
      	} // if
    	} else {
    		$this->active_user = new User();
    	} // if
    	
    	$this->smarty->assign('active_user', $this->active_user);
    } // __construct
    
    /**
     * Show user profile page
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if($this->request->isApiCall()) {
      	$this->serveData($this->active_user, 'user', array(
      	  'describe_company' => true,
      	  'describe_avatar' => true,
      	));
      } // if
    } // view
    
    /**
     * Create new user
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      if(!User::canAdd($this->logged_user, $this->active_company)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $user_data = $this->request->post('user');
      if(!is_array($user_data)) {
        $user_data = array(
          'role_id' => ConfigOptions::getValue('default_role'),
          'auto_assign' => false,
        );
      } // if
      
      $this->smarty->assign(array(
        'user_data' => $user_data,
      ));
      
      if($this->request->isSubmitted()) {
      	db_begin_work();
      	
      	// Validate password
      	if($this->request->isApiCall() || array_var($user_data, 'specify_password')) {
      	  $errors = new ValidationErrors();
      	  
      	  $password = array_var($user_data, 'password');
      	  $password_a = array_var($user_data, 'password_a');
      	  
      	  if(strlen(trim($password)) < 3) {
      	    $errors->addError(lang('3 Letters or Longer'), 'password');
      	  } else {
      	    if($password != $password_a) {
      	      $errors->addError(lang('Passwords Mismatch'), 'password_a');
      	    } // if
      	  } // if
      	  
      	  if($errors->hasErrors()) {
      	    if($this->request->getFormat() == FORMAT_HTML) {
      	      $this->smarty->assign('errors', $errors);
      	      $this->render();
      	    } else {
      	      $this->serveData($errors);
      	    } // if
      	  } // if
      	} else {
      	  $password = make_password(11);
      	} // if
      	
      	$this->active_user = new User();
      	$this->active_user->setAttributes($user_data);
      	$this->active_user->setPassword($password);
      	$this->active_user->setCompanyId($this->active_company->getId());
      	
      	if($this->logged_user->isPeopleManager()) {
      	  $this->active_user->setAutoAssignData(
      	    (boolean) array_var($user_data, 'auto_assign'),
      	    (integer) array_var($user_data, 'auto_assign_role_id'),
      	    array_var($user_data, 'auto_assign_permissions')
      	  );
      	} else {
      	  $this->active_user->setRoleId(ConfigOptions::getValue('default_role'));
      	} // if
      	
      	$save = $this->active_user->save();
      	if($save && !is_error($save)) {
      	  $welcome_message_sent = false;
      	  
      	  if(array_var($user_data, 'send_welcome_message')) {
      	    $welcome_message = trim(array_var($user_data, 'welcome_message'));
      	    if($welcome_message) {
      	      UserConfigOptions::setValue('welcome_message', $welcome_message, $this->active_user);
      	    } // if
      	    
      	    $welcome_message_sent = ApplicationMailer::send(array($this->active_user), 'system/new_user', array(
              'created_by_id'   => $this->logged_user->getId(),
              'created_by_name' => $this->logged_user->getDisplayName(),
              'created_by_url'  => $this->logged_user->getViewUrl(),
              'email'           => $this->active_user->getEmail(),
              'password'        => $password,
              'login_url'       => assemble_url('login'),
              'welcome_body'    => $welcome_message ? nl2br(clean($welcome_message)) : '',
      	    ));
      	  } // if
      	  
      	  $title = trim(array_var($user_data, 'title'));
      	  if($title) {
      	    UserConfigOptions::setValue('title', $title, $this->active_user);
      	  } // if
      	  
      		db_commit();
      		
      		if($this->request->isApiCall()) {
      		  $this->serveData($this->active_user, 'user');
      		} else {
      		  if($welcome_message_sent) {
      		    flash_success('New user account has been created. Login information has been sent to :email', array('email' => $this->active_user->getEmail()));
      		  } else {
      		    flash_success('New user account has been created');
      		  } // if
      		  
      			$this->redirectToUrl($this->active_user->getViewUrl());
      		} // if
      	} else {
      		db_rollback();
      		
      		if($this->request->isApiCall()) {
      		  $this->serveData($save);
      		} else {
      		  $this->smarty->assign('errors', $save);
      		} // if
      	} // if
      } // if
    } // add
    
    /**
     * API method
     *
     * @param void
     * @return null
     */
    function edit() {
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->request->isApiCall() && $this->request->isSubmitted()) {
        if(!$this->active_user->canEdit($this->logged_user)) {
          $this->httpError(HTTP_ERR_FORBIDDEN);
        } // if
        
        $config_options = array('title', 'phone_work', 'phone_mobile', 'im_type', 'im_value', 'format_date', 'format_time', 'time_timezone', 'time_dst', 'time_first_week_day', 'visual_editor', 'theme');
        if(LOCALIZATION_ENABLED) {
          $config_options[] = 'language';
        } // if
        
        $user_data = $this->request->post('user');
        
        // Unset fields user cannot change if he is not people manager
        if(!$this->logged_user->isPeopleManager()) {
          if(isset($user_data['company_id'])) {
            unset($user_data['company_id']);
          } // if
          if(isset($user_data['role_id'])) {
            unset($user_data['role_id']);
          } // if
        } // if
        
        // Set attributes
        $this->active_user->setAttributes($user_data);
        $save = $this->active_user->save();
        
        if($save && !is_error($save)) {
          
          // Set config options
          foreach($config_options as $config_option) {
      	    if($config_option == 'time_dst' || $config_option == 'visual_editor') {
      	      $value = (boolean) array_var($user_data, $config_option);
      	    } elseif($config_option == 'time_timezone' || $config_option == 'time_first_week_day ') {
      	      $value = (integer) array_var($user_data, $config_option);
      	    } else {
      	      $value = trim(array_var($user_data, $config_option));
      	    } // if
      	    
      	    if($value === '') {
      	      UserConfigOptions::removeValue($config_option, $this->active_user);
      	    } else {
      	      UserConfigOptions::setValue($config_option, $value, $this->active_user);
      	    } // if
      	  } // foreach
      	  
      	  $this->serveData($this->active_user, 'user', array(
        	  'describe_company' => true,
        	  'describe_avatar' => true,
        	));
        } else {
          $this->serveData($save);
        } // if
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // edit
    
    /**
     * Edit Profile
     *
     * @param void
     * @return null
     */
    function edit_profile() {
      $this->wireframe->print_button = false;
      
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_user->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $config_options = array('title', 'phone_work', 'phone_mobile', 'im_type', 'im_value');
      
      $user_data = $this->request->post('user');
      if(!is_array($user_data)) {
        $user_data = array_merge(array(
      	  'first_name' => $this->active_user->getFirstName(),
          'last_name'  => $this->active_user->getLastName(),
      	  'email'      => $this->active_user->getEmail(),
      	), UserConfigOptions::getValues($config_options, $this->active_user));
      } // if
      
      $this->smarty->assign('user_data', $user_data);

      if($this->request->isSubmitted()) {
      	db_begin_work();
      	
      	$display = $this->active_user->getDisplayName();
      	$user_data['role_id'] = $this->active_user->getRoleId(); // role cannot be changed
      	
      	$this->active_user->setAttributes($user_data);
      	
      	$save = $this->active_user->save();
      	if($save && !is_error($save)) {
      	  foreach($config_options as $config_option) {
      	    $value = trim(array_var($user_data, $config_option));
      	    
      	    if($value === '') {
      	      UserConfigOptions::removeValue($config_option, $this->active_user);
      	    } else {
      	      UserConfigOptions::setValue($config_option, $value, $this->active_user);
      	    } // if
      	  } // foreach
      		db_commit();
      		
      		if($this->request->isApiCall()) {
      		  $this->serveData($this->active_user, 'user');
      		} else {
      		  flash_success(":display's profile has been updated", array('display' => $display));
      			$this->redirectToUrl($this->active_user->getViewUrl());
      		} // if
      	} else {
      		db_rollback();
      		
      		if($this->request->isApiCall()) {
      		  $this->serveData($save);
      		} else {
      		  $this->smarty->assign('errors', $save);
      		} // if
      	} // if
      } else {
        if($this->request->isApiCall()) {
          $this->httpError(HTTP_ERR_BAD_REQUEST, null, true, true);
        } // if
      } // if
    } // edit_profile
    
    /**
     * Show and process edit settings page
     *
     * @param void
     * @return null
     */
    function edit_settings() {
      $this->wireframe->print_button = false;
      
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_user->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $config_options = array('format_date', 'format_time', 'time_timezone', 'time_dst', 'time_first_week_day', 'visual_editor', 'theme', 'default_assignments_filter');
      if(LOCALIZATION_ENABLED) {
        $config_options[] = 'language';
      } // if
      
      $user_data = $this->request->post('user');
      if(!is_array($user_data)) {
        $user_data = array_merge(array(
      	  'auto_assign'             => $this->active_user->getAutoAssign(),
      	  'auto_assign_role_id'     => $this->active_user->getAutoAssignRoleId(),
      	  'auto_assign_permissions' => $this->active_user->getAutoAssignPermissions(),
      	), UserConfigOptions::getValues($config_options, $this->active_user));
      	
      	if(LOCALIZATION_ENABLED) {
        	if(!UserConfigOptions::hasValue('language', $this->active_user)) {
        	  $user_data['language'] = null;
        	} // if
      	} // if
      	
      	if(!UserConfigOptions::hasValue('time_dst', $this->active_user)) {
      	  $user_data['time_dst'] = null;
      	} // if
      	
      	if(!UserConfigOptions::hasValue('format_date', $this->active_user)) {
      	  $user_data['format_date'] = null;
      	} // if
      	
      	if(!UserConfigOptions::hasValue('format_time', $this->active_user)) {
      	  $user_data['format_time'] = null;
      	} // if
      	
      	if(!UserConfigOptions::hasValue('theme', $this->active_user)) {
      	  $user_data['theme'] = null;
      	} // if
      	
      	if(!UserConfigOptions::hasValue('default_assignments_filter', $this->active_user)) {
      	  $user_data['default_assignments_filter'] = null;
      	} // if
      } // if
      
      $this->smarty->assign(array(
        'user_data' => $user_data,
        'default_dst_value' => (boolean) ConfigOptions::getValue('time_dst'),
      ));

      if($this->request->isSubmitted()) {
      	db_begin_work();
      	
      	$display = $this->active_user->getDisplayName();
      	$user_data['role_id'] = $this->active_user->getRoleId(); // role cannot be changed
      	
      	$this->active_user->setAttributes($user_data);
      	
      	if($this->active_user->canChangeRole($this->logged_user)) {
      	  $this->active_user->setAutoAssignData(
      	    (boolean) array_var($user_data, 'auto_assign'),
      	    (integer) array_var($user_data, 'auto_assign_role_id'),
      	    array_var($user_data, 'auto_assign_permissions')
      	  );
      	} // if
      	
      	$save = $this->active_user->save();
      	if($save && !is_error($save)) {
      	  foreach($config_options as $config_option) {
      	    if($config_option == 'time_dst') {
      	      $value = array_var($user_data, $config_option) === '' ? '' : (boolean) array_var($user_data, $config_option);
      	    } elseif($config_option == 'visual_editor') {
      	      $value = (boolean) array_var($user_data, $config_option);
      	    } elseif($config_option == 'time_timezone' || $config_option == 'time_first_week_day' || $config_option == 'default_assignments_filter') {  
      	      $value = (integer) array_var($user_data, $config_option);
      	    } else {
      	      $value = trim(array_var($user_data, $config_option));
      	    } // if
      	    
      	    if($config_option == 'default_assignments_filter' && $value == 0) {
      	      $value = ''; // Reset to default
      	    } // if
      	    
      	    if($value === '') {
      	      UserConfigOptions::removeValue($config_option, $this->active_user);
      	    } else {
      	      
      	      $display_localized_message = false;
      	      if (LOCALIZATION_ENABLED && $this->logged_user->getId() == $this->active_user->getId() && $user_data['language'] !== UserConfigOptions::getValue('language', $this->active_user)) { 
      	        $display_localized_message = true;
      	      } // if
      	      
      	      UserConfigOptions::setValue($config_option, $value, $this->active_user);
      	    } // if
      	  } // foreach
      	  
      		db_commit();

    		  flash_success(lang(":display's settings have been updated", array('display' => $this->active_user->getDisplayName()), true, $display_localized_message === true ? $this->active_user->getLanguage() : null), null, true);
    			$this->redirectToUrl($this->active_user->getViewUrl());
      	} else {
      		db_rollback();
      		$this->smarty->assign('errors', $save);
      	} // if
      } // if
    } // edit_settings
    
    /**
     * Update user's company and role information
     *
     * @param void
     * @return null
     */
    function edit_company_and_role() {
      $this->wireframe->print_button = false;
      
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_user->canChangeRole($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $user_data = $this->request->post('user');
      if(!is_array($user_data)) {
        $user_data = array(
          'company_id' => $this->active_user->getCompanyId(),
          'role_id' => $this->active_user->getRoleId(),
        );
      } // if
      $this->smarty->assign('user_data', $user_data);
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_user->setAttributes($user_data);
        $save = $this->active_user->save();
        
        if($save && !is_error($save)) {
          db_commit();
      		
    		  flash_success(":display's company and role information has been updated", array('display' => $this->active_user->getDisplayName()));
    			$this->redirectToUrl($this->active_user->getViewUrl());
        } else {
          db_rollback();
      		$this->smarty->assign('errors', $save);
        } // if
      } // if
    } // edit_company_and_role
    
    /**
     * Edit Profile Password
     *
     * @param void
     * @return null
     */
    function edit_password() {
      $this->wireframe->print_button = false;
      
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_user->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
    	$user_data = $this->request->post('user');
      $this->smarty->assign('user_data', $user_data);
      
    	if($this->request->isSubmitted()) {
      	$errors = new ValidationErrors();
      	
      	$password = array_var($user_data, 'password');
      	$repeat_password = array_var($user_data, 'repeat_password');
      	
      	if(empty($password)) {
      		$errors->addError(lang('Password value is required'), 'password');
      	} // if
      	
      	if(empty($repeat_password)) {
      		$errors->addError(lang('Repeat Password value is required'), 'repeat_password');
      	} // if
      	
      	if(!$errors->hasErrors() && ($password !== $repeat_password)) {
      	  $errors->addError(lang('Inserted values does not match'));
      	} // if
      	
      	if($errors->hasErrors()) {
      	  $this->smarty->assign('errors', $errors);
      	  $this->render();
      	} // if
      	
    	  db_begin_work();
    	  
    		$this->active_user->setPassword($user_data['password']);
    		$save = $this->active_user->save();
    		
    		if($save && !is_error($save)) {
    			db_commit();
    			
    			if($this->request->getFormat() == FORMAT_HTML) {
    			  flash_success('Password has been updated');
    			  $this->redirectToUrl($this->active_user->getViewUrl());	
    			} else {
    				$this->serveData($this->active_user, 'user');
    			} // if
    		} else {
    		  db_rollback();
    		  
      		if($this->request->getFormat() == FORMAT_HTML) {
      			$this->smarty->assign('errors', $errors);
      		} else {
      			$this->serveData($errors);
      		} // if
    		} // if
    	} // if
    } // edit_password
    
    /**
     * Edit Profile Avatar
     *
     * @param void
     * @return null
     */
    function edit_avatar() {
      $this->wireframe->print_button = false;
      
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_user->canEdit($this->logged_user)) {
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
    	  if(!isset($_FILES['avatar']) || !is_uploaded_file($_FILES['avatar']['tmp_name'])) {
    	    $message = lang('Please select an image');
    	    if ($this->request->isAsyncCall()) {
    	      $this->httpError(HTTP_ERR_OPERATION_FAILED, $message);
    	    } else {
      	    flash_error($message);
      	    $this->redirectToUrl($this->active_user->getEditAvatarUrl());
    	    } // if
    	  } // if
    	  
    		if(can_resize_images()) {
    		  $errors = new ValidationErrors();
    		  do {
    		    $from = WORK_PATH.'/'.make_password(10).'_'.$_FILES['avatar']['name'];
    		  } while (is_file($from));
    		  
    		  if(move_uploaded_file($_FILES['avatar']['tmp_name'], $from)) {
            $to = $this->active_user->getAvatarPath();
      		  $small = scale_image($from, $to, 16, 16, IMAGETYPE_JPEG,100);
      		  
      		  $to = $this->active_user->getAvatarPath(true);
      		  $large = scale_image($from, $to, 40, 40, IMAGETYPE_JPEG,100);
      		  
      	    @unlink($from);
    		  } else {
      	    $errors->addError(lang("Can't copy image to work path"), 'icon');
    		  } // if
    		  
    		  if(empty($from)) {
    		  	$errors->addError(lang('Select avatar'), 'avatar');
    		  } // if
    		  
    		  if($errors->hasErrors()) {
      	    $this->smarty->assign('errors', $errors);
      	    $this->render();
      	  } // if
    		} // if
    	} // if
    } // edit_avatar
    
    /**
     * Delete user
     *
     * @param void
     * @return null
     */
    function delete() {
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if(!$this->active_user->canDelete($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      if($this->request->isSubmitted()) {
        $delete = $this->active_user->delete();
        if($delete && !is_error($delete)) {
          if($this->request->isApiCall()) {
            $this->httpOk();
          } else {
      	    flash_success('User ":name" has been deleted', array('name' => $this->active_user->getDisplayName()));
      	    $this->redirectToUrl($this->active_company->getViewUrl());
          } // if
        } else {
          if($this->request->isApiCall()) {
            $this->serveData($delete);
          } else {
            flash_error('Failed to delete ":name"', array('name' => $this->active_user->getDisplayName()));
            $this->redirectToUrl($this->active_company->getViewUrl());
          } // if
        } // if
    	} else {
    	  $this->httpError(HTTP_ERR_BAD_REQUEST, null, true, $this->request->isApiCall());
    	} // if
    } // delete
    
    /**
     * Delete Profile Avatar
     *
     * @param void
     * @return null
     */
    function delete_avatar() {
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_user->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
    	if($this->request->isSubmitted()) {
    	  unlink($this->active_user->getAvatarPath());
    	  unlink($this->active_user->getAvatarPath(true));
    	  
    	  
    	  if ($this->request->isAsyncCall()) {
    	    $this->serveData(array(
    	     'message' => lang('Icon successfully removed'),
           'icon' => $this->active_user->getAvatarUrl(true)
    	    ), 'delete', null, FORMAT_JSON);
    	  } else {
    	     $this->redirectToUrl($this->active_user->getEditAvatarUrl());
    	  }
    	} else {
    	  $this->httpError(HTTP_ERR_BAD_REQUEST);
    	} // if
    } // delete_avatar
    
    /**
     * Show API settings URL
     *
     * @param void
     * @return null
     */
    function api() {
      if(!$this->active_user->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->wireframe->print_button = false;
      
      $this->smarty->assign('api_url', ROOT_URL . '/api.php');
    } // api
    
    /**
     * Reset API key
     *
     * @param void
     * @return null
     */
    function api_reset_key() {
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_user->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isSubmitted()) {
        $this->active_user->setToken(make_string(40));
        $save = $this->active_user->save();
        if($save && !is_error($save)) {
          flash_success('API key updated');
        } else {
          flash_error('Failed to update API key. Try again in a few minutes');
        } // if
        
        $this->redirectToUrl($this->active_user->getApiSettingsUrl());
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // api_reset_key
    
    /**
     * Recent activities for selected user
     *
     * @param void
     * @return null
     */
    function recent_activities() {
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
    	if(!$this->active_user->canViewActivities($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
      
      $per_page = 15;
    	
    	list($recent_activities, $pagination) = ActivityLogs::paginateActivitiesByUser($this->active_user, $page, $per_page);
			
    	$this->smarty->assign(array(
    		'recent_activities' => group_by_date($recent_activities),
    		'pagination' => $pagination
    	));
    } // recent_activities
    
    /**
     * Show and process add to projects page
     *
     * @param void
     * @return null
     */
    function add_to_projects() {
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
    	if(!$this->logged_user->isProjectManager()) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $add_to_projects_data = $this->request->post('add_to_projects');
      $this->smarty->assign(array(
        'add_to_projects_data' => $add_to_projects_data,
        'exclude_project_ids' => Projects::findProjectIdsByUser($this->active_user),
      ));
      
      if($this->request->isSubmitted()) {
        $errors = new ValidationErrors();
        
        $projects = null;
        if(is_foreachable($add_to_projects_data['projects'])) {
          $projects = Projects::findByIds($add_to_projects_data['projects']);
        } // if
        
        if(!is_foreachable($projects)) {
          $errors->addError(lang('Please select projects'), 'projects');
        } // if
        
        if($add_to_projects_data['role_id']) {
          $role = Roles::findById($add_to_projects_data['role_id']);
          $permissions = null;
          
          if(!instance_of($role, 'Role') || !($role->getType() == ROLE_TYPE_PROJECT)) {
            $errors->addError(lang('Invalid project role'), 'project_permissions');
          } // if
        } else {
          $role = null;
          $permissions = array_var($add_to_projects_data, 'permissions');
        } // if
        
        if($errors->hasErrors()) {
          $this->smarty->assign('errors', $errors);
        } else {
          $added = 0;
          foreach($projects as $project) {
            $add = $project->addUser($this->active_user, $role, $permissions);
            if($add && !is_error($add)) {
              $added++;
            } // if
          } // foreach
          
          if($added == 1) {
            flash_success(':name has been added to 1 project', array('name' => $this->active_user->getDisplayName()));
          } else {
            flash_success(':name has been added to :count projects', array('name' => $this->active_user->getDisplayName(), 'count' => $added));
          } // if
          
          $this->redirectToUrl($this->active_user->getViewUrl());
        } // if
      } // if
    } // add_to_projects
    
    /**
     * Send welcome message
     *
     * @param void
     * @return null
     */
    function send_welcome_message() {
      if($this->active_user->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_user->canSendWelcomeMessage($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->skip_layout = $this->request->isAsyncCall();
      
      $welcome_message_data = $this->request->post('welcome_message');
      if(!is_array($welcome_message_data)) {
        $welcome_message_data = array(
          'message' => UserConfigOptions::getValue('welcome_message', $this->active_user),
        );
      } // if
      $this->smarty->assign('welcome_message_data', $welcome_message_data);
      
      if($this->request->isSubmitted()) {
        $welcome_message = trim(array_var($welcome_message_data, 'message'));
        if($welcome_message) {
          UserConfigOptions::setValue('welcome_message', $welcome_message, $this->active_user);
        } else {
          UserConfigOptions::removeValue('welcome_message', $this->active_user);
        } // if
        
        $password = make_password(11);
        $this->active_user->setPassword($password);
        
        $save = $this->active_user->save();
        if($save && !is_error($save)) {
          $welcome_message_sent = ApplicationMailer::send(array($this->active_user), 'system/new_user', array(
            'created_by_id'   => $this->logged_user->getId(),
            'created_by_name' => $this->logged_user->getDisplayName(),
            'created_by_url'  => $this->logged_user->getViewUrl(),
            'email'           => $this->active_user->getEmail(),
            'password'        => $password,
            'login_url'       => assemble_url('login'),
            'welcome_body'    => $welcome_message ? nl2br(clean($welcome_message)) : '',
    	    ));
    	    
    	    if($welcome_message_sent) {
    	      $message = lang('Welcome message has been sent to :name', array('name' => $this->active_user->getDisplayName()));
    	    } else {
    	      $message = lang('Failed to send welcome message to :name. Please try again later', array('name' => $this->active_user->getDisplayName()));
    	    } // if
    	    
    	    if($this->request->isAsyncCall()) {
            die($message);
          } else {
            flash_success($message);
            $this->redirectToUrl($this->active_user->getViewUrl());
          } // if
        } else {
          if($this->request->isAsyncCall()) {
            $this->httpError(HTTP_ERR_OPERATION_FAILED);
          } else {
            flash_error($message);
            $this->redirectToUrl($this->active_user->getViewUrl());
          } // if
        } // if
      } // if
    } // send_welcome_message
    
  }
  
?>