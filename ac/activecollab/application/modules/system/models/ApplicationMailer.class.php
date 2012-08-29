<?php

  /**
   * Application mailer
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ApplicationMailer extends AngieObject {
    
    /**
     * Swift instance
     *
     * @var Swift
     */
    var $swift;
    
    /**
     * Caches list of messages that need to be sent out
     *
     * @var array
     */
    var $messages = array();
    
    /**
     * Indicator whether Swift is connected or not
     *
     * @var boolean
     */
    var $connected = false;
    
    /**
     * Send email to list of recipients
     * 
     * $to is a list of users who need to receive email notifications. If this 
     * function gets list of email addresses default language will be used. If 
     * we get User instances we'll use language set as prefered on their profile 
     * page
     * 
     * $to can also be a single user or email address
     * 
     * $tpl is a script in format module/name. If / is not present activeCollab 
     * will assume that template is in system module
     * 
     * Context is object that this email is primary related to
     * 
     * $attachments is array of attachments that are structured like this
     *    path -> path to file
     *    name -> name which will be displayed in email (if ommited original filename will be used)
     *    mime_type -> file mime type (if ommited system will determine mime type automatically)
     * here is sample of one $attachments array
     *    $attachments = array(
     *      array('path' => '/work/picture3.png', 'name' => 'simple_file_name.png', 'mime_type' => 'image/png')
     *    );
     * 
     * @param array $to
     * @param string $tpl
     * @param array $replacements
     * @param mixed $context
     * @param array $attachments
     * @return boolean
     */
    function send($to, $tpl, $replacements = null, $context = null, $attachments = null) {
      static $mark_as_bulk = null, $empty_return_path = null;
      
      if(isset($this) && instance_of($this, 'ApplicationMailer')) {
        if(!$this->connected) {
          $this->connect();
        } // if
        
        if(!is_foreachable($to)) {
          if(instance_of($to, 'User') || is_valid_email($to)) {
            $to = array($to);
          } else {
            return true; // no recipients
          } // if
        } // if
        
      	if(strpos($tpl, '/') === false) {
      	  $template_module = SYSTEM_MODULE;
      	} else {
      	  list($template_module, $template_name) = explode('/', $tpl);
      	} // if
      	
      	$template = EmailTemplates::findById(array(
      	  'module' => $template_module,
      	  'name'   => $template_name,
      	));
      	
      	if(!instance_of($template, 'EmailTemplate')) {
      	  return false;
      	} // if
      	
      	$owner_company = get_owner_company();
        if(is_array($replacements)) {
          $replacements['owner_company_name'] = $owner_company->getName();
        } else {
          $replacements = array('owner_company_name' => $owner_company->getName());
        } // if
        
        // Array of messages and recipients organized by language
        $to_send = array();
        
        // Set default locale (built in one)
        $default_locale = BUILT_IN_LOCALE;
        
        // Do we have a default language set
        $default_language_id = ConfigOptions::getValue('language');
        if($default_language_id) {
          $default_language = Languages::findById($default_language_id);
          if(instance_of($default_language, 'Language') && !$default_language->isBuiltIn()) {
            $default_locale = $default_language->getLocale();
          } // if
        } // if
        
        // cache of loaded languages
        $languages = array();
        
        foreach($to as $recipient) {
          $locale = $default_locale;
          if(instance_of($recipient, 'User')) {
            $locale = $recipient->getLocale($default_locale);
            
            $recipient_name = $recipient->getDisplayName();
            $recipient_email = $recipient->getEmail();
            
            // If same reset name... "name@site.com <name@site.com>" can cause 
            // problems with some servers
            if($recipient_name == $recipient_email) {
              $recipient_name = null;
            } // if
          } else {
            $recipient_name = null;
            $recipient_email = $recipient;
          } // if
          
          $language = isset($languages[$locale]) ? $languages[$locale] : Languages::findByLocale($locale);
          
          // We have message prepared, just need to add a recipient
          if(isset($to_send[$locale])) {
            $to_send[$locale]['recipients']->add($recipient_email, $recipient_name);
          // Need to prepare message and add first recipient
          } else {
            $subject = $template->getSubject($locale);
            $body = $template->getBody($locale);
            
            foreach($replacements as $k => $v) {
              if(is_array($v)) {
                $v = isset($v[$locale]) ? $v[$locale] : array_shift($v);
              } // if
              
              $subject = str_replace(":$k", $v, $subject);
              if(str_ends_with($k, '_body')) {
                $body = str_replace(":$k", $v, $body);
              } else {
                $body = str_replace(":$k", clean($v), $body);
              } // if
            } // foreach
            
            event_trigger('on_prepare_email', array($tpl, $recipient_email, $context, &$body, &$subject, &$attachments, &$language));
            
            // if files need to be attached, message will be multipart
            if (is_foreachable($attachments)) {
              $message = new Swift_Message($subject);
              $message->attach(new Swift_Message_Part($body, 'text/html', EMAIL_ENCODING, EMAIL_CHARSET));
              foreach ($attachments as $attachment) {
                $file_path = array_var($attachment, 'path', null);
                if (file_exists($file_path)) {
                  $message->attach(new Swift_Message_Attachment(
                    new Swift_File($file_path),
                    array_var($attachment, 'name', basename($file_path)),
                    array_var($attachment, 'mime_type', mime_content_type($file_path))
                  ));
                }
              } // if
            } else {
              $message = new Swift_Message($subject, $body, 'text/html', EMAIL_ENCODING, EMAIL_CHARSET);
            } // if
            
            // Load values...
            if($mark_as_bulk === null || $empty_return_path === null) {
              $mark_as_bulk = (boolean) ConfigOptions::getValue('mailing_mark_as_bulk');
              $empty_return_path = (boolean) ConfigOptions::getValue('mailing_empty_return_path');
            } // if
            
            // Custom headers (to prevent auto responders)
            if($mark_as_bulk) {
              $message->headers->set('Auto-Submitted', 'auto-generated');
              $message->headers->set('Precedence', 'bulk');
            } // if
            
            if($empty_return_path) {
              $message->headers->set('Return-Path', '<>');
            } // if
            
            $to_send[$locale] = array(
              'recipients' => new Swift_RecipientList(),
              'message'    => $message,
            );
            $to_send[$locale]['recipients']->add($recipient_email, $recipient_name);
          } // if
        } // foreach
        
        $from_email = ConfigOptions::getValue('notifications_from_email');
        $from_name = ConfigOptions::getValue('notifications_from_name');
        
        if(!is_valid_email($from_email)) {
          $from_email = ADMIN_EMAIL;
        } // if
        
        if(empty($from_name)) {
          $from_name = $owner_company->getName();
        } // if
        
        if(is_foreachable($to_send)) {
          foreach($to_send as $locale => $message_data) {
            $this->swift->batchSend($message_data['message'], $message_data['recipients'], new Swift_Address($from_email, $from_name));
          } // foreach
        } // if
      
        return true;
      } else {
        $instance =& ApplicationMailer::instance();
        return $instance->send($to, $tpl, $replacements, $context, $attachments);
      } // if
    } // send
    
    /**
     * Return mailer instance
     *
     * @param void
     * @return Swift
     */
    function &mailer() {
  	  $instance =& ApplicationMailer::instance();
  	  if(!$instance->connected) {
  	    $instance->connect();
  	  } // if
  	  return $instance->swift;
    } // mailer
    
    // ---------------------------------------------------
    //  Utility methods
    // ---------------------------------------------------
    
    /**
     * Connect mailer instance
     *
     * @param void
     * @return boolean
     */
    function connect() {
      if($this->connected) {
        return true;
      } // if
      
      require_once ANGIE_PATH . '/classes/swiftmailer/init.php';
      
      $mailing = ConfigOptions::getValue('mailing');
      
      // Create native connection
      if($mailing == MAILING_NATIVE) {
        require_once SWIFTMAILER_LIB_PATH . '/Swift/Connection/NativeMail.php';
        
        $options = trim(ConfigOptions::getValue('mailing_native_options'));
        if(empty($options)) {
          $options = null;
        } // if
        
        $this->swift = new Swift(new Swift_Connection_NativeMail($options));
        
      // Create SMTP connection
      } elseif($mailing == MAILING_SMTP) {
        require_once SWIFTMAILER_LIB_PATH . '/Swift/Connection/SMTP.php';
        
        $smtp_host = ConfigOptions::getValue('mailing_smtp_host');
        $smtp_port = ConfigOptions::getValue('mailing_smtp_port');
        $smtp_auth = ConfigOptions::getValue('mailing_smtp_authenticate');
        $smtp_user = ConfigOptions::getValue('mailing_smtp_username');
        $smtp_pass = ConfigOptions::getValue('mailing_smtp_password');
        $smtp_security = ConfigOptions::getValue('mailing_smtp_security');
        
        switch($smtp_security) {
          case 'tsl':
            $smtp_enc = SWIFT_SMTP_ENC_TLS;
            break;
          case 'ssl':
            $smtp_enc = SWIFT_SMTP_ENC_SSL;
            break;
          default:
            $smtp_enc = SWIFT_SMTP_ENC_OFF;
        } // switch
        
        $smtp = new Swift_Connection_SMTP($smtp_host, $smtp_port, $smtp_enc);
        if($smtp_auth) {
          $smtp->setUsername($smtp_user);
          $smtp->setPassword($smtp_pass);
        } // if
        
        $this->swift = new Swift($smtp);
        
      // Not supported!
      } else {
        return new InvalidParamError('mailing', $mailer, "Unknown mailing type: '$mailing' in configuration", true);
      } // if
      
      // Set logger
      if(DEBUG >= DEBUG_DEVELOPMENT) {
        Swift_ClassLoader::load("Swift_Log_AngieLog");
        
        $logger = new Swift_Log_AngieLog();
        $logger->setLogLevel(SWIFT_LOG_EVERYTHING);
        
        Swift_LogContainer::setLog($logger);
      } // if
      return $this->swift;
    } // connect
    
    /**
     * Disconnect mailer
     *
     * @param void
     * @return boolean
     */
    function disconnect() {
    	if($this->connected && instance_of($this->swift, 'Swift')) {
    	  $this->swift->disconnect();
    	} // if
    	$this->connected = false;
    } // disconnect
    
    /**
     * Returns true if mailing functonality is disabled
     *
     * @param void
     * @return boolean
     */
    function isDisabled() {
    	return defined('DISABLE_MAILING') && DISABLE_MAILING;
    } // isDisabled
   
    /**
     * Return mailer instance
     *
     * @param void
     * @return ApplicationMailer
     */
    function &instance() {
    	static $instance;
    	if(!instance_of($instance, 'ApplicationMailer')) {
    	  $instance = new ApplicationMailer();
    	} // if
    	return $instance;
    } // instance
     
  }

?>