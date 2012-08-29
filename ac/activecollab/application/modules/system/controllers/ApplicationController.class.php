<?php

  /**
   * Application controller
   *
   * This controller is base controller for all controllers in the system. It 
   * will log in the user and if $login_required variable is set to true (true 
   * by default) user will be redirected to login route...
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ApplicationController extends PageController {
    
    /**
     * User is required to be logged in
     * 
     * If true user will need to be logged in in order to execute controller 
     * actions. If user is not logged in he will be redirected to login route. 
     * Override in subclasses for controllers that does not require for user to 
     * be logged in
     *
     * @var boolean
     */
    var $login_required = true;
    
    /**
     * Name of the login route
     *
     * @var string
     */
    var $login_route = 'login';
    
    /**
     * User needs to have system access permissions
     * 
     * If true user will need to have access permissions in order to execute 
     * actions of this controller. Access permissions will be checked only if 
     * login is required (in other case we will not have user to check 
     * permissions against)
     *
     * @var boolean
     */
    var $access_permissions_required = true;
    
    /**
     * This controller restricts access to people who are not logged in when 
     * system is in maintenance mode
     *
     * @var boolean
     */
    var $restrict_access_in_maintenance_mode = true;
    
    /**
     * Smarty instance                           
     *
     * @var Smarty
     */
    var $smarty;
    
    /**
     * Wireframe instance
     *
     * @var Wireframe
     */
    var $wireframe;
    
    /**
     * Application object instance
     *
     * @var ActiveCollab
     */
    var $application;
    
    /**
     * Authentication instance (Authentication::instance())
     * 
     * @var Authentication
     */
    var $authentication;
    
    /**
     * Logged in user
     *
     * @var User
     */
    var $logged_user;
    
    /**
     * Owner company
     * 
     * Instance of account owner company. Script will break if owner company does 
     * not exist
     *
     * @var Company
     */
    var $owner_company;
    
    /**
     * Name of the selected theme
     *
     * @var string
     */
    var $theme_name;
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return ApplicationController
     */
    function __construct($request) {
      parent::__construct($request);
      
      // Set detault layout for application pages
      $this->setLayout(array(
        'module' => SYSTEM_MODULE,
        'layout' => 'wireframe',
      ));
      
      // Get Smarty instance... We need it
      $this->smarty =& Smarty::instance();
      
      // Load and init owner company
      $this->owner_company = get_owner_company();
      
      if(instance_of($this->owner_company, 'Company')) {
        cache_set('owner_company', $this->owner_company);
      } else {
        $this->httpError(HTTP_ERR_NOT_FOUND, 'Owner company is not defined');
      } // if

      $this->application =& application();
      $this->authentication =& Authentication::instance();
      $this->logged_user =& $this->authentication->provider->getUser();
      
      $this->wireframe =& Wireframe::instance();
      $this->wireframe->page_company = $this->owner_company;
      $this->theme_name = instance_of($this->logged_user, 'User') ? UserConfigOptions::getValue('theme', $this->logged_user) : ConfigOptions::getValue('theme');
      
      $this->smarty->assign(array(
        'root_url'   => ROOT_URL,
        'assets_url' => ASSETS_URL,
      ));
      
      // Maintenance mode
      if(ConfigOptions::getValue('maintenance_enabled')) {
        if(instance_of($this->logged_user, 'User') && $this->logged_user->isAdministrator()) {
          $this->wireframe->addPageMessage(lang('System is in maintenance mode and can be used by administrators only. <a href=":url">Click here</a> to turn off maintenance mode', array('url' => assemble_url('admin_settings_maintenance'))), 'warning');
        } else {
          $additional_error_info = ConfigOptions::getValue('maintenance_message');
          if($additional_error_info) {
            $additional_error_info .= "\n\n";
          } // if
          $additional_error_info .= lang('When system is in maintenance mode, administrators can log in and access the system') . ": " . assemble_url('login');
        
          $this->smarty->assign('additional_error_info', $additional_error_info);
          
          if($this->restrict_access_in_maintenance_mode) {
            $this->httpError(503);
          } // if
        } // if
      } // if
      
      // Check permissions
      if($this->login_required && !instance_of($this->logged_user, 'User')) {
        
        // If async don't redirect to loging, just server proper HTTP code
        if($this->request->isAsyncCall()) {
          $this->httpError(HTTP_ERR_UNAUTHORIZED, null, true, true);
          
        // Not async? Redirect to login with extracted route data...
        } else {
          $params = array();
          if($request->matched_route != 'login') {
            $params['re_route'] = $request->matched_route;
            foreach($this->request->url_params as $k => $v) {
              if(($k == 'module') || ($k == 'controller') || ($k == 'action')) {
                continue;
              } // if
              
              $params["re_$k"] = $v;
            } // foreach
          } // if
          
          $this->redirectTo($this->login_route, $params);
        } // if
      } // if
      
      if(instance_of($this->logged_user, 'User') && !$this->logged_user->getSystemPermission('system_access')) {
        $this->authentication->provider->logUserOut();
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $loaded_modules = $this->application->getModules();
      
      $assets_query_string = 'v='.$this->application->version.'&modules=';
      foreach ($loaded_modules as $loaded_module) {
      	$assets_query_string.= $loaded_module->getName().',';
      } // foreach
      
      $this->smarty->assign(array(
        'api_status'      => API_STATUS,
        'application'     => $this->application,
        'owner_company'   => $this->owner_company,
        'authentication'  => $this->authentication,
        'logged_user'     => $this->logged_user,
        'request'         => $this->request,
        'theme_name'      => $this->theme_name,
        'request_time'    => $this->request_time,
        'loaded_modules'  => $this->application->getModules(),
        'captcha_url'     => ROOT_URL.'/captcha.php?id='.md5(time()),
        'assets_query_string' => $assets_query_string,
        'js_disabled_url'   => assemble_url('js_disabled'),
      ));
      
      $this->smarty->assign_by_ref('wireframe', $this->wireframe);
      
      js_assign(array(
        'homepage_url' => ROOT_URL,
        'assets_url' => ASSETS_URL,
        'indicator_url' => get_image_url('indicator.gif'),
        'big_indicator_url' => get_image_url('indicator_big.gif'),
        'ok_indicator_url' => get_image_url('ok_indicator.gif'),
        'warning_indicator_url' => get_image_url('warning_indicator.gif'),
        'error_indicator_url' => get_image_url('error_indicator.gif'),
        'pending_indicator_url' => get_image_url('pending_indicator.gif'),
        'url_base' => URL_BASE,
        'keep_alive_interval' => KEEP_ALIVE_INTERVAL,
        'refresh_session_url' => assemble_url('refresh_session'),
        'jump_to_project_url' => assemble_url('jump_to_project_widget'),
        'quick_add_url' => assemble_url('quick_add'),
        'path_info_through_query_string' => PATH_INFO_THROUGH_QUERY_STRING,
        'image_picker_url' => assemble_url('image_picker'),
      ));
    } // __construct
    
    /**
     * Serve data to the client
     *
     * @param mixed $data
     * @param string $as
     * @param array $additional_describe_params
     * @param string $format
     * @param boolean $die
     * @return null
     */
    function serveData($data, $as = null, $additional_describe_params = null, $format = null, $die = true) {
      $to_encode = $data;
      
      // Error
      if(is_error($data)) {
        header("HTTP/1.1 417 Expectation Failed");
        $as = 'error';
        
        $to_encode = $data->describe();
        
      // Object with describe function
      } elseif(instance_of($data, 'AngieObject') && in_array('describe', get_class_methods(get_class($data)))) {
        $to_encode = $data->describe($this->logged_user, $additional_describe_params);
        
      // Everything else
      } elseif(is_array($data)) {
        $all_objects = true;
        foreach($data as $v) {
          if(!instance_of($v, 'ApplicationObject')) {
            $all_objects = false;
          } // if
        } // foreach
        
        if($all_objects) {
          $to_encode = array();
          foreach($data as $k => $v) {
            $to_encode[$k] = $v->describe($this->logged_user, $additional_describe_params);
          } // foreach
        } // if
      } // if
      
      return parent::serveData($to_encode, $as, $format, $die);
    } // serveData
    
    /**
     * Serve HTTP error
     * 
     * If $only_headers is TRUE function returns only http header without any 
     * fancy output
     *
     * @param integer $code
     * @param string $message
     * @param boolean $die
     * @param boolean $only_header
     * @param boolean $portal_error
     * @return null
     */
    function httpError($code, $message = null, $die = true, $only_headers = false, $portal_error = false) {
      if($message === null) {
        $errors = array(
          100 => "HTTP/1.1 100 Continue",
          101 => "HTTP/1.1 101 Switching Protocols",
          200 => "HTTP/1.1 200 OK",
          201 => "HTTP/1.1 201 Created",
          202 => "HTTP/1.1 202 Accepted",
          203 => "HTTP/1.1 203 Non-Authoritative Information",
          204 => "HTTP/1.1 204 No Content",
          205 => "HTTP/1.1 205 Reset Content",
          206 => "HTTP/1.1 206 Partial Content",
          300 => "HTTP/1.1 300 Multiple Choices",
          301 => "HTTP/1.1 301 Moved Permanently",
          302 => "HTTP/1.1 302 Found",
          303 => "HTTP/1.1 303 See Other",
          304 => "HTTP/1.1 304 Not Modified",
          305 => "HTTP/1.1 305 Use Proxy",
          307 => "HTTP/1.1 307 Temporary Redirect",
          400 => "HTTP/1.1 400 Bad Request",
          401 => "HTTP/1.1 401 Unauthorized",
          402 => "HTTP/1.1 402 Payment Required",
          403 => "HTTP/1.1 403 Forbidden",
          404 => "HTTP/1.1 404 Not Found",
          405 => "HTTP/1.1 405 Method Not Allowed",
          406 => "HTTP/1.1 406 Not Acceptable",
          407 => "HTTP/1.1 407 Proxy Authentication Required",
          408 => "HTTP/1.1 408 Request Time-out",
          409 => "HTTP/1.1 409 Conflict",
          410 => "HTTP/1.1 410 Gone",
          411 => "HTTP/1.1 411 Length Required",
          412 => "HTTP/1.1 412 Precondition Failed",
          413 => "HTTP/1.1 413 Request Entity Too Large",
          414 => "HTTP/1.1 414 Request-URI Too Large",
          415 => "HTTP/1.1 415 Unsupported Media Type",
          416 => "HTTP/1.1 416 Requested range not satisfiable",
          417 => "HTTP/1.1 417 Expectation Failed",
          500 => "HTTP/1.1 500 Internal Server Error",
          501 => "HTTP/1.1 501 Not Implemented",
          502 => "HTTP/1.1 502 Bad Gateway",
          503 => "HTTP/1.1 503 Service Unavailable",
          504 => "HTTP/1.1 504 Gateway Time-out" 
        );
        
        $message = array_var($errors, $code);
        if(trim($message) == '') {
          $message = 'Unknown';
        } // if
      } // if
      
      if($this->request->isApiCall()) {
        $only_headers = true;
      } // if
      
      header("HTTP/1.1 $code $message");
      
      if($only_headers) {
        echo $message;
        if($die) {
          die();
        } // if
      } else {
        $this->setLayout('error');
        
        if($portal_error === false) {
        	$template_path = get_template_path($code, 'error');
        } else {
        	$template_path = get_template_path($code, 'portal_errors', PORTALS_MODULE);
        } // if
        
        if(!is_file($template_path)) {
          $template_path = get_template_path('default', 'error');
        } // if
        
        $this->smarty->assign(array(
          'code' => $code,
          'message' => $message,
        ));
        
        $this->renderLayout(get_layout_path('error'), $this->smarty->fetch($template_path), $die);
      } // if
    } // httpError
  
  }

?>