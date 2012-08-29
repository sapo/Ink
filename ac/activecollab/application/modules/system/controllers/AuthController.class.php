<?php

  /**
   * Authentication controller
   * 
   * This controller will handle user login, logout, lost password and similar 
   * actions. It does not require login!
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class AuthController extends ApplicationController {
    
    /**
     * Login is not required here...
     * 
     * @var boolean
     */
    var $login_required = false;
    
    /**
     * Allow people to log in when system is in maintenance mode
     *
     * @var boolean
     */
    var $restrict_access_in_maintenance_mode = false;
    
    /**
     * Is visitor using mobile device
     */
    var $is_mobile_device = false;
    
    /**
     * Construct auth controller
     *
     * @param Request $request
     * @return AuthController
     */
    function __construct($request) {
      parent::__construct($request);
      // if user is using mobile device, redirect it to mobile access login page
      if (module_loaded('mobile_access') && is_mobile_device(USER_AGENT)) {
        if ($request->matched_route != 'logout') {
          $this->redirectTo('mobile_access_login');
        } else {
          $this->redirectTo('mobile_access_logout');
        } // if
      } // if
      $this->setLayout('application');
    } // __construct
    
    /**
     * Log user in
     *
     * @param void
     * @return null
     */
    function login() {
      $redirect_to = null; // Get page user wanted to visit based on GET params
      if($this->request->get('re_route')) {
        $params = array();
        foreach($this->request->url_params as $k => $v) {
          if(($k != 're_route') && str_starts_with($k, 're_')) {
            $params[substr($k, 3)] = $v;
          } // if
        } // if
        $redirect_to = assemble_url($this->request->get('re_route'), $params);
      } else {
        $redirect_to = assemble_url('dashboard');
      } // if
      
      // If user is already logged in redirect him to page he wanted to visit
      if(instance_of($this->logged_user, 'User')) {
        flash_error('You are already logged in as :display. Please logout before you can login on another account', array('display' => $this->logged_user->getDisplayName()));
        $this->redirectToUrl($redirect_to);
      } // if
      
      $login_data = $this->request->post('login');
      $this->smarty->assign(array(
        'login_data' => $login_data,
        'auto_focus' => true
      ));
            
      if($this->request->isSubmitted()) {
        $errors = new ValidationErrors();
        
        $email = trim(array_var($login_data, 'email'));
        $password = array_var($login_data, 'password');
        $remember = (boolean) array_var($login_data, 'remember');
        
        if($email == '') {
          $errors->addError(lang('Email address is required'), 'email');
        } // if
        
        if(trim($password) == '') {
          $errors->addError(lang('Password is required'), 'password');
        } // if
        
        if($errors->hasErrors()) {
          $this->smarty->assign('auto_focus', false);
          $this->smarty->assign('errors', $errors);
          $this->render();
        } // if
        
        $user =& $this->authentication->provider->authenticate(array(
          'email' => $email,
          'password' => $password,
          'remember' => $remember,
        ));
        
        if(!$user || is_error($user)) {
          $errors->addError(lang('Failed to log you in with data you provided. Please try again'), 'login');
          $this->smarty->assign('errors', $errors);
          $this->render();
        } // if
        
        flash_success(lang('Welcome back :display!', array('display' => $user->getDisplayName()), true, $user->getLanguage()), null, true);
        $this->redirectToUrl($redirect_to);
      } // if
    } // login
    
    /**
     * Log user out
     *
     * @param void
     * @return null
     */
    function logout() {
      $this->setLayout('application');
      
      if(!instance_of($this->logged_user, 'User')) {
        $this->redirectTo('login');
      } // if
      
      // Logout and redirect to login screen...
      $this->authentication->provider->logUserOut();
      
      $option = ConfigOptions::findByName('on_logout_url');
      $on_logout_url = $option->getValue();
      $this->redirectToUrl(is_valid_url($on_logout_url) ? $on_logout_url : assemble_url('login'));
    } // logout
    
    /**
     * Render and process forgot password form
     *
     * @param void
     * @return null
     */
    function forgot_password() {
      $forgot_password_data = $this->request->post('forgot_password');
      $this->smarty->assign('forgot_password_data', $forgot_password_data);
      
      if($this->request->isSubmitted()) {
        $errors = new ValidationErrors();
        
        $email = trim(array_var($forgot_password_data, 'email'));
        if($email == '') {
          $errors->addError(lang('Email address is required'), 'email');
        } else {
          if(is_valid_email($email)) {
            $user = Users::findByEmail($email);
            if(instance_of($user, 'User')) {
              $user->setPasswordResetKey(make_string(13));
              $user->setPasswordResetOn(new DateTimeValue());
              
              $save = $user->save();
              if(!$save || is_error($save)) {
                $errors->addError('Failed to update your user password with reset password data');
              } // of
            } else {
              $errors->addError(lang('There is no user account that matches the e-mail address you entered'), 'email');
            } // if
          } else {
            $errors->addError(lang('Invalid email address'), 'email');
          } // if
        } // if
        
        if($errors->hasErrors()) {
          $this->smarty->assign('errors', $errors);
          $this->render();
        } // if
        
        $sent = ApplicationMailer::send(array($user), 'system/forgot_password', array(
          'reset_url' => $user->getResetPasswordUrl(),
        ));
        
        $this->smarty->assign(array(
          'success_message' => lang('We emailed reset password instructions at :email', array('email' => $user->getEmail())),
          'forgot_password_data' => null,
        ));
      } // if
    } // forgot_password
    
    /**
     * Reset users password
     *
     * @param void
     * @return null
     */
    function reset_password() {
      $user_id = $this->request->getId('user_id');
      $code = trim($this->request->get('code'));
      
      if(empty($user_id) || empty($code)) {
        $this->httpError(HTTP_ERR_INVALID_PROPERTIES);
      } // if
      
      $user = null;
    	if($user_id) {
    	  $user = Users::findById($user_id);
    	} // if
    	
    	// Valid user and key
    	if(!instance_of($user, 'User')) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if($user->getPasswordResetKey() != $code) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	// Not expired
    	$reset_on = $user->getPasswordResetOn();
    	if(instance_of($reset_on, 'DateTimeValue')) {
    	  if(($reset_on->getTimestamp() + 172800) < time()) {
    	    $this->httpError(HTTP_ERR_NOT_FOUND);
    	  } // if
    	} else {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	$reset_data = $this->request->post('reset');
    	$this->smarty->assign(array(
    	  'reset_data' => $reset_data,
    	  'user' => $user,
    	));
    	
    	if($this->request->isSubmitted()) {
    	  $password = array_var($reset_data, 'password');
    	  $password_a = array_var($reset_data, 'password_a');
    	  
    	  $errors = new ValidationErrors();
    	  
    	  if(strlen_utf($password) < 3) {
    	    $errors->addError(lang('Minimal password length is 3 characters'), 'password');
    	  } // if
    	  
    	  if($password != $password_a) {
    	    $errors->addError(lang('Passwords do not match'), 'passwords');
    	  } // if
    	  
    	  if($errors->hasErrors()) {
    	    $this->smarty->assign('errors', $errors);
          $this->render();
    	  } // if
    	  
    	  $user->setPassword($password);
    	  $user->setPasswordResetKey(null);
    	  $user->setPasswordResetOn(null);
    	  
    	  $save = $user->save();
    	  if($save && !is_error($save)) {
    	    $this->authentication->provider->logUserIn($user);
    	    
    	    flash_success('Welcome back :name', array('name' => $user->getDisplayName()));
    	    $this->redirectTo('dashboard');
    	  } else {
    	    $this->smarty->assign('errors', $errors);
    	  } // if
    	} // if
    } // reset_password
    
    /**
     * Refresh session
     *
     * @param void
     * @return null
     */
    function refresh_session() {
      require_once SMARTY_PATH . '/plugins/modifier.datetime.php';
      print 'Session refreshed on: ' . smarty_modifier_datetime(new DateTimeValue());
      die();
    } // refresh_session
    
  } // AuthController

?>