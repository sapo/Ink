<?php

  // we need mobile access controller
  use_controller('mobile_access', MOBILE_ACCESS_MODULE);

  /**
   * Authentication controller
   * 
   * This controller will handle user login, logout for mobile devices
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessAuthController extends MobileAccessController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access_auth';
    
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
     * Construct auth controller
     *
     * @param Request $request
     * @return AuthController
     */
    function __construct($request) {
      parent::__construct($request);
      $this->setLayout('auth');
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
      
      if (!$this->request->isSubmitted()) {
        $login_data["use_mobile"] = true;
        $this->smarty->assign(array(
          'login_data' => $login_data,
        ));
      } // if
      
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
        
        flash_success('Welcome back :display!', array('display' => $user->getDisplayName()));
        
        if ($redirect_to === null) {
          if (array_var($login_data, 'use_mobile')) {
            $redirect_to = assemble_url('mobile_access');
          } else {
            $redirect_to = assemble_url('dashboard');
          } // if
        } // if
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
      if(!instance_of($this->logged_user, 'User')) {
        $this->redirectTo('mobile_access_login');
      } // if
      
      // Logout and redirect to login screen...
      $this->authentication->provider->logUserOut();
      
      $option = ConfigOptions::findByName('on_logout_url');
      $on_logout_url = $option->getValue();
      $this->redirectToUrl(is_valid_url($on_logout_url) ? $on_logout_url : assemble_url('mobile_access_login'));
    } // logout
    
    
    /**
     * Render and process forgot password form
     *
     * @param void
     * @return null
     */
    function forgot_password() {
      $forgot_password_data = $this->request->post('forgot_password');
      
      $this->smarty->assign(array(
        "page_title" => lang('Password recovery'),
      ));
      
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
    
  } // AuthController

?>