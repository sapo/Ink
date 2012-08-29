<?php

  // We need projects controller
  use_controller('application', SYSTEM_MODULE);

  /**
   * Incoming Mail Frontend controller
   *
   * @package activeCollab.modules.incoming_mail
   * @subpackage controllers
   */
  class IncomingMailFrontendController extends ApplicationController {
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = INCOMING_MAIL_MODULE;
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'incoming_mail_frontend';
    
    /**
     * Active incoming mail
     *
     * @var IncomingMail
     */
    var $active_mail;
    
    /**
     * Constructor method
     *
     * @param string $request
     * @return StatusController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if(!$this->logged_user->isAdministrator() && !$this->logged_user->getSystemPermission('can_use_incoming_mail_frontend')) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->wireframe->addBreadCrumb(lang('Incoming Mail'), assemble_url('incoming_mail'));
      $this->wireframe->current_menu_item = 'incoming_mail';
      
      require_once ANGIE_PATH . '/classes/UTF8Converter/init.php';
      require_once ANGIE_PATH . '/classes/mailboxmanager/init.php';
      
      use_model('incoming_mail_activity_logs', INCOMING_MAIL_MODULE);
      
      $this->active_mail = IncomingMails::findById($this->request->getId('mail_id'));
      if (!instance_of($this->active_mail, 'IncomingMail')) {
        $this->active_mail = new IncomingMail();
      } else {
        $this->wireframe->addBreadCrumb($this->active_mail->getSubject(), $this->active_mail->getImportUrl());
      } // if
      
      $this->wireframe->print_button = false;
      
      $this->smarty->assign(array(
        'active_mail' => $this->active_mail,
      ));
    } // __construct
    
    /**
     * Index page action
     * 
     * @param void
     * @return void
     */
    function index() {
      $per_page = 15; // emails per page
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
        
      list($incoming_mails, $pagination) = IncomingMails::paginatePending($page, $per_page);
      
      $this->smarty->assign(array(
        'incoming_mails'                =>  $incoming_mails,
        'pagination'                    =>  $pagination,
        'mass_conflict_resolution_url'  =>  assemble_url('incoming_mail_mass_conflict_resolution')
      ));
      
      js_assign('additional_fields_url', assemble_url('incoming_mail_additional_form_fields'));
    } // index
    
     /**
     * Provide ajax functionality for menu badge
     * 
     * @param void
     * @return void
     */
    function count_pending() {
      echo IncomingMails::countPending();
      die();
    } // count_pending
    
    /**
     * Action to delete incoming mail
     *
     * @param void
     * @return void
     */
    function delete() {
      if (!instance_of($this->active_mail, 'IncomingMail')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->request->isSubmitted()) {
        $delete = $this->active_mail->delete();
        
        if($delete && !is_error($delete)) {
          flash_success("Email has been deleted");
        } else {
          flash_error("Failed to delete email");
        } // if
        $this->redirectTo('incoming_mail');
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // delete
    
    /**
     * Conflict incoming mail
     * 
     * @param void
     * @return void
     */
    function conflict() {
      if ($this->active_mail->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      require_once(INCOMING_MAIL_MODULE_PATH.'/models/IncomingMailImporter.class.php');
      
      $mail_data = $this->request->post('mail');
      if (!is_foreachable($mail_data)) {
        flash_error(incoming_mail_module_get_status_description($this->active_mail->getState()));
        $mail_data = array(
          'subject' => $this->active_mail->getSubject(),
          'body' => $this->active_mail->getBody(),
          'created_by_id' => $this->active_mail->getCreatedById(),
          'project_id' => $this->active_mail->getProjectId(),
        );
      } // if
      
      if ($this->request->isSubmitted()) {
        $this->active_mail->setSubject(array_var($mail_data, 'subject'));
        $this->active_mail->setBody(array_var($mail_data, 'body'));
        
        $creator_id = array_var($mail_data, 'created_by_id');
        if ($creator_id && ($creator_id != 'original_author')) {
            $creator = Users::findById($creator_id);
            if (instance_of($creator,'User')) {
              $this->active_mail->setCreatedBy($creator);
            } // if
        } // if
        
        $this->active_mail->setCreatedById(array_var($mail_data, 'created_by_id'));
        $this->active_mail->setObjectType(array_var($mail_data, 'object_type'));
        if (array_var($mail_data, 'object_type') == 'comment') {
          $this->active_mail->setParentId(array_var($mail_data, 'parent_id'));
        } // if
               
        // import email
        if (instance_of($importing_result = IncomingMailImporter::importPendingEmail($this->active_mail, $creator_id == 'original_author'), 'ProjectObject')) {
          // we have successfully imported email
          $this->active_mail->delete();
          if ($this->request->isAsyncCall()) {
            $this->renderText(lang('<p>Conflict Solved Successfully!</p><p>View created <a href=":url">:object</a>.</p>', array('object' => $this->active_mail->getObjectType(), 'url' => $importing_result->getViewUrl())));
          } else {
            flash_success('Conflict Solved Successfully!');
            $this->redirectTo('incoming_mail');
          } // if
         } else {
          if ($this->request->isAsyncCall()) {
            $this->httpError(HTTP_ERR_INVALID_PROPERTIES, null, false, 2);
          } else {
            flash_error($importing_result->getMessage());
          } // if
        } // if
      } // if

      $user = $this->active_mail->getCreatedBy();
      if (instance_of($user, 'User')) {
        $this->smarty->assign('object_user', $user);
      } else {
        $this->smarty->assign('object_user', $this->logged_user);
      } // if
      
      $this->smarty->assign(array(
        'async' => $this->request->isAsyncCall(),
        'form_url' => $this->active_mail->getImportUrl().($this->request->isAsyncCall() ? '?skip_layout=1&async=1' : ''),
        'status_message' => incoming_mail_module_get_status_description($this->active_mail->getState()),
        'mail_data' => $mail_data,
        'project' => $this->active_mail->getProject(),
      ));
      
      $flash =& Flash::instance();
      $flash->init();
      js_assign('additional_fields_url', assemble_url('incoming_mail_additional_form_fields'));
    } // conflict
    
    /**
     * Returns additional fields for import for, dependable of provided project id
     * 
     * this is ajax request
     *
     */
    function conflict_form_additional_fields() {
      if (!$this->request->isAsyncCall()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      $project = Projects::findById($this->request->get('project_id'));
      if (!instance_of($project,'Project')) {
        echo lang('Project does not exists');
        die();
      } // if
      
      $form_data = array(
        'user_id' => $this->request->get('user_id'),
        'object_type' => $this->request->get('object_type')
      );
      
      $this->smarty->assign(array(
        'object_type' => $this->request->get('object_type'),
        'project' => $project,
        'form_data' => $form_data,
      ));      
    } // conflict_form_additional_fields
    
    /**
     * Tool for mass conflict resolution of selected emails
     * 
     * @param void
     * @return null
     */
    function mass_conflict_resolution() {
      if (!$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      $action = $this->request->post('with_selected');
      if(trim($action) == '') {
        flash_error('Please select what you want to do with selected conflicts');
        $this->redirectToReferer(assemble_url('incoming_mail'));
      } // if
      
      $conflict_ids = $this->request->post('conflicts');
      $conflicts = IncomingMails::findByIds($conflict_ids);
      
      if (!is_foreachable($conflicts)) {
        flash_error("You didn't selected any conflicts for resolution");
        $this->redirectToReferer(assemble_url('incoming_mail'));
      } // if
      
      $updated = 0;
      switch ($action) {
      	case 'delete':
	        foreach ($conflicts as $conflict) {
      		  $delete = $conflict->delete();
      		  if ($delete && !is_error($delete)) {
      		    $updated ++;
      		  } // if
	        } // foreach
	        $message = ':count conflicts removed';
      		break;
      
      	default:
      		break;
      } // switch
      
      flash_success($message, array('count' => $updated));
      $this->redirectToReferer(assemble_url('incoming_mail'));
    } // mass_conflict_resolution
    
  } // IncomingMailFrontendController
?>