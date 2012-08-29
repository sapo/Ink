<?php

  // Projects controller is required
  use_controller('project', SYSTEM_MODULE);

  /**
   * Tasks controller
   * 
   * @package activeCollab.modules.resources
   * @subpackage controllers
   */
  class TasksController extends ProjectController {
  
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = RESOURCES_MODULE;
    
    /**
     * Selected task
     *
     * @var Task
     */
    var $active_task;
    
    /**
     * Parent object of active task
     *
     * @var ProjectObject
     */
    var $active_task_parent;
    
    /**
     * List of available API actions
     *
     * @var array
     */
    var $api_actions = array('view', 'add', 'edit');
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return TasksController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $task_id = $this->request->getId('task_id');
      if($task_id) {
        $this->active_task = Tasks::findById($task_id);
      } // if
      
      if(instance_of($this->active_task, 'Task')) {
        $this->active_task_parent = $this->active_task->getParent();
        if(instance_of($this->active_task_parent, 'ProjectObject')) {
          $this->active_task_parent->prepareProjectSectionBreadcrumb($this->wireframe);
        } // if
      } else {
        $this->active_task = new Task();
        
        $parent_id = $this->request->getId('parent_id');
        if($parent_id) {
          $parent = ProjectObjects::findById($parent_id);
          if(instance_of($parent, 'ProjectObject')) {
            $this->active_task_parent = $parent;
            $this->active_task_parent->prepareProjectSectionBreadcrumb($this->wireframe);
          } // if
        } // if
      } // if
      
      if(instance_of($this->active_task_parent, 'ProjectObject')) {
        $this->wireframe->addBreadCrumb($this->active_task_parent->getName(), $this->active_task_parent->getViewUrl());
      } else {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $this->smarty->assign(array(
        'active_task' => $this->active_task,
        'active_task_parent' => $this->active_task_parent,
        'page_tab' => $this->active_task->getProjectTab()
      ));
    } // __construct
    
    /**
     * View task URL (redirects to parent object)
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_task->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if(empty($this->active_task_parent)) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if($this->request->isApiCall()) {
        $this->serveData($this->active_task, 'task');
      } else {
        $this->redirectToUrl($this->active_task_parent->getViewUrl() . '#task' . $this->active_task->getId());
      } // if
    } // view
    
    /**
     * Show and process add task form
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      if(!instance_of($this->active_task_parent, 'ProjectObject')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_task_parent->canSubtask($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $task_data = $this->request->post('task');
      $this->smarty->assign(array(
        'task_data' => $task_data,
        'page_tab' => $this->active_task_parent->getProjectTab(),
      ));
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_task = new Task(); // just in case...
        $this->active_task->log_activities = false;
        
        $this->active_task->setAttributes($task_data);
        $this->active_task->setParent($this->active_task_parent);
        $this->active_task->setProjectId($this->active_project->getId());
        $this->active_task->setCreatedBy($this->logged_user);
        $this->active_task->setState(STATE_VISIBLE);
        $this->active_task->setVisibility($this->active_task_parent->getVisibility());
        
        $save = $this->active_task->save();
        if($save && !is_error($save)) {
          $subscribers = array($this->logged_user->getId());
          if(is_foreachable(array_var($task_data['assignees'], 0))) {
            $subscribers = array_merge($subscribers, array_var($task_data['assignees'], 0));
          } else {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          
          if(!in_array($this->active_project->getLeaderId(), $subscribers)) {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          
          Subscriptions::subscribeUsers($subscribers, $this->active_task);
          
          $activity = new NewTaskActivityLog();
          $activity->log($this->active_task, $this->logged_user);
          
          db_commit();
          $this->active_task->ready();
          
          if($this->request->isApiCall()) {
            $this->serveData($this->active_task, 'task');
          } elseif($this->request->isAsyncCall()) {
            $this->smarty->assign(array(
              '_object_task' => $this->active_task,
            ));              
            print tpl_fetch(get_template_path('_task_opened_row', $this->controller_name, RESOURCES_MODULE));
            die();
          } else {
            flash_success('Task ":name" has been added', array('name' => str_excerpt($this->active_task->getBody(), 80, '...')), false, false);
            $this->redirectToUrl($this->active_task_parent->getViewUrl());
          } // if
        } else {
          db_rollback();
          
          if($this->request->isApiCall() || $this->request->isAsyncCall()) {
            $this->serveData($save);
          } else {
            $this->smarty->assign('errors', $save);
          } // if
        } // if
      } else {
        if($this->request->isApiCall()) {
          $this->httpError(HTTP_ERR_BAD_REQUEST);
        } // if
      } // if
    } // add
    
    /**
     * Show and process edit task form
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->active_task->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if(empty($this->active_task_parent)) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if(!$this->active_task->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $task_data = $this->request->post('task');
      if(!is_array($task_data)) {
        $task_data = array(
          'body' => $this->active_task->getBody(),
          'priority' => $this->active_task->getPriority(),
          'due_on' => $this->active_task->getDueOn(),
          'assignees' => Assignments::findAssignmentDataByObject($this->active_task),
        );
      } // if
      
      $this->smarty->assign('task_data', $task_data);
      
      if($this->request->isSubmitted()) {
        if(!isset($task_data['assignees'])) {
          $task_data['assignees'] = array(array(), 0);
        } // if
        
        db_begin_work();
        $old_name = $this->active_task->getBody();
        
        $this->active_task->setAttributes($task_data);
        $save = $this->active_task->save();
        
        if($save && !is_error($save)) {
          db_commit();
          
          if($this->request->isApiCall()) {
            $this->serveData($this->active_task, 'task');
          } else {
            flash_success('Task ":name" has been updated', array('name' => str_excerpt($old_name, 80, '...')), false, false);
            $this->redirectToUrl($this->active_task_parent->getViewUrl());
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
    } // edit
    
    /**
     * Complete specific object
     *
     * @param void
     * @return null
     */
    function complete() {
      if($this->active_task->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_task->canChangeCompleteStatus($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $action = $this->active_task->complete($this->logged_user);
        if($action && !is_error($action)) {
          db_commit();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            if($this->request->get('async')) {
              $this->smarty->assign(array(
                '_object_task' => $this->active_task,
              ));              
              print tpl_fetch(get_template_path('_task_completed_row', $this->controller_name, RESOURCES_MODULE));
              die();
            } else {
              flash_success('Task ":name" has been completed', array('name' => str_excerpt($this->active_task->getName(), 80, '...')));
              $this->redirectToReferer($this->active_task->getViewUrl());
            } // if
          } else {
            $this->serveData($this->active_task);
          } // if
        } else {
          db_rollback();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            if($this->request->get('async')) {
              $this->serveData($action);
            } else {
              flash_error('Failed to complete task ":name"', array('name' => str_excerpt($this->active_task->getName(), 80, '...')));
              $this->redirectToReferer($this->active_task->getViewUrl());
            } // if
          } else {
            $this->httpError(HTTP_ERR_OPERATION_FAILED);
          } // if
        } // if
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // complete
    
    /**
     * Reopen specific object
     *
     * @param void
     * @return null
     */
    function open() {
      if($this->active_task->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_task->canChangeCompleteStatus($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $action = $this->active_task->open($this->logged_user);
        if($action && !is_error($action)) {
          db_commit();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            if($this->request->get('async')) {
              $this->smarty->assign(array(
                '_object_task' => $this->active_task,
              ));              
              print tpl_fetch(get_template_path('_task_opened_row', $this->controller_name, RESOURCES_MODULE));
              die();
            } else {
              flash_success('Task ":name" has been opened', array('name' => str_excerpt($this->active_task->getName(), 80, '...')));
              $this->redirectToReferer($this->active_task->getViewUrl());
            } // if
          } else {
            $this->serveData($this->active_task);
          } // if
        } else {
          db_rollback();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            if($this->request->get('async')) {
              $this->serveData($action);
            } else {
              flash_error('Failed to open task ":name"', array('name' => str_excerpt($this->active_task->getName(), 80, '...')));
              $this->redirectToReferer($this->active_task->getViewUrl());
            } // if
          } else {
            $this->httpError(HTTP_ERR_OPERATION_FAILED);
          } // if
        } // if
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // open
    
    /**
     * Show and process reorder task form
     *
     * @param void
     * @return null
     */
    function reorder() {
      $this->wireframe->print_button = false;
      
      if(!instance_of($this->active_task_parent, 'ProjectObject')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_task_parent->canSubtask($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $order_data = $this->request->post('task');
      $ids = array_keys($order_data);
      if (is_foreachable($order_data)) {
      	$x = 1;
        foreach ($order_data as $key=>$value) {
        	$order_data[$key] = $x;
        	$x++;
        } // foreach
      } // if
      
      $tasks = Tasks::findByIds($ids, STATE_VISIBLE, $this->logged_user->getVisibility());
      if (is_foreachable($tasks)) {
        foreach ($tasks as $task) {
          $task->setParent($this->active_task_parent);
          $task->setProjectId($this->active_task_parent->getProjectId());
          $task->setVisibility($this->active_task_parent->getVisibility());
          $task->setPosition(array_var($order_data, $task->getId()));
          $task->save();
        } // foreach
      } // if
      $this->httpOk();
    } // reorder
    
    /**
     * Function used only via ajax to return all completed tasks
     * 
     * @param void
     * @return null
     */
    function list_completed() {
      if (!$this->request->isAsyncCall()) {
        $this->redirectToReferer('dashboard');
      } // if
      
      $completed_tasks = $this->active_task_parent->getCompletedTasks();
      $this->smarty->assign(array(
        'completed_tasks' => $completed_tasks
      ));      
    } // list_completed
  
  } // TasksController

?>