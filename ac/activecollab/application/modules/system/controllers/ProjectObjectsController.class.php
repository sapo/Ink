<?php

  // We need project controller
  use_controller('project', SYSTEM_MODULE);

  /**
   * Project objects controller
   *
   * Controller that implements actions that are done on object level (like 
   * moving and copying for example)
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectObjectsController extends ProjectController {
    
    /**
     * Name of this controller (underscore)
     *
     * @var string
     */
    var $controller_name = 'project_objects';
    
    /**
     * Selected object
     *
     * @var ProjectObject
     */
    var $active_object;
    
    /**
     * Actions exposed through API
     *
     * @var array
     */
    var $api_actions = array('attachments', 'move_to_trash', 'restore_from_trash', 'complete', 'open', 'star', 'unstar', 'subscribe', 'unsubscribe');
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return ProjectObjectsController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $object_id = (integer) $this->request->getId('object_id');
      if($object_id) {
        $this->active_object = ProjectObjects::findById($object_id);
      } // if
      
      if(instance_of($this->active_object, 'ProjectObject')) {
        $this->active_object->prepareProjectSectionBreadcrumb($this->wireframe);
        $this->wireframe->addBreadCrumb($this->active_object->getName(), $this->active_object->getViewUrl());
      } else {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $this->smarty->assign(array(
        'active_object' => $this->active_object,
        'page_tab' => $this->active_object->getProjectTab(),
      ));
    } // __construct
    
    /**
     * Move object to trash
     *
     * @param void
     * @return null
     */
    function move_to_trash() {
      if(!$this->active_object->canDelete($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $this->executeOnActiveObject('moveToTrash', null, 
        lang(':type ":name" has been moved to Trash', array('type' => $this->active_object->getVerboseType(), 'name' => $this->active_object->getName())), 
        lang('Failed to move :type ":name" to Trash', array('type' => $this->active_object->getVerboseType(true), 'name' => $this->active_object->getName()))
      );
    } // move_to_trash
    
    /**
     * Restore object from trash
     *
     * @param void
     * @return null
     */
    function restore_from_trash() {
      if(!$this->active_object->canDelete($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $this->executeOnActiveObject('restoreFromTrash', null, 
        lang(':type ":name" has been restored', array('type' => $this->active_object->getVerboseType(), 'name' => $this->active_object->getName())), 
        lang('Failed to restore :type ":name"', array('type' => $this->active_object->getVerboseType(true), 'name' => $this->active_object->getName()))
      );
    } // restore_from_trash
    
    /**
     * Move object into a different project
     *
     * @param void
     * @return null
     */
    function move() {
      if(!$this->active_object->canMove($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      if($this->request->isSubmitted()) {
        $project = null;
        
        $project_id = (integer) $this->request->post('move_to_project_id');
        if($project_id) {
          $destination_project = Projects::findById($project_id);
        } // if
        
        if(!instance_of($destination_project, 'Project')) {
          flash_error('Please select destination project');
          $this->redirectToUrl($this->active_object->getMoveUrl());
        } // if
        
        $source_project = $this->active_object->getProject();
        
        $move = $this->active_object->moveToProject($destination_project);
        if($move && !is_error($move)) {
          if(instance_of($source_project, 'Project')) {
            $source_project->refreshTasksCount();
          } // if
          $destination_project->refreshTasksCount();
          
          flash_success('":name" has been successfully moved to ":project" project', array('name' => $this->active_object->getName(), 'project' => $destination_project->getName()));
          $this->redirectToUrl($this->active_object->getViewUrl());
        } else {
          flash_error('Failed to move ":name" to ":project" project', array('name' => $this->active_object->getName(), 'project' => $destination_project->getName()));
          $this->redirectToUrl($this->active_object->getMoveUrl());
        } // if
      } // if
    } // move
    
    /**
     * Copy object into a different project
     *
     * @param void
     * @return null
     */
    function copy() {
      if(!$this->active_object->canCopy($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      if($this->request->isSubmitted()) {
        $project = null;
        
        $project_id = (integer) $this->request->post('copy_to_project_id');
        if($project_id) {
          $destination_project = Projects::findById($project_id);
        } // if
        
        if(!instance_of($destination_project, 'Project')) {
          flash_error('Please select destination project');
          $this->redirectToUrl($this->active_object->getCopyUrl());
        } // if
        
        $copy = $this->active_object->copyToProject($destination_project);
        if(instance_of($copy, 'ProjectObject')) {
          $destination_project->refreshTasksCount();
          
          flash_success('":name" has been successfully copied to ":project" project', array('name' => $this->active_object->getName(), 'project' => $destination_project->getName()));
          $this->redirectToUrl($copy->getViewUrl());
        } else {
          flash_error('Failed to copy ":name" to ":project" project', array('name' => $this->active_object->getName(), 'project' => $destination_project->getName()));
          $this->redirectToUrl($this->active_object->getCopyUrl());
        } // if
      } // if
    } // copy
    
    /**
     * Complete specific object
     *
     * @param void
     * @return null
     */
    function complete() {
      if(!$this->active_object->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      if($this->request->get('ajax_complete_reopen')) {
        if($this->request->isSubmitted()) {
          db_begin_work();
          $action = $this->active_object->complete($this->logged_user);
          if($action && !is_error($action)) {
            db_commit();
          } else {
            db_rollback();
          } // if
        } // if
        
        require_once SYSTEM_MODULE_PATH . '/helpers/function.object_complete.php';
        
        print smarty_function_object_complete(array(
          'object' => $this->active_object, 
          'user' => $this->logged_user
        ), $this->smarty);
        
        die();
      } else {
        $this->executeOnActiveObject('complete', array($this->logged_user, ''), 
          lang(':type ":name" has been completed', array('type' => $this->active_object->getVerboseType(), 'name' => $this->active_object->getName())), 
          lang('Failed to complete :type ":name"', array('type' => $this->active_object->getVerboseType(true), 'name' => $this->active_object->getName()))
        );
      } // if
    } // complete
    
    /**
     * Reopen specific object
     *
     * @param void
     * @return null
     */
    function open() {
      if(!$this->active_object->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      if($this->request->get('ajax_complete_reopen')) {
        if($this->request->isSubmitted()) {
          db_begin_work();
          $action = $this->active_object->open($this->logged_user);
          if($action && !is_error($action)) {
            db_commit();
          } else {
            db_rollback();
          } // if
        } // if
        
        require_once SYSTEM_MODULE_PATH . '/helpers/function.object_complete.php';
        
        print smarty_function_object_complete(array(
          'object' => $this->active_object, 
          'user' => $this->logged_user
        ), $this->smarty);
        
        die();
      } else {
        $this->executeOnActiveObject('open', array($this->logged_user), 
          lang(':type ":name" has been reopened', array('type' => $this->active_object->getVerboseType(), 'name' => $this->active_object->getName())), 
          lang('Failed to open :type ":name"', array('type' => $this->active_object->getVerboseType(true), 'name' => $this->active_object->getName()))
        );
      } // if
    } // open
    
    /**
     * Lock object for comments
     * 
     * @param void
     * @return null
     */
    function lock() {
      if (!$this->active_object->can_have_comments) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if (!$this->active_object->canChangeLockedState($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $this->executeOnActiveObject('lock', array($this->logged_user), 
        lang(':type ":name" has been locked', array('type' => $this->active_object->getVerboseType(), 'name' => $this->active_object->getName())), 
        lang('Failed to lock :type ":name"', array('type' => $this->active_object->getVerboseType(true), 'name' => $this->active_object->getName()))
      );
    } // lock
    
    /**
     * Unlock object for comments
     * 
     * @param void
     * @return null
     */
    function unlock() {
      if (!$this->active_object->can_have_comments) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if (!$this->active_object->canChangeLockedState($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $this->executeOnActiveObject('unlock', array($this->logged_user), 
        lang(':type ":name" has been unlocked', array('type' => $this->active_object->getVerboseType(), 'name' => $this->active_object->getName())), 
        lang('Failed to unlock :type ":name"', array('type' => $this->active_object->getVerboseType(true), 'name' => $this->active_object->getName()))
      );
    } // unlock
    
    /**
     * Mark object as starred
     *
     * @param void
     * @return null
     */
    function star() {
      if($this->request->isAsyncCall()) {
        if($this->request->isSubmitted()) {
          db_begin_work();
          $action = $this->active_object->star($this->logged_user);
          if($action && !is_error($action)) {
            db_commit();
          } else {
            db_rollback();
          } // if
        } // if
        
        require_once SYSTEM_MODULE_PATH . '/helpers/function.object_star.php';
        
        print smarty_function_object_star(array(
          'object' => $this->active_object, 
          'user' => $this->logged_user
        ), $this->smarty);
        
        die();
      } else {
        $this->executeOnActiveObject('star', array($this->logged_user), 
          lang(':type ":name" has been starred', array('type' => $this->active_object->getVerboseType(), 'name' => $this->active_object->getName())), 
          lang('Failed to star :type ":name"', array('type' => $this->active_object->getVerboseType(true), 'name' => $this->active_object->getName()))
        );
      } // if
    } // star
    
    /**
     * Remove star from an object
     *
     * @param void
     * @return null
     */
    function unstar() {
      if($this->request->isAsyncCall()) {
        if($this->request->isSubmitted()) {
          db_begin_work();
          $action = $this->active_object->unstar($this->logged_user);
          if($action && !is_error($action)) {
            db_commit();
          } else {
            db_rollback();
          } // if
        } // if
        
        require_once SYSTEM_MODULE_PATH . '/helpers/function.object_star.php';
        
        print smarty_function_object_star(array(
          'object' => $this->active_object, 
          'user' => $this->logged_user
        ), $this->smarty);
        
        die();
      } else {
        $this->executeOnActiveObject('unstar', array($this->logged_user), 
          lang(':type ":name" has been unstarred', array('type' => $this->active_object->getVerboseType(), 'name' => $this->active_object->getName())), 
          lang('Failed to unstar :type ":name"', array('type' => $this->active_object->getVerboseType(true), 'name' => $this->active_object->getName()))
        );
      } // if
    } // unstar
    
    // ---------------------------------------------------
    //  Manage Attachments
    // ---------------------------------------------------
    
    /**
     * List all attachments, and manage attachments
     *
     * @param void
     * @return null
     */
    function attachments() {
    	if(!$this->active_object->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $attachemts = $this->active_object->getAttachments();
      $this->smarty->assign(array(
        'attachments' => $attachemts,
      ));
      
      if($this->request->isSubmitted()) {
        $async = (boolean) $this->request->get('async');
        
        db_begin_work();
        
        $file = array_shift($_FILES);
        
        $this->active_object->attachUploadedFile($file, $this->logged_user);
        $save = $this->active_object->save();
      	if($save && !is_error($save)){
      	  $attachment = Attachments::findLastByObject($this->active_object);
      	  if(instance_of($attachment, 'Attachment')) {
      	    db_commit();
      	    
      	    if($async) {
        	    $this->smarty->assign(array(
        	      '_attachment' => $attachment,
        	      '_object_attachments_cycle_name' => 'object_attachments_cycle_' . $attachment->getId(),
        	    ));
        	    
        	    // jQuery acts a bit weird here. Insted of providing response as 
              // a string it tries to append it to the BODY so some markup 
              // (tr, td) gets discarded. That is why we need to use temp table 
              // in order to get properly marked-up row
        	    die('<table style="display: none">' . $this->smarty->fetch(get_template_path('_object_attachments_row', 'attachments', RESOURCES_MODULE))) . '</table>';
      	    } elseif($this->request->isApiCall()) {
      	      $this->serveData($attachment, 'attachment');
      	    } else {
      	      flash_success('File ":file" has been added', array('file' => $file['name']));
          	  $this->redirectToUrl($this->active_object->getAttachmentsUrl());
      	    } // if
      	  } // if
      	} // if
      	
      	db_rollback();
      	if($async) {
      	  $this->httpError(HTTP_ERR_OPERATION_FAILED);
      	} elseif($this->request->isApiCall()) {
      	  $this->httpError(HTTP_ERR_OPERATION_FAILED, null, true, true);
      	} else {
          flash_error('File ":file" has not been added', array('file' => $file['name']));
          $this->redirectToUrl($this->active_object->getAttachmentsUrl());
      	} // if
      } else {
        if($this->request->isApiCall()) {
          $this->serveData($attachemts, 'attachments');
        } // if
      } // if
    } // attachments
    
    /**
     * attachments_mass_update
     *
     * @param void
     * @return null
     */
    function attachments_mass_update() {
      if(!$this->active_object->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
    	if($this->request->isSubmitted()) {
    		$action = $this->request->post('action');
    		
    		if(!in_array($action, array('move_to_trash'))) {
    			$this->httpError(HTTP_ERR_BAD_REQUEST, 'Invalid action');
    		} // if
    		
    		$objects = ProjectObjects::findByIds($this->request->post('objects'));
    		
    		$operations_performed = 0;
    		foreach($objects as $object) {
    			if($action == 'move_to_trash') {
    				$operation = $object->moveToTrash();
    			} // if
    			
    			if($operation && !is_error($operation)) {
            $operations_performed++;
          } // if
    		} // foreach
    		db_commit();
        
        if($action == 'move_to_trash') {
          $message = lang(':count objects moved to trash', array('count' => $operations_performed));
        } // if
        
        flash_success($message, null, true);
        $this->redirectToUrl($this->active_object->getAttachmentsUrl());
    	} // if
    } // attachments_mass_update
    
    /**
     * Show people subscribed to a given object
     *
     * @param void
     * @return null
     */
    function subscriptions() {
      $this->skip_layout = $this->request->isAsyncCall();
      
      if(!$this->active_object->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $users = $this->active_project->getUsers();
      if(is_foreachable($users)) {
        $people = array();
        $grouped_users = array();
        
        foreach($users as $user) {
          $company_id = $user->getCompanyId();
          if(!isset($people[$company_id])) {
            $people[$company_id] = array(
              'users' => null,
              'company' => null,
            );
          } // if
          $people[$company_id]['users'][] = $user;
        } // foreach
        
        $companies = Companies::findByIds(array_keys($people));
        foreach($companies as $company) {
          $people[$company->getId()]['company'] = $company;
        } // foreach
        
        $this->smarty->assign('people', $people);
      } else {
        $this->smarty->assign('people', null);
      } // if
    } // subscriptions
    
    /**
     * Subscribe current user to a given object
     *
     * @param void
     * @return null
     */
    function subscribe() {
      if(!$this->active_object->canSubscribe($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $user_id = $this->request->getId('user_id');
      if($user_id) {
        $user = Users::findById($user_id);
        if(!instance_of($user, 'User')) {
          $this->httpError(HTTP_ERR_NOT_FOUND); // user ID provided, but no user found
        } // if
      } else {
        $user = $this->logged_user;
      } // if
      
      if(isset($_GET['async']) && $_GET['async']) {
        if($this->request->isSubmitted()) {
          db_begin_work();
          $action = $this->active_object->subscribe($user);
          if($action && !is_error($action)) {
            db_commit();
          } else {
            db_rollback();
          } // if
        } // if
        
        require_once RESOURCES_MODULE_PATH . '/helpers/function.object_subscription.php';
        print smarty_function_object_subscription(array(
          'object' => $this->active_object,
          'user' => $user,
          'render_wrapper' => false
        ), $this->smarty);
        die();
      } else {
        $this->executeOnActiveObject('subscribe', array($user), 
          lang('You are subscribed to ":name" :type now', array('type' => $this->active_object->getVerboseType(true), 'name' => $this->active_object->getName())), 
          lang('Failed to subscribe you to ":name" :type', array('type' => $this->active_object->getVerboseType(true), 'name' => $this->active_object->getName()))
        );
      } // if
    } // subscribe
    
    /**
     * Unsubscribe current user to a given object
     *
     * @param void
     * @return null
     */
    function unsubscribe() {
      if(!$this->active_object->canSubscribe($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $user_id = $this->request->getId('user_id');
      if($user_id) {
        $user = Users::findById($user_id);
        if(!instance_of($user, 'User')) {
          $this->httpError(HTTP_ERR_NOT_FOUND); // user ID provided, but no user found
        } // if
      } else {
        $user = $this->logged_user;
      } // if
      
      if(isset($_GET['async']) && $_GET['async']) {
        if($this->request->isSubmitted()) {
          db_begin_work();
          $action = $this->active_object->unsubscribe($user);
          if($action && !is_error($action)) {
            db_commit();
          } else {
            db_rollback();
          } // if
        } // if
        
        require_once RESOURCES_MODULE_PATH . '/helpers/function.object_subscription.php';
        print smarty_function_object_subscription(array(
          'object' => $this->active_object,
          'user' => $user,
          'render_wrapper' => false
        ), $this->smarty);
        die();
      } else {
        $this->executeOnActiveObject('unsubscribe', array($user), 
          lang('You are no longer subscribed to ":name" :type', array('type' => $this->active_object->getVerboseType(true), 'name' => $this->active_object->getName())), 
          lang('Failed to unsubscribe you from ":name" :type', array('type' => $this->active_object->getVerboseType(true), 'name' => $this->active_object->getName()))
        );
      } // if
    } // unsubscribe
    
    /**
     * Unsubscribe specific user from a given object
     *
     * @param void
     * @return null
     */
    function unsubscribe_user() {
      if(!$this->active_object->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if($this->request->isSubmitted()) {
        $user = null;
        $user_id = (integer) $this->request->get('user_id');
        if($user_id) {
          $user = Users::findById($user_id);
        } // if
        
        if(!instance_of($user, 'User')) {
          $this->httpError(HTTP_ERR_NOT_FOUND);
        } // if
        
        $subscription = Subscriptions::findById(array(
          'user_id' => $user->getId(),
          'parent_id' => $this->active_object->getId(),
        ));
        
        if(!instance_of($subscription, 'Subscription')) {
          $this->httpError(HTTP_ERR_NOT_FOUND);
        } // if
        
        $delete = $subscription->delete();
        if($delete && !is_error($delete)) {
          flash_success(':user_name has been unsubscribed from :object_name :object_type', array(
            'user_name' => $user->getDisplayName(),
            'object_name' => $this->active_object->getName(),
            'object_type' => $this->active_object->getVerboseType(true),
          ));
        } else {
          flash_error('Failed to unsubscribe :user_name from :object_name :object_type', array(
            'user_name' => $user->getDisplayName(),
            'object_name' => $this->active_object->getName(),
            'object_type' => $this->active_object->getVerboseType(true),
          ));
        } // if
        
        $this->redirectToReferer($this->active_object->getSubscriptionsUrl());
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // unsubscribe_user
    
    /**
     * Show object visibility
     *
     * @param void
     * @return null
     */
    function visibility() {
    	$this->skip_layout = $this->request->isAsyncCall();
    	
    	if($this->active_object->getVisibility() <= VISIBILITY_PRIVATE) {
    	  $this->smarty->assign('private_roles', who_can_see_private_objects());
    	} // if
    } // visibility
    
    /**
     * Call object function and server result to client
     * 
     * Most of the actions in this controller look the same. This simple 
     * implementation holds behavior that is same for almost all the actions. 
     * Copying is bad :)
     * 
     * $success_message and $error_message are language patters. Variables that 
     * are provided by this functions to the patterns are:
     * 
     * - name - object name
     * - type - object ype
     *
     * @param string $method
     * @param array $params
     * @param string $success_message
     * @param string $error_message
     * @return null
     */
    function executeOnActiveObject($method, $params = null, $success_message = '', $error_message = '') {
      if(empty($method) || empty($success_message) || empty($error_message)) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        if(is_array($params)) {
          $action = call_user_func_array(array(&$this->active_object, $method), $params);
        } else {
          $action = call_user_func(array(&$this->active_object, $method));
        } // if
        
        if($action && !is_error($action)) {
          db_commit();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            if($this->request->get('async')) {
              $this->httpOk();
            } // if
            
            flash_success($success_message, null, true);
            $this->redirectToReferer($this->active_object->getViewUrl());
          } else {
            $this->serveData($this->active_object, strtolower($this->active_object->getType()));
          } // if
        } else {
          db_rollback();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_error($error_message, null, true);
            $this->redirectToReferer($this->active_object->getViewUrl());
          } else {
            $this->httpError(HTTP_ERR_OPERATION_FAILED, null, true, $this->request->isApiCall());
          } // if
        } // if
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST, null, true, $this->request->isApiCall());
      } // if
      
      die(); // just in case! :)
    } // executeOnActiveObject
  
  }

?>