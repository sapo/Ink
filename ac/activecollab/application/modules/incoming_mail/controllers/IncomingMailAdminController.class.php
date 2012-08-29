<?php

  // We need admin controller
  use_controller('admin', SYSTEM_MODULE);
  
  /**
   * Manages incoming mail
   * 
   * @package activeCollab.modules.incoming_mail
   * @subpackage controllers
   */
  class IncomingMailAdminController extends AdminController {
    
    /**
     * Active Mailbox
     * 
     * @var IncomingMailbox
     */
    var $active_mailbox;
    
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'incoming_mail_admin';
    
    /**
     * Construct incoming mail administration controller
     *
     * @param Request $request
     * @return IncomingMailAdminController
     */
    function __construct($request) {
      parent::__construct($request);
      
      require_once ANGIE_PATH . '/classes/mailboxmanager/init.php';
      
      if(!extension_loaded('imap')) {
        $this->wireframe->addPageMessage(lang('<b>IMAP extension not installed</b> - IMAP extension is required for activeCollab to be able to connect to POP3/IMAP servers'), PAGE_MESSAGE_ERROR);
      } // if
           
      $mailbox_id = $this->request->getId('mailbox_id');
      if($mailbox_id) {
        $this->active_mailbox = IncomingMailboxes::findById($mailbox_id);
      } // if
      
      if (!instance_of($this->active_mailbox, 'IncomingMailbox')) {
        $this->active_mailbox = new IncomingMailbox();
      } // if
      
      $this->smarty->assign(array(
        'active_mailbox'      => $this->active_mailbox,
        'add_new_mailbox_url' => assemble_url('incoming_mail_admin_add_mailbox'),
      ));
      
      $this->wireframe->addBreadCrumb(lang('Incoming Mail'), assemble_url('incoming_mail_admin'));
      $this->wireframe->addPageAction(lang('New Mailbox'), assemble_url('incoming_mail_admin_add_mailbox'));
    } // __construct
    
    /**
     * Main page in administration for Incoming Mail
     *
     */
    function index() {
      $config_admin_email = ConfigOptions::getValue('notifications_from_email');
      $notifications_email = $config_admin_email ? $config_admin_email : ADMIN_EMAIL;

      $default_mailbox = IncomingMailboxes::findByFromEmail($notifications_email);
      $add_default_mailbox_url = assemble_url('incoming_mail_admin_add_mailbox', array('default_email_address' => $notifications_email));
      
      if (!instance_of($default_mailbox, 'IncomingMailbox')) {
        $this->wireframe->addPageMessage(
          lang('System is not able to receive email messages sent as replies to notifications. If you would like your users to be able to reply to notifications and have their messages automatically submitted as comments please <a href=":add_default_mailbox_url">define an incoming mailbox</a> for <strong>:address</strong>.', array('address' => $notifications_email, 'add_default_mailbox_url' => $add_default_mailbox_url)),
          PAGE_MESSAGE_WARNING
        );
      } elseif (!$default_mailbox->getEnabled()) {
        $this->wireframe->addPageMessage(
          lang('System is not able to receive email messages sent as replies to notifications. If you would like your users to be able to reply to notifications and have their messages automatically submitted as comments please <a href=":edit_default_mailbox_url">enable default incoming mailbox</a>.', array('edit_default_mailbox_url' => $default_mailbox->getEditUrl())),
          PAGE_MESSAGE_WARNING
        );
      } // if
      
      if (!MM_CAN_DOWNLOAD_LARGE_ATTACHMENTS) {
        $limited_filesize = format_file_size(FAIL_SAFE_IMAP_ATTACHMENT_SIZE_MAX);
        if (!function_exists('imap_savebody')) {
          $this->wireframe->addPageMessage(lang("<b>Your PHP version is obsolete</b> - You won't be able to download attachments larger than <b>:file_size</b>. Please upgrade to latest stable version of PHP to solve this issue.", array('file_size' => $limited_filesize)), PAGE_MESSAGE_WARNING);
        } else {
          $this->wireframe->addPageMessage(lang("Importing attachments larger than <b>:file_size</b> is disabled. Module uses failsafe IMAP functions due to platform restrictions.", array('file_size' => $limited_filesize)), PAGE_MESSAGE_WARNING);
        } // if
      } // if
      
      use_model('incoming_mail_activity_logs', INCOMING_MAIL_MODULE);
      
      $per_page = 50; // mailbox activity per page
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
      
      $only_problematic = (boolean) array_var($_GET,'only_problematic', false);
      
      if ($only_problematic) {
        list($activity_history, $pagination) = IncomingMailActivityLogs::paginateConflicts($page, $per_page);
      } else {
        list($activity_history, $pagination) = IncomingMailActivityLogs::paginate(array(
          'order' => 'created_on DESC'
        ), $page, $per_page);
      } // if
      
      $activity_history = group_by_date($activity_history);
      
      $this->smarty->assign(array(
        'mailboxes' => IncomingMailboxes::find(),
        'activity_history' => $activity_history,
        'pagination' => $pagination,
        'only_problematic' => $only_problematic,
      ));

    } // index
    
    /**
     * Form for adding mailbox
     * 
     * @param void
     * @return void
     */
    function add_mailbox() {
      $mailbox_data = $this->request->post('mailbox');
      if (!is_array($mailbox_data)) {
        $default_email_address = $this->request->get('default_email_address');
        $mailbox_data = array(
          'accept_all_registered' => false,
          'mailbox' => 'INBOX',
          'from_email' => $default_email_address,
          'type' => MM_SERVER_TYPE_POP3,
          'port' => 110,
        );
      } else {
        $mailbox_data['accept_all_registered'] = array_var($mailbox_data, 'accept_all_registered', null) > 0;
        $mailbox_data['accept_anonymous'] = array_var($mailbox_data, 'accept_anonymous', null) > 0;
      } // if
           
      $this->wireframe->addPageMessage(lang("Incoming mail module will import all emails from your mailbox, no matter if they are read or unread. If you find archived email in this mailbox valuable to you, please don't use it for incoming mailbox, because your email will be lost."), PAGE_MESSAGE_INFO);
            
      if ($this->request->isSubmitted()) {      
        $this->active_mailbox->setAttributes($mailbox_data);
        $manager = $this->active_mailbox->getMailboxManager();
        $result = $manager->testConnection();
        if (!$result) {
          $this->smarty->assign('errors', $result);
        } else {
          $this->active_mailbox->setEnabled(true);
          $save = $this->active_mailbox->save();
          if (!$save || is_error($save)) {
            $this->smarty->assign('errors', $save);
          } else {
            flash_success(lang('Mailbox for email address :mailbox is added', array('mailbox' => $this->active_mailbox->getFromEmail())));
            $this->redirectTo('incoming_mail_admin');
          } // if
        } // if
      } // if
      
      js_assign('test_mailbox_connection_url', assemble_url('incoming_mail_admin_test_mailbox_connection'));
      
      $this->smarty->assign(array(
        'mailbox_data' => $mailbox_data,
        'test_mailbox_connection_url' => assemble_url('incoming_mail_admin_test_mailbox_connection'),
      ));
    } // add_mailbox
    
    /**
     * Page which displays mailbox activity history
     * 
     * @param void
     * @return void
     */
    function view_mailbox() {
      if ($this->active_mailbox->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      use_model('incoming_mail_activity_logs', INCOMING_MAIL_MODULE);
      
      $this->wireframe->addBreadCrumb(clean($this->active_mailbox->getDisplayName()), $this->active_mailbox->getViewUrl());
      
      $per_page = 50; // mailbox activity per page
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
      
      $only_problematic = (boolean) array_var($_GET,'only_problematic', false);
      
      if ($only_problematic) {
        list($activity_history, $pagination) = IncomingMailActivityLogs::paginateConflictsByMailbox($this->active_mailbox, $page, $per_page);
      } else {
        list($activity_history, $pagination) = IncomingMailActivityLogs::paginateByMailbox($this->active_mailbox, $page, $per_page);
      } // if
      
      $activity_history = group_by_date($activity_history);
      
      $this->smarty->assign(array(
        'activity_history' => $activity_history,
        'pagination' => $pagination,
        'only_problematic' => $only_problematic,
      ));
    } // view_mailbox
    
    /**
     * Page for editing mailbox
     *
     * @param void
     * @return void
     */
    function edit_mailbox() {      
      if ($this->active_mailbox->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
       
      $this->wireframe->addBreadCrumb(clean($this->active_mailbox->getDisplayName()), $this->active_mailbox->getViewUrl());
      
      $mailbox_data = $this->request->post('mailbox');
      if (!is_array($mailbox_data)) {
        $mailbox_data = array(
          "from_email" => $this->active_mailbox->getFromEmail(),
          "from_name" => $this->active_mailbox->getFromName(),
          "object_type" => $this->active_mailbox->getObjectType(),
          "host" => $this->active_mailbox->getHost(),
          "username" => $this->active_mailbox->getUsername(),
          "password" => $this->active_mailbox->getPassword(),
          "type" => $this->active_mailbox->getType(),
          "security" => $this->active_mailbox->getSecurity(),
          "port" => $this->active_mailbox->getPort(),
          "mailbox" => $this->active_mailbox->getMailbox(),
          "project_id" => $this->active_mailbox->getProjectId(),
          "enabled" => $this->active_mailbox->getEnabled(),
          "accept_all_registered" => $this->active_mailbox->getAcceptAllRegistered(),
          "accept_anonymous" => $this->active_mailbox->getAcceptAnonymous(),
        );
      } else {
        $mailbox_data['accept_all_registered'] = array_var($mailbox_data, 'accept_all_registered', null) > 0;
        $mailbox_data['accept_anonymous'] = array_var($mailbox_data, 'accept_anonymous', null) > 0;
      } // if
            
      if ($this->request->isSubmitted()) {
        $this->active_mailbox->setAttributes($mailbox_data);
        $manager = $this->active_mailbox->getMailboxManager();
        $result = $manager->testConnection();
        if (is_error($result)) {
          $this->smarty->assign('errors', $result);
        } else {         
          $save = $this->active_mailbox->save();
          if (!$save || is_error($save)) {
            $this->smarty->assign('errors', $save);
          } else {
            flash_success(lang('Mailbox for email address :mailbox is edited', array('mailbox' => $this->active_mailbox->getFromEmail())));
            $this->redirectTo('incoming_mail_admin');
          } // if
        } // if
      } // if
      
      js_assign('test_mailbox_connection_url', assemble_url('incoming_mail_admin_test_mailbox_connection'));

      $this->smarty->assign(array(
        "mailbox_data" => $mailbox_data,
      ));
    } // edit_mailbox
    
    /**
     * Action for deleting mailbox
     * 
     * @param void
     * @return void
     */
    function delete_mailbox() {
      if ($this->active_mailbox->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if ($this->request->isSubmitted()) {
        $delete = $this->active_mailbox->delete();
        if($delete && !is_error($delete)) {
          flash_success("Mailbox for email address ':name' has been deleted", array('name' => $this->active_mailbox->getFromEmail()));
        } else {
          flash_error("Failed to delete mailbox for email address ':name'", array('name' => $this->active_mailbox->getFromEmail()));
        } // if
        $this->redirectTo('incoming_mail_admin');
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // delete_mailbox
    
    /**
     * Controller action called via ajax to check if user has provided valid mail server connection data
     * 
     * @param void
     * @return void
     *
     */
    function test_mailbox_connection() {      
      $mailbox = array_var($_POST, 'mailbox');
      if (!is_array($mailbox)) {
        $this->httpError(HTTP_ERR_OPERATION_FAILED, lang('No connection parameters provided'), true, 1);            
      } // if
      
      $manager = new PHPImapMailboxManager($mailbox['host'], $mailbox['type'], $mailbox['security'], $mailbox['port'], $mailbox['mailbox'], $mailbox['username'], $mailbox['password']);
      $test = $manager->connect();
      if (is_error($test)) {
        $this->httpError(HTTP_ERR_OPERATION_FAILED, $test->getMessage(), true, true);
      } // if     
      $new_messages = $manager->countUnreadMessages();
      $manager->disconnect();
      echo lang('Successfully conected to server. Found :number unread messages.', array("number"=>$new_messages));
      die();
    } // test_mailbox_connection
    
    /**
     * List messages on server
     *
     * @param void
     * @return null
     */
    function list_messages() {
      $manager = $this->active_mailbox->getMailboxManager();
      $this->wireframe->addBreadCrumb(clean($this->active_mailbox->getFromEmail()), $this->active_mailbox->getViewUrl());
      
      $connection = $manager->connect();
      if (is_error($connection)) {
        $this->wireframe->addPageMessage($connection->getMessage(), PAGE_MESSAGE_ERROR);
      } else {
        $total_emails = $manager->countMessages();      
        $headers = $manager->listMessagesHeaders(1, $total_emails);
        $this->smarty->assign(array(
          'unread_emails' => $manager->countUnreadMessages(),
          'total_emails'  => $total_emails,
          'headers'       => $headers,
        ));
      } // if
      
      $this->smarty->assign(array(
        'connection' => $connection,
      ));
    } // list_messages
    
  }
?>