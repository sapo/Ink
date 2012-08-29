<?php

  // We build on admin controller
  use_controller('admin');
  
  /**
   * System module administration tools controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class SystemToolsController extends AdminController {
    
    /**
     * Name of this controller (underscore)
     *
     * @var string
     */
    var $controller_name = 'system_tools';
    
    /**
    * Constructor
    *
    * @param void
    * @return ToolsController
    */
    function __construct($request) {
      parent::__construct($request);
      
      $this->smarty->assign(array(
        'test_email_url'  => assemble_url('admin_tools_test_email'),
        'mass_mailer_url' => assemble_url('admin_tools_mass_mailer'),
      ));
    } // __construct
      
    /**
     * Test Mailer
     *
     * @param void
     * @return null
     */
    function test_email() {
      $email_data = $this->request->post('email');
      if(!is_array($email_data)) {
        $email_data = array(
          'recipient' => $this->logged_user->getEmail(),
          'subject' => lang('activeCollab - test email'),
          'message' => lang("<p>Hi,</p>\n\n<p>Purpose of this message is to test whether activeCollab can send emails or not</p>"),
        );
      } // if
      
      $this->smarty->assign('email_data', $email_data);
    	if($this->request->isSubmitted()) {
    	  $errors = new ValidationErrors();
    	  
    	  $subject = trim(array_var($email_data, 'subject'));
    	  $message = trim(array_var($email_data, 'message'));
    	  $recipient = trim(array_var($email_data, 'recipient'));
    	  
    	  if($subject == '') {
    	    $errors->addError(lang('Message subject is required'), 'subject');
    	  } // if
    	  
    	  if($message == '') {
    	    $errors->addError(lang('Message body is required'), 'message');
    	  } // if
    	  
    	  if(is_valid_email($recipient)) {
    	    $recipient_name = null;
    	    $recipient_email = $recipient;
    	  } else {
    	    if((($pos = strpos($recipient, '<')) !== false) && str_ends_with($recipient, '>')) {
    	      $recipient_name = trim(substr($recipient, 0, $pos));
    	      $recipient_email = trim(substr($recipient, $pos + 1, strlen($recipient) - $pos - 2));
    	      
    	      if(!is_valid_email($recipient_email)) {
    	        $errors->addError(lang('Invalid email address'), 'recipient');
    	      } // if
    	    } else {
    	      $errors->addError(lang('Invalid recipient'), 'recipient');
    	    } // if
    	  } // if
    	  
    	  if($errors->hasErrors()) {
    	    if(instance_of($mailer, 'Swift')) {
    	      $mailer->disconnect();
    	    } // if
    	    
    	    $this->smarty->assign('errors', $errors);
    	    $this->render();
    	  } // if
    	  
    	  $mailer =& ApplicationMailer::mailer();
    	  
    	  $email_message = new Swift_Message($subject, $message, 'text/html', EMAIL_ENCODING, EMAIL_CHARSET);
    	  if($mailer->send($email_message, new Swift_Address($recipient_email, $recipient_name), $this->logged_user->getEmail())) {
    	  	flash_success('Test email has been sent, check your inbox');
    	  } else {
    	    flash_error('Failed to send out test email');
    	  } // if
    	  
    	  $this->redirectTo('admin_tools_test_email');
    	} // if    	
    } // test_email
    
    /**
     * Mass mailer
     *
     * @param void
     * @return null
     */
    function mass_mailer() {
      if (!MASS_MAILER_ENABLED) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
    	$email_data = $this->request->post('email');
      $this->smarty->assign(array(
        'email_data' => $email_data,
        'exclude' => array($this->logged_user->getId()),
      ));

      if($this->request->isSubmitted()) {
        $errors = new ValidationErrors();
        
        $subject = trim(array_var($email_data, 'subject'));
        $body = trim(array_var($email_data, 'body'));
        $recipient_ids = array_var($email_data, 'recipients');
        
        if(empty($subject)) {
          $errors->addError(lang('Subject is required'), 'subject');			
        } // if		
        
        if(empty($body)) {
        	$errors->addError(lang('Body is required'), 'body');
        } // if
        
        $recipients = array();
        if(is_foreachable($recipient_ids)) {
          $recipients = Users::findByIds(array_unique($recipient_ids));
        } // if
        
        if(!is_foreachable($recipients)) {
          $errors->addError(lang('Please select recipients'), 'recipients');
        } // if
        
        if($errors->hasErrors()) {
        	$this->smarty->assign('errors', $errors);
        	$this->render();
        } // if
        
        $mailer =& ApplicationMailer::mailer();
        
        $message = new Swift_Message($subject, $body, 'text/html', EMAIL_ENCODING, EMAIL_CHARSET);
        $recipients_list = new Swift_RecipientList();
        
        foreach($recipients as $recipient) {
          $name = $recipient->getDisplayName();
          $email = $recipient->getEmail();
          
          if($name == $email) {
            $name = '';
          } // if
          
          $recipients_list->add($email, $name);
        } // foreach
        
        $name = $this->logged_user->getDisplayName();
        $email = $this->logged_user->getEmail();
        if($name == $email) {
          $name = '';
        } // if
        
        if($mailer->batchSend($message, $recipients_list, new Swift_Address($email, $name))) {
          flash_success('Email has been successfully sent');
        } else {
          flash_error('Failed to send email');
        } // if
        
        $this->redirectTo('admin_tools_mass_mailer');
      } // if    	
    } // mass_mailer
    
    /**
     * Show scheduled tasks page
     *
     * @param void
     * @return null
     */
    function scheduled_tasks() {
      $options = ConfigOptions::findbyNames(array('last_frequently_activity', 'last_hourly_activity', 'last_daily_activity'));
      
      $values = array(
    	  'last_frequently_activity' => (integer) $options['last_frequently_activity']->getValue(),
    	  'last_hourly_activity'     => (integer) $options['last_hourly_activity']->getValue(),
    	  'last_daily_activity'      => (integer) $options['last_daily_activity']->getValue(),
    	);
    	
    	// Convert non-NULL values into date time value objects
    	foreach($values as $k => $v) {
    	  if($v > 0) {
    	    $values[$k] = new DateTimeValue($v);
    	  } // if
    	} // foreach
      
    	$this->smarty->assign($values);
    } // scheduled_tasks
    
  }
  
?>