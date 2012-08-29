<?php

  // Foundation...
  use_controller('project', SYSTEM_MODULE);

  /**
   * Checklists controller
   *
   * @package activeCollab.modules.checklists
   * @subpackage controllers
   */
  class ChecklistsController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = CHECKLISTS_MODULE;
    
    /**
     * Selected checklist
     *
     * @var Checklist
     */
    var $active_checklist;
    
    /**
     * Array of actions available through API
     *
     * @var array
     */
    var $api_actions = array('index', 'archive', 'view', 'add', 'edit');
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return ChecklistsController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if($this->logged_user->getProjectPermission('checklist', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $checklists_url = checklists_module_url($this->active_project);
      $checklists_archive_url = checklists_module_archive_url($this->active_project);
      $add_checklist_url = false;
      
      $this->wireframe->addBreadCrumb(lang('Checklists'), $checklists_url);
      if(Checklist::canAdd($this->logged_user, $this->active_project)) {
        $add_checklist_url = checklists_module_add_checklist_url($this->active_project);
        $this->wireframe->addPageAction(lang('New Checklist'), $add_checklist_url);
      } // if
      
      $checklist_id = $this->request->getId('checklist_id');
      if($checklist_id) {
        $this->active_checklist = ProjectObjects::findById($checklist_id);
      } // if
      
      if(instance_of($this->active_checklist, 'Checklist')) {
        if($this->active_checklist->isCompleted()) {
          $this->wireframe->addBreadCrumb(lang('Archive'), checklists_module_archive_url($this->active_project));
        } // if
        $this->wireframe->addBreadCrumb($this->active_checklist->getName(), $this->active_checklist->getViewUrl());
      } else {
        $this->active_checklist = new Checklist();
      } // if
      
      $this->smarty->assign(array(
        'active_checklist'       => $this->active_checklist,
        'checklists_url'         => $checklists_url,
        'checklists_archive_url' => $checklists_archive_url,
        'add_checklist_url'      => $add_checklist_url,
        'page_tab'               => 'checklists',
      ));
    } // __construct
    
    /**
     * Show checklists page
     *
     * @param void
     * @return null
     */
    function index() {
      $checklists = Checklists::findActiveByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility());
           
      if($this->request->isApiCall()) {
        $this->serveData($checklists, 'checklists');
      } else {
      	$this->smarty->assign('checklists', $checklists);
      } // if
        
      js_assign('can_manage_checklists', Checklist::canReorder($this->logged_user, $this->active_project));
      js_assign('reorder_checklists_url', assemble_url('project_checklists_reorder', array('project_id'=>$this->active_project->getId())), null,array('id' => 'reorder_checklists'));
      js_assign('expander_collapsed', get_image_url('expand_collapsed.gif'));
      js_assign('expander_expanded', get_image_url('expand_expanded.gif'));
    } // index
    
    /**
     * Show list of completed checklists
     *
     * @param void
     * @return null
     */
    function archive() {
      $checklists = Checklists::findCompletedByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility());
      if($this->request->isApiCall()) {
        $this->serveData($checklists, 'checklists');
      } else {
      	$this->smarty->assign('checklists', $checklists);
      } // if
    } // archive
    
    /**
     * View specific checklist
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_checklist->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_checklist->canView($this->logged_user)) {
      	$this->httpError($this->logged_user);
      } // if
      
      if($this->request->isApiCall()) {
        $this->serveData($this->active_checklist, 'checklist', array(
          'describe_tasks' => true,
        ));
      } else {
        ProjectObjectViews::log($this->active_checklist, $this->logged_user);
      } // if
      
      $show_only_tasks = $this->request->get('show_only_tasks');
      if ($show_only_tasks) {
        $this->skip_layout = true;
        $this->smarty->assign(array(
          'show_only_tasks' => true
        ));
      } // if
    } // view
    
    /**
     * Create a new checklist
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      if(!Checklist::canAdd($this->logged_user, $this->active_project)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $checklist_data = $this->request->post('checklist');
      if(!is_array($checklist_data)) {
        $checklist_data = array(
          'milestone_id' => $this->request->get('milestone_id'),
          'visibility' => $this->active_project->getDefaultVisibility(),
        );
      } // if
      $this->smarty->assign('checklist_data', $checklist_data);
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_checklist = new Checklist();
        
        $this->active_checklist->setAttributes($checklist_data);
        $this->active_checklist->setProjectId($this->active_project->getId());
        $this->active_checklist->setCreatedBy($this->logged_user);
        $this->active_checklist->setState(STATE_VISIBLE);
        
        $save = $this->active_checklist->save();
        if($save && !is_error($save)) {
          db_commit();
          $this->active_checklist->ready(); // ready
          
          if($this->request->isApiCall()) {
            $this->serveData($this->active_checklist, 'checklist');
          } else {
            flash_success('Checklist :name has been added', array('name' => $this->active_checklist->getName()));
            $this->redirectToUrl($this->active_checklist->getViewUrl());
          } // if
        } else {
          db_rollback();
          
          if($this->request->isApiCall()) {
            $this->serveData($save);
          } else {
            $this->smarty->assign('errors', $save);
          } // if
        } // if
      } // if
    } // add
    
    /**
     * Quick add checklist
     *
     * @param void
     * @return null
     */
    function quick_add() {
      if(!Checklist::canAdd($this->logged_user, $this->active_project)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN, lang("You don't have permission for this action"), true, true);
      } // if
      
      $this->skip_layout = true;
      
      $checklist_data = $this->request->post('checklist');
      if (!is_array($checklist_data)) {
        $checklist_data = array(
          'visibility' => $this->active_project->getDefaultVisibility()
        );
      } //if
      
      $this->smarty->assign(array(
        'checklist_data' => $checklist_data,
        'quick_add_url' => assemble_url('project_checklists_quick_add', array('project_id' => $this->active_project->getId())),
      ));
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_checklist = new Checklist();
        
        $this->active_checklist->setAttributes($checklist_data);
        $this->active_checklist->setProjectId($this->active_project->getId());
        $this->active_checklist->setCreatedBy($this->logged_user);
        $this->active_checklist->setState(STATE_VISIBLE);
        
        $subscribers = array($this->logged_user->getId());
        if(is_foreachable(array_var($checklist_data['assignees'], 0))) {
          $subscribers = array_merge($subscribers, array_var($checklist_data['assignees'], 0));
        } else {
          $subscribers[] = $this->active_project->getLeaderId();
        } // if
        Subscriptions::subscribeUsers($subscribers, $this->active_checklist);
        $this->active_checklist->ready(); // ready
        
        $save = $this->active_checklist->save();
        if($save && !is_error($save)) {
          if(isset($checklist_data['tasks']) && is_foreachable($checklist_data['tasks'])) {
            foreach($checklist_data['tasks'] as $task_text) {
              $task_text = trim($task_text);
              if($task_text != '') {
                $task = new Task();
                
                $task->setBody($task_text);
                $task->setPriority(PRIORITY_NORMAL);
                $task->setProjectId($this->active_project->getId());
                $task->setParent($this->active_checklist);
                $task->setCreatedBy($this->logged_user);
                $task->setState(STATE_VISIBLE);
                $task->setVisibility(VISIBILITY_NORMAL);
                $task->new_assignees = $checklist_data['assignees'];
                
                $task->save();
                Subscriptions::subscribeUsers($subscribers, $task);
                $task->ready();
              } // if
            } // if
          } // if
          
          db_commit();
          
          $this->smarty->assign(array(
            'active_checklist' => $this->active_checklist,
            'checklist_data' => array('visibility' => $this->active_project->getDefaultVisibility()),
            'project_id' => $this->active_project->getId()
          ));          
        } else {
          db_rollback();
          $this->httpError(HTTP_ERR_OPERATION_FAILED, $save->getErrorsAsString(), true, true);
        } // if
      } // if
    } // quick_add
    
    /**
     * Edit selected checklist
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      if($this->active_checklist->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_checklist->canEdit($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $checklist_data = $this->request->post('checklist');
      if(!is_array($checklist_data)) {
        $checklist_data = array(
          'name' => $this->active_checklist->getName(),
          'body' => $this->active_checklist->getBody(),
          'visibility' => $this->active_checklist->getVisibility(),
          'milestone_id' => $this->active_checklist->getMilestoneId(),
          'tags' => $this->active_checklist->getTags(),
        );
      } // if
      
      $this->smarty->assign('checklist_data', $checklist_data);
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $old_name = $this->active_checklist->getName();
        $this->active_checklist->setAttributes($checklist_data);
        
        $save = $this->active_checklist->save();
        if($save && !is_error($save)) {
          db_commit();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success('Checklist :name has been updated', array('name' => $old_name));
            $this->redirectToUrl($this->active_checklist->getViewUrl());
          } else {
            $this->serveData($this->active_checklist, 'checklist');
          } // if
        } else {
          db_rollback();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            $this->smarty->assign('errors', $save);
          } else {
            $this->serveData($save);
          } // if
        } // if
      } // if
    } // edit
    
    /**
     * Export project checklists
     *
     * @param void
     * @return null
     */
    function export() {
      $object_visibility = array_var($_GET, 'visibility', VISIBILITY_NORMAL);
      $exportable_modules = explode(',', array_var($_GET,'modules', null));
      if (!is_foreachable($exportable_modules)) {
        $exportable_modules = null;
      } // if
      
      require_once(PROJECT_EXPORTER_MODULE_PATH.'/models/ProjectExporterOutputBuilder.class.php');
      
      $output_builder = new ProjectExporterOutputBuilder($this->active_project, $this->smarty, $this->active_module, $exportable_modules);
      if (!$output_builder->createOutputFolder()) {
        $this->serveData($output_builder->execution_log, 'execution_log', null, FORMAT_JSON);
      } // if
      $output_builder->createAttachmentsFolder();
            
      $completed_checklists = Checklists::findCompletedByProject($this->active_project, STATE_VISIBLE, $object_visibility);
      $active_checklists = Checklists::findActiveByProject($this->active_project, STATE_VISIBLE, $object_visibility);
      $checklists = array_merge((array) $completed_checklists, (array) $active_checklists);
            
      // export checklists front page
      $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'index');
      $output_builder->smarty->assign('active_objects', $active_checklists);
      $output_builder->smarty->assign('completed_objects', $completed_checklists);
      $output_builder->outputToFile('index');
      
      if (is_foreachable($checklists)) {
        $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'object');
        foreach ($checklists as $checklist) {
          if (instance_of($checklist,'Checklist')) {
            $output_builder->smarty->assign(array(
              'object' => $checklist,
            ));
            $output_builder->outputToFile('checklist_'.$checklist->getId());
          } // if
        } // foreach
      } // if
            
      $this->serveData($output_builder->execution_log, 'execution_log', null, FORMAT_JSON);
    } // export
    
    /**
     * Reorder checklists widget
     * 
     * @param void
     * @return null
     */
    function reorder() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if(!Checklist::canReorder($this->logged_user, $this->active_project)) {
          $this->httpError(HTTP_ERR_FORBIDDEN);
        } // if
        
        $checklists_ids = $this->request->post('checklists');
        if(is_foreachable($checklists_ids)) {
          for($x = 0; $x < count($checklists_ids); $x++) {
            db_execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET position=? WHERE id = ?', $x + 1, $checklists_ids[$x]);
          } // for
        } // if
        
        $this->httpOk();
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // reorder
    
  }

?>