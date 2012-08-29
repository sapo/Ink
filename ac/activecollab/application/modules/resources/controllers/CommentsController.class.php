<?php

  // We need projects controller
  use_controller('project', SYSTEM_MODULE);

  /**
   * Comments controller
   *
   * @package activeCollab.modules.resources
   * @subpackage controllers
   */
  class CommentsController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = RESOURCES_MODULE;
    
    /**
     * Active comment
     * 
     * @var Comment
     */
    var $active_comment;
    
    /**
     * API actions
     *
     * @var array
     */
    var $api_actions = array('view', 'add', 'edit');
    
    /**
     * Construct comments controller
     *
     * @param Request $request
     * @return CommentsController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $comment_id = $this->request->getId('comment_id');
      if($comment_id > 0) {
        $this->active_comment = ProjectObjects::findById($comment_id);
      } // if
      
      if(!instance_of($this->active_comment, 'Comment')) {
        $this->active_comment = new Comment();
      } // if
      
      $this->smarty->assign(array(
        'active_comment' => $this->active_comment,
        'page_tab' => $this->active_comment->getProjectTab()
      ));
    } // __construct
    
    /**
     * View single comment
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_comment->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if(!$this->active_comment->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      if($this->request->isApiCall()) {
        $this->serveData($this->active_comment, 'comment');
      } else {
        $this->redirectToUrl($this->active_comment->getRealViewUrl());
      } // if
    } // view
    
    /**
     * Create new comment
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      $active_object = ProjectObjects::findById($this->request->getId('parent_id'));
      if(!instance_of($active_object, 'ProjectObject')) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if(!$active_object->canComment($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $active_object->prepareProjectSectionBreadcrumb($this->wireframe);
      $this->wireframe->addBreadCrumb($active_object->getName(), $active_object->getViewUrl());
      
      if(!$active_object->canComment($this->logged_user)) {
        if($this->request->isApiCall()) {
          $this->httpError(HTTP_ERR_FORBIDDEN, null, true, true);
        } else {
          flash_error('Parent object not found');
          $this->redirectToReferer($this->active_project->getOverviewUrl());
        } // if
      } // if
      
      $comment_data = $this->request->post('comment');
      
      $this->smarty->assign(array(
        'active_object'   => $active_object,
        'page_tab'        => $active_object->getProjectTab(),
        'comment_data'    => $comment_data,
        'recent_comments' => Comments::findRecentObject($active_object, 5, STATE_VISIBLE, $this->logged_user->getVisibility()),
      ));
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $complete_parent_object = (boolean) array_var($comment_data, 'complete_parent_object');
        
        $this->active_comment = new Comment();
        $this->active_comment->log_activities = false;
        
        if($complete_parent_object) {
          $this->active_comment->send_notification = false;
        } // if
        
        attach_from_files($this->active_comment, $this->logged_user);
        
        $this->active_comment->setAttributes($comment_data);
        $this->active_comment->setParent($active_object);
        $this->active_comment->setProjectId($this->active_project->getId());
        $this->active_comment->setState(STATE_VISIBLE);
        $this->active_comment->setVisibility($active_object->getVisibility());
        $this->active_comment->setCreatedBy($this->logged_user);
        
        $save = $this->active_comment->save();
        if($save && !is_error($save)) {
          $active_object->subscribe($this->logged_user);
          
          $activity = new NewCommentActivityLog();
          $activity->log($this->active_comment, $this->logged_user);
          
          if($complete_parent_object && $active_object->canChangeCompleteStatus($this->logged_user)) {
            $active_object->complete($this->logged_user, $this->active_comment->getFormattedBody(true));
          } // if
          
          db_commit();
          $this->active_comment->ready();
          
          if($this->request->isApiCall()) {
            $this->serveData($this->active_comment, 'comment');
          } else {           
            flash_success('Comment successfully posted');
            $this->redirectToUrl($this->active_comment->getRealViewUrl());
          } // if
        } else {
          db_rollback();
          
          if($this->request->isApiCall()) {
            $this->serveData($save);
          } else {
            $this->smarty->assign('errors', $save);
          } // if
        } // if
      } else {
        if($this->request->isApiCall()) {
          $this->httpError(HTTP_ERR_BAD_REQUEST, null, true, true);
        } // if
      } // if
    } // add
    
    /**
     * Update an existing comment
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->active_comment->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_comment->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $parent = $this->active_comment->getParent();
      if(!instance_of($parent, 'ProjectObject')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $parent->prepareProjectSectionBreadcrumb($this->wireframe);
      $this->wireframe->addBreadCrumb($parent->getName(), $parent->getViewUrl());
      
      $comment_data = $this->request->post('comment');
      if(!is_array($comment_data)) {
        $comment_data = array(
          'body' => $this->active_comment->getBody(),
        );
      } // if
      
      $this->smarty->assign('comment_data', $comment_data);
      
      if($this->request->isSubmitted()) {
        $this->active_comment->setAttributes($comment_data);
        $save = $this->active_comment->save();
        
        if($save && !is_error($save)) {
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success('Comment has been updated');
            $this->redirectToUrl($this->active_comment->getRealViewUrl());
          } else {
            $this->serveData($this->active_comment, 'comment');
          } // if
        } else {
          if($this->request->getFormat() == FORMAT_HTML) {
            $this->smarty->assign('errors', $save);
          } else {
            $this->serveData($save);
          } // if
        } // if
      } // if
    } // edit
    
  }

?>