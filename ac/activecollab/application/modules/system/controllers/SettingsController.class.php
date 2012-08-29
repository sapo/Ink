<?php

  // Build on top of administration controller
  use_controller('admin');
  
  /**
   * Administration settings controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class SettingsController extends AdminController  {
    
    /**
     * Name of this controller (underscore)
     *
     * @var string
     */
    var $controller_name = 'settings';
       
    /**
     * General settings
     *
     * @param void
     * @return null
     */
    function general() {
      $general_data = $this->request->post('general');
    	if(!is_array($general_data)) {
    		$configs = ConfigOptions::findbyNames(array('theme', 'default_assignments_filter', 'project_templates_group', 'show_welcome_message', 'projects_use_client_icons', 'on_logout_url'));
    		
    		$general_data = array(
    		  'theme' => $configs['theme']->getValue(),
    		  'default_assignments_filter' => $configs['default_assignments_filter']->getValue(),
    		  'project_templates_group' => $configs['project_templates_group']->getValue(),
    		  'show_welcome_message' => $configs['show_welcome_message']->getValue(),
    		  'projects_use_client_icons' => $configs['projects_use_client_icons']->getValue(),
    		  'on_logout_url' => $configs['on_logout_url']->getValue(),
    		);
    		
    		$general_data['use_on_logout_url'] = $general_data['on_logout_url'] && is_valid_url($general_data['on_logout_url']);
    	} // if
    	$this->smarty->assign('general_data', $general_data);
    	
    	if($this->request->isSubmitted()) {
    		ConfigOptions::setValue('theme', $general_data['theme']);
    		UserConfigOptions::deleteByOption('theme'); // reset
    		
    		ConfigOptions::setValue('default_assignments_filter', (integer) $general_data['default_assignments_filter']);
    		ConfigOptions::setValue('project_templates_group', $general_data['project_templates_group']);
    		ConfigOptions::setValue('show_welcome_message', (boolean) $general_data['show_welcome_message']);
    		ConfigOptions::setValue('projects_use_client_icons', (boolean) $general_data['projects_use_client_icons']);
    		
    		if($this->request->post('use_on_logout_url')) {
    		  $logout_url = trim($general_data['on_logout_url']);
    		  if($logout_url && is_valid_url($logout_url)) {
    		    ConfigOptions::setValue('on_logout_url', $logout_url);
    		  } else {
    		    ConfigOptions::setValue('on_logout_url', '');
    		  } // if
    		} else {
    		  ConfigOptions::setValue('on_logout_url', '');
    		} // if
    		
    		cache_remove('project_icons');
    		cache_remove_by_pattern('user_config_options_*');
    		
    		flash_success('General settings updated');
    	  $this->redirectTo('admin');
    	} // if
    } // general
    
    /**
     * Show date and time configuration panel
     *
     * @param void
     * @return null
     */
    function date_time() {
    	$date_time_data = $this->request->post('date_time');
    	if(!is_array($date_time_data)) {
    		$configs = ConfigOptions::findbyNames(array(
    		  'time_timezone', 
    		  'time_dst', 
    		  'time_first_week_day',
    		  'format_date',
    		  'format_time',
    		));
    		
    		$date_time_data = array(
    		  'time_timezone'       => $configs['time_timezone']->getValue(),
    		  'time_dst'            => $configs['time_dst']->getValue(),
    		  'time_first_week_day' => $configs['time_first_week_day']->getValue(),
    		  'format_date'         => $configs['format_date']->getValue(),
    		  'format_time'         => $configs['format_time']->getValue(),
    		);
    	} // if
    	$this->smarty->assign('date_time_data', $date_time_data);
    	
    	if($this->request->isSubmitted()) {
    		ConfigOptions::setValue('time_timezone', (integer) $date_time_data['time_timezone']);
    		ConfigOptions::setValue('time_dst', (integer) $date_time_data['time_dst']);
    		ConfigOptions::setValue('time_first_week_day', (integer) $date_time_data['time_first_week_day']);
    	  ConfigOptions::setValue('format_date', $date_time_data['format_date']);
    	  ConfigOptions::setValue('format_time', $date_time_data['format_time']);
    	  
    	  cache_remove_by_pattern('user_config_options_*');
    		
    		flash_success('Date and time settings updated');
    	  $this->redirectTo('admin');
    	} // if
    } // date_time
    
    /**
     * Mailing Settings
     *
     * @param void
     * @return null
     */
    function mailing() {
      $this->smarty->assign('admin_email', ADMIN_EMAIL);
      
    	$mailing_data = $this->request->post('mailing');
    	if(!is_array($mailing_data)) {
    		$configs = ConfigOptions::findbyNames(array(
    		  'mailing', 
    		  'mailing_smtp_host', 
    		  'mailing_smtp_port', 
    		  'mailing_smtp_authenticate',
    		  'mailing_smtp_username', 
    		  'mailing_smtp_password', 
    		  'mailing_smtp_security',
    		  'mailing_native_options',
    		  'mailing_mark_as_bulk',
    		  'mailing_empty_return_path',
    		  'notifications_from_email',
    		  'notifications_from_name'
    		));
    		
    		$mailing_data = array(
    		  'mailing'                   => $configs['mailing']->getValue(),
    		  'mailing_smtp_host'         => $configs['mailing_smtp_host']->getValue(),
    		  'mailing_smtp_port'         => $configs['mailing_smtp_port']->getValue(),
    		  'mailing_smtp_authenticate' => $configs['mailing_smtp_authenticate']->getValue(),
    		  'mailing_smtp_username'     => $configs['mailing_smtp_username']->getValue(),
    		  'mailing_smtp_password'     => $configs['mailing_smtp_password']->getValue(),
    		  'mailing_smtp_security'     => $configs['mailing_smtp_security']->getValue(),
    		  'mailing_native_options'    => $configs['mailing_native_options']->getValue(),
    		  'mailing_mark_as_bulk'      => $configs['mailing_mark_as_bulk']->getValue(),
    		  'mailing_empty_return_path' => $configs['mailing_empty_return_path']->getValue(),
    		  'notifications_from_email'  => $configs['notifications_from_email']->getValue(),
    		  'notifications_from_name'   => $configs['notifications_from_name']->getValue(),
    		);
    	} // if
    	
    	$this->smarty->assign('mailing_data', $mailing_data);
    	js_assign('test_smtp_connection_url', assemble_url('admin_settings_mailing_test_connection', array('async'=>1)));
    	
    	if($this->request->isSubmitted()) {
        $errors = new ValidationErrors();
        
    	  if($mailing_data['mailing'] == 'smtp') {
    	    if(empty($mailing_data['mailing_smtp_host'])) {
    	    	$errors->addError('SMTP host is required', 'mailing_smtp_host');
    	    } // if
    	    
    	    if(empty($mailing_data['mailing_smtp_port'])) {
    	    	$errors->addError('SMTP port is required', 'mailing_smtp_port');
    	    } // if
    	    
    	    // only for smtp authentication check username & password
          if($mailing_data['mailing_smtp_authenticate']) {
            if(empty($mailing_data['mailing_smtp_username'])) {
            	$errors->addError('SMTP username is required', 'mailing_smtp_username');
            } // if
            if(empty($mailing_data['mailing_smtp_password'])) {
            	$errors->addError('SMTP password is required', 'mailing_smtp_password');
            } // if
          } // if
          
          // From email address
          if(!empty($mailing_data['notifications_from_email'])) {
            if(!is_valid_email($mailing_data['notifications_from_email'])) {
              $errors->addError('Email address is not valid', 'notifications_from_email');
            } // if
          } // if
          
    	    if($errors->hasErrors()) {
    	    	$this->smarty->assign('errors', $errors);
    	    	$this->render();
    	    } // if
    	    
    	    ConfigOptions::setValue('mailing', (string) $mailing_data['mailing']);
    	    ConfigOptions::setValue('mailing_smtp_host', (string) $mailing_data['mailing_smtp_host']);
    	    ConfigOptions::setValue('mailing_smtp_port', (integer) $mailing_data['mailing_smtp_port']);
    	    ConfigOptions::setValue('mailing_smtp_authenticate', (boolean) $mailing_data['mailing_smtp_authenticate']);
    	    ConfigOptions::setValue('mailing_smtp_username', (string) $mailing_data['mailing_smtp_username']);
    	    ConfigOptions::setValue('mailing_smtp_password', (string) $mailing_data['mailing_smtp_password']);
    	    ConfigOptions::setValue('mailing_smtp_security', (string) $mailing_data['mailing_smtp_security']);
    	  } // if
    	  
    	  if(!$errors->hasErrors()) {
    	    ConfigOptions::setValue('mailing', $mailing_data['mailing']);
    	    ConfigOptions::setValue('mailing_native_options', (string) $mailing_data['mailing_native_options']);
    	    ConfigOptions::setValue('mailing_mark_as_bulk', (boolean) $mailing_data['mailing_mark_as_bulk']);
    	    ConfigOptions::setValue('mailing_empty_return_path', (boolean) $mailing_data['mailing_empty_return_path']);
      	  ConfigOptions::setValue('notifications_from_email', trim($mailing_data['notifications_from_email']));
      	  ConfigOptions::setValue('notifications_from_name', (string) $mailing_data['notifications_from_name']);
    	  } // if
    	  
    	  flash_success('Mailing settings updated');
    	  $this->redirectTo('admin_settings_mailing');
    	} // if
    } // mailing
    
    /**
     * Controller action called exclusively to check if connection parameters are ok
     * 
     * @param void
     * @return null
     */
    function mailing_test_connection() {
      if (!$this->request->isAsyncCall()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
    	if(!$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
    	} // if
      
    	$mailing_data = $this->request->post('mailing');
    	if (!is_array($mailing_data)) {
    	  $this->httpError(HTTP_ERR_INVALID_PROPERTIES);
    	} // if
    	
    	// Include files before we start using constants
    	require_once ANGIE_PATH . '/classes/swiftmailer/init.php';
      require_once SWIFTMAILER_LIB_PATH . '/Swift/Connection/SMTP.php';
    	  	
    	$mailing_host = array_var($mailing_data,'mailing_smtp_host', null);
    	$mailing_port = array_var($mailing_data,'mailing_smtp_port', null);
      switch(array_var($mailing_data,'mailing_smtp_security', null)) {
        case 'tsl':
          $mailing_encryption = SWIFT_SMTP_ENC_TLS;
          break;
        case 'ssl':
          $mailing_encryption = SWIFT_SMTP_ENC_SSL;
          break;
        default:
          $mailing_encryption = SWIFT_SMTP_ENC_OFF;
      } // switch
      $mailing_username = array_var($mailing_data,'mailing_smtp_username', null);
      $mailing_password = array_var($mailing_data,'mailing_smtp_password', null);
      
      $message = '';
      $is_success = false;
      $exception = null;
      Swift_Errors::expect($exception, "Swift_ConnectionException");
    	$smtp = new Swift_Connection_SMTP($mailing_host, $mailing_port, $mailing_encryption);
    	$smtp->setTimeout(15);
      if (array_var($mailing_data,'mailing_smtp_authenticate', null)) {
        $smtp->setUsername($mailing_username);
        $smtp->setPassword($mailing_password);
      } // if
      $swift = new Swift($smtp);
      $swift->connect();

      if ($exception !== null) {
        if (instance_of($exception, 'Swift_ConnectionException')) {
          $message = $exception->getMessage(); 
        } else {
          $message = lang('Unknown Error');
        } // if
      } else {
        $is_success = true;
        $message = lang('Connection has been established, all parameters are valid');
        Swift_Errors::clear("Swift_ConnectionException");
      } // if
      
      $this->serveData(array(
        'message' => $message,
        'isSuccess' => $is_success,
      ), 'result', null, FORMAT_JSON);
    } // mailing_test_connection
    
    /**
     * Show and process maintenance form
     *
     * @param void
     * @return null
     */
    function maintenance() {
      $maintenance_data = $this->request->post('maintenance');
      if(!is_array($maintenance_data)) {
        $maintenance_data = array(
          'maintenance_enabled' => ConfigOptions::getValue('maintenance_enabled'),
          'maintenance_message' => ConfigOptions::getValue('maintenance_message'),
        );
      } // if
      
      $this->smarty->assign('maintenance_data', $maintenance_data);
      
      if($this->request->isSubmitted()) {
        ConfigOptions::setValue('maintenance_enabled', (boolean) array_var($maintenance_data, 'maintenance_enabled'));
        ConfigOptions::setValue('maintenance_message', trim(array_var($maintenance_data, 'maintenance_message')));
        
        flash_success('Maintenance mode settings have been updated');
        $this->redirectTo('admin_settings_maintenance');
      } // if
    } // maintenance
    
    /**
     * Hide welcome message
     *
     * @param void
     * @return null
     */
    function hide_welcome_message() {
      if($this->request->isSubmitted()) {
      	ConfigOptions::setValue('show_welcome_message', false);
      	$this->redirectTo('dashboard');
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // hide_welcome_message
    
  }

?>