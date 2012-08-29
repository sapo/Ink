<?php

  /**
   * Reminders related actions
   *
   * @package activeCollab.modules.resources
   * @subpackage controllers
   */
  class RemindersController extends ApplicationController {
    
    /**
     * Selected reminder
     *
     * @var Reminder
     */
    var $active_reminder = null;
    
    /**
     * Construct reminder controller
     *
     * @param Request $request
     * @return RemindersController
     */
    function __construct($request) {
    	parent::__construct($request);
    	
    	$reminder_id = $this->request->getId('reminder_id');
    	if($reminder_id) {
    	  $this->active_reminder = Reminders::findById($reminder_id);
    	} else {
    	  $this->active_reminder = new Reminder();
    	} // if
    	
    	$this->smarty->assign(array(
    	  'active_reminder' => $this->active_reminder,
    	));
    } // __construct
    
    /**
     * List active reminders for user
     * 
     * @param void
     * @return null
     *
     */
    function index() {
      $this->smarty->assign(array(
        'active_reminders' => Reminders::findActiveByUser($this->logged_user)
      ));
    } // index
    
    /**
     * Create a new reminder
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      $parent = ProjectObjects::findById($this->request->getId('parent_id'));
    	if(!instance_of($parent, 'ProjectObject')) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	$project = $parent->getProject();
    	if(!instance_of($project, 'Project')) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	$assignees = $parent->getAssignees();
    	$subscribers = $parent->getSubscribers();
    	$commenters = Comments::findCommenters($parent, $this->logged_user);
    	
    	$reminder_data = $this->request->post('reminder');
    	if(!is_array($reminder_data)) {
    	  $who = 'user';
    	  if(is_foreachable($assignees)) {
    	    $who = 'assignees';
    	  } elseif(is_foreachable($subscribers)) {
    	    $who = 'subscribers';
    	  } elseif(is_foreachable($commenters)) {
    	    $who = 'commenters';
    	  } // if
    	  
    	  $reminder_data = array('who' => $who);
    	} // if
    	
    	$this->smarty->assign(array(
    	  'parent'        => $parent,
    	  'assignees'     => $assignees,
    	  'subscribers'   => $subscribers,
    	  'commenters'    => $commenters,
    	  'project_users' => ProjectUsers::findUserIdsByProject($project),
    	  'reminder_data' => $reminder_data
    	));
    	
    	if($this->request->isSubmitted()) {
    	  $send_to_users = null;
    	  
    	  switch($reminder_data['who']) {
    	    case 'assignees':
    	      $send_to_users = $assignees;
    	      break;
    	    case 'subscribers':
    	      $send_to_users = $subscribers;
    	      break;
    	    case 'commenters':
    	      $send_to_users = $commenters;
    	      break;
    	    case 'user':
    	      $user_id = (integer) array_var($reminder_data, 'user_id');
    	      if($user_id) {
    	        $user = Users::findById($user_id);
    	        if(instance_of($user, 'User')) {
    	          $send_to_users = array($user);
    	        } // if
    	      } // if
    	      break;
    	  } // switch
    	  
    	  // Do reminder
    	  if(is_foreachable($send_to_users)) {
    	    
    	    $comment = trim(array_var($reminder_data, 'comment'));
    	    if($comment) {
    	      require_once SMARTY_PATH . '/plugins/modifier.clickable.php';
      	    require_once ANGIE_PATH . '/classes/htmlpurifier/init.php';
      	    
      	    $comment = strip_tags(prepare_html($comment, true)); // make sure we have clean text
      	    $comment = nl2br(smarty_modifier_clickable($comment)); // preserve breaklines and convert links
    	    } // if
    	    
    	    db_begin_work();
    	    
    	    $reminders_sent = array();
    	    foreach($send_to_users as $user) {
    	      $reminder = new Reminder();
    	      
    	      $reminder->setAttributes(array(
    	        'user_id'   => $user->getId(),
    	        'object_id' => $parent->getId(),
    	        'comment'   => $comment,
    	      ));
    	      $reminder->setCreatedBy($this->logged_user);
    	      
    	      $save = $reminder->save();
    	      if($save && !is_error($save)) {
    	        $reminders_sent[] = $user->getDisplayName();
    	        ApplicationMailer::send($user, 'system/reminder', array(
        	      'reminded_by_name'  => $this->logged_user->getDisplayName(),
        	      'reminded_by_url'   => $this->logged_user->getViewUrl(),
        	      'object_name'       => $parent->getName(),
        	      'object_url'        => $parent->getViewUrl(),
        	      'object_type'       => strtolower($parent->getType()),
        	      'comment_body'      => $comment,
        	      'project_name'      => $project->getName(),
        	      'project_url'       => $project->getOverviewUrl(),
              ), $parent);
    	      } // if
    	    } // foreach
    	    
    	    db_commit();
    	    
    	    $message = lang('Users reminded: :users', array('users' => implode(', ', $reminders_sent)));
    	    
          if($this->request->get('skip_layout')) {
    	      $this->renderText($message);
    	    } else {
    	      flash_success($message);
    	      $this->redirectToUrl($parent->getViewUrl());
    	    } // if
    	    
    	  // No reminders
    	  } else {
    	    if($this->request->get('skip_layout')) {
    	      $this->renderText(lang('0 users reminded'));
    	    } else {
    	      flash_success('0 users reminded');
    	      $this->redirectToUrl($parent->getViewUrl());
    	    } // if
    	  } // if
    	} // if
    } // add
    
    /**
     * Dismiss reminder
     *
     * @param void
     * @return null
     */
    function dismiss() {
    	if($this->active_reminder->isNew()) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_reminder->canDismiss($this->logged_user)) {
    	  $this->httpError(HTTP_ERR_FORBIDDEN);
    	} // if
    	
    	if($this->request->isSubmitted()) {
    	  if($this->active_reminder->delete()) {
    	    if($this->request->isAsyncCall()) {
      	   $this->httpOk();
    	    } else {
    	     flash_success('Selected reminder has been dismissed'); 
    	    } // if
      	} else {
    	    if ($this->request->isAsyncCall()) {
      	   $this->httpError(HTTP_ERR_OPERATION_FAILED);
    	    } else {
    	     flash_error('Failed to dismiss selected reminder');
    	    } // if
      	} // if
      	
      	$this->redirectToReferer(assemble_url('homepage'));
    	} else {
    	  $this->httpError(HTTP_ERR_BAD_REQUEST);
    	} // if
    } // dismiss
    
  }

?>