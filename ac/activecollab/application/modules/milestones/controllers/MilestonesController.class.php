<?php

  // We need ProjectController
  use_controller('project', SYSTEM_MODULE);

  /**
   * Milestones controller
   *
   * @package activeCollab.modules.milestones
   * @subpackage models
   */
  class MilestonesController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = MILESTONES_MODULE;
    
    /**
     * Selected milestone
     *
     * @var Milestone
     */
    var $active_milestone;
    
    /**
     * Actions available through API
     *
     * @var array
     */
    var $api_actions = array('index', 'archive', 'view', 'add', 'edit');
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return MilestonesController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if($this->logged_user->getProjectPermission('milestone', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $milestones_url = milestones_module_url($this->active_project);
      $add_milestone_url = milestones_module_add_url($this->active_project);
      
      $this->wireframe->addBreadCrumb(lang('Milestones'), $milestones_url);

      if(Milestone::canAdd($this->logged_user, $this->active_project)) {
        $this->wireframe->addPageAction(lang('New Milestone'), $add_milestone_url);
      } // if
      
      $milestone_id = $this->request->getId('milestone_id');
      if($milestone_id) {
        $this->active_milestone = ProjectObjects::findById($milestone_id);
      } // if
      
      if(instance_of($this->active_milestone, 'Milestone')) {
        if($this->active_milestone->getCompletedOn()) {
          $this->wireframe->addBreadCrumb(lang('Archive'), assemble_url('project_milestones_archive', array(
            'project_id' => $this->active_project->getId(),
          )));
        } // if
        
        $this->wireframe->addBreadCrumb($this->active_milestone->getName(), $this->active_milestone->getViewUrl());
      } else {
        $this->active_milestone = new Milestone();
      } // if
      
      $this->smarty->assign(array(
        'active_milestone'  => $this->active_milestone,
        'milestones_url'    => $milestones_url,
        'add_milestone_url' => $add_milestone_url,
        'page_tab'          => 'milestones',
      ));
    } // __construct
    
    /**
     * Show milestones index page
     *
     * @param void
     * @return null
     */
    function index() {
      require_once SMARTY_PATH . '/plugins/modifier.datetime.php';
      
      $milestones = Milestones::findActiveByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility());
      if($this->request->isApiCall()) {
        $this->serveData($milestones, 'milestones');
      } else {
        $this->smarty->assign('milestones', $milestones);
      } // if
    } // index
    
    /**
     * Show completed milestones
     *
     * @param void
     * @return null
     */
    function archive() {
      $milestones = Milestones::findCompletedByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility());
      if($this->request->isApiCall()) {
        $this->serveData($milestones, 'milestones');
      } else {
        $this->smarty->assign('milestones', $milestones);
      } // if
    } // archive
    
    /**
     * Show single milestone
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_milestone->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_milestone->canView($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      ProjectObjectViews::log($this->active_milestone, $this->logged_user);
      
      if($this->request->isApiCall()) {
        $this->serveData($this->active_milestone, 'milestone', array(
          'describe_assignees' => true,
        ));
      } else {
        $total_objects = 0;
        $objects = $this->active_milestone->getObjects($this->logged_user);
        if(is_foreachable($objects)) {
          foreach($objects as $objects_by_module) {
            $total_objects += count($objects_by_module);
          } // foreach
        } // if
        
        // ---------------------------------------------------
        //  Prepare add suboject links
        // ---------------------------------------------------
        
        $links_code = '';
        
        $links = array();
        event_trigger('on_milestone_add_links', array($this->active_milestone, $this->logged_user, &$links));
        
        if(is_foreachable($links)) {
          $total_links = count($links);
          $counter = 1;
          foreach($links as $k => $v) {
            $links_code .= open_html_tag('a', array('href' => $v)) . $k . '</a>';
            
            if($counter < ($total_links - 1)) {
              $links_code .= ', ';
            } elseif($counter == ($total_links - 1)) {
              $links_code .= ' ' . lang('or') . ' ';
            } // if
            
            $counter++;
          } // foreach
        } // if
        
        $this->smarty->assign(array(
          'total_objects' => $total_objects,
          'milestone_objects' => $objects,
          'milestone_add_links_code' => $links_code,
        ));
      } // if
    } // view
    
    /**
     * Create a new milestone
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      if(!Milestone::canAdd($this->logged_user, $this->active_project)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $milestone_data = $this->request->post('milestone');
      $this->smarty->assign('milestone_data', $milestone_data);
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_milestone = new Milestone();
        
        $this->active_milestone->setAttributes($milestone_data);
        $this->active_milestone->setProjectId($this->active_project->getId());
        $this->active_milestone->setCreatedBy($this->logged_user);
        $this->active_milestone->setState(STATE_VISIBLE);
        $this->active_milestone->setVisibility(VISIBILITY_NORMAL);
        
        $save = $this->active_milestone->save();
        
        if($save && !is_error($save)) {
          $subscribers = array($this->logged_user->getId());
          if(is_foreachable(array_var($milestone_data['assignees'], 0))) {
            $subscribers = array_merge($subscribers, array_var($milestone_data['assignees'], 0));
          } else {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          
          if(!in_array($this->active_project->getLeaderId(), $subscribers)) {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          
          Subscriptions::subscribeUsers($subscribers, $this->active_milestone);
            
          db_commit();
          $this->active_milestone->ready();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success('Milestone ":name" has been created', array('name' => $this->active_milestone->getName()));
            $this->redirectToUrl($this->active_milestone->getViewUrl());
          } else {
            $this->serveData($this->active_milestone, 'milestone');
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
    } // add
    
    /**
     * Quick add milestone
     *
     * @param void
     * @return null
     */
    function quick_add() {
      if(!Milestone::canAdd($this->logged_user, $this->active_project)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN, lang("You don't have permission for this action"), true, true);
      } // if
      
      $this->skip_layout = true;
            
      $milestone_data = $this->request->post('milestone');
      if (!is_array($milestone_data)) {
        $milestone_data = array(
          'visibility' => $this->active_project->getDefaultVisibility()
        );
      } // if
      
      $this->smarty->assign(array(
        'milestone_data' => $milestone_data,
        'quick_add_url' => assemble_url('project_milestones_quick_add', array('project_id' => $this->active_project->getId())),
      ));
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_milestone = new Milestone();
        
        $this->active_milestone->setAttributes($milestone_data);
        if(!isset($milestone_data['priority'])) {
          $this->active_milestone->setPriority(PRIORITY_NORMAL);
        } // if
        $this->active_milestone->setProjectId($this->active_project->getId());
        $this->active_milestone->setCreatedBy($this->logged_user);
        $this->active_milestone->setState(STATE_VISIBLE);
        $this->active_milestone->setVisibility(VISIBILITY_NORMAL);
        
        $save = $this->active_milestone->save();
        if($save && !is_error($save)) {
          $subscribers = array($this->logged_user->getId());
          if(is_foreachable(array_var($milestone_data['assignees'], 0))) {
            $subscribers = array_merge($subscribers, array_var($milestone_data['assignees'], 0));
          } else {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          Subscriptions::subscribeUsers($subscribers, $this->active_milestone);
          
          db_commit();
          $this->active_milestone->ready();
          
          $this->smarty->assign(array(
            'active_milestone' => $this->active_milestone,
            'milestone_data' => array('visibility' => $this->active_project->getDefaultVisibility()),
            'project_id' => $this->active_project->getId()
          ));
          
          $this->skip_layout = true;
        } else {
          db_rollback();
          $this->httpError(HTTP_ERR_OPERATION_FAILED, $save->getErrorsAsString(), true, true);
        } // if
      } // if
    } // quick_add
    
    /**
     * Edit specific milestone
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->active_milestone->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_milestone->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->wireframe->addPageMessage(lang('<a href=":url">Click here</a> if you wish to reschedule this milestone', array('url' => $this->active_milestone->getRescheduleUrl())), 'info');
      
      $milestone_data = $this->request->post('milestone');
      if(!is_array($milestone_data)) {
        $milestone_data = array(
          'name' => $this->active_milestone->getName(),
          'body' => $this->active_milestone->getBody(),
          'start_on' => $this->active_milestone->getStartOn(),
          'due_on' => $this->active_milestone->getDueOn(),
          'priority' => $this->active_milestone->getPriority(),
          'assignees' => Assignments::findAssignmentDataByObject($this->active_milestone),
          'tags' => $this->active_milestone->getTags(),
        );
      } // if
      $this->smarty->assign('milestone_data', $milestone_data);
      
      if($this->request->isSubmitted()) {
        if(!isset($milestone_data['assignees'])) {
          $milestone_data['assignees'] = array(array(), 0);
        } // if
        
        db_begin_work();
        
        $old_name = $this->active_milestone->getName();
        $this->active_milestone->setAttributes($milestone_data);
        $save = $this->active_milestone->save();
        if($save && !is_error($save)) {
          db_commit();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success('Milestone ":name" has been updated', array('name' => $old_name));
            $this->redirectToUrl($this->active_milestone->getViewUrl());
          } else {
            $this->serveData($this->active_milestone, 'milestone');
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
     * Reschedule selected milestone
     *
     * @param void
     * @return null
     */
    function reschedule() {
      if($this->active_milestone->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_milestone->canEdit($this->logged_user)) {
      	$this->httpError($this->logged_user);
      } // if
      
      $milestone_data = $this->request->post('milestone');
      if(!is_array($milestone_data)) {
        $milestone_data = array(
          'start_on' => $this->active_milestone->getStartOn(),
          'due_on' => $this->active_milestone->getDueOn(),
          'reschedule_milstone_objects' => false,
        );
      } // if
      $this->smarty->assign('milestone_data', $milestone_data);
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $old_due_on = $this->active_milestone->getDueOn();
        
        $new_start_on = new DateValue(array_var($milestone_data, 'start_on'));
        $new_due_on = new DateValue(array_var($milestone_data, 'due_on'));
        $reschedule_tasks = (boolean) array_var($milestone_data, 'reschedule_milstone_objects');
        
        $successive_milestones = Milestones::findSuccessiveByMilestone($this->active_milestone, STATE_VISIBLE, $this->logged_user->getVisibility()); // before we update timestamp
        
        $reschedule = $this->active_milestone->reschedule($new_start_on, $new_due_on, $reschedule_tasks);
        if($reschedule && !is_error($reschedule)) {
          if($new_due_on->getTimestamp() != $old_due_on->getTimestamp()) {
            $with_successive = array_var($milestone_data, 'with_sucessive');
            
            $to_move = null;
            switch(array_var($with_successive, 'action')) {
              case 'move_all':
                $to_move = $successive_milestones;
                break;
              case 'move_selected':
                $selected_milestones = array_var($with_successive, 'milestones');
                if(is_foreachable($selected_milestones)) {
                  $to_move = Milestones::findByIds($selected_milestones, STATE_VISIBLE, $this->logged_user->getVisibility());
                } // if
                break;
            } // switch
            
            if(is_foreachable($to_move)) {
              $diff = $new_due_on->getTimestamp() - $old_due_on->getTimestamp();
              foreach($to_move as $to_move_milestone) {
                $milestone_start_on = $to_move_milestone->getStartOn();
                $milestone_due_on = $to_move_milestone->getDueOn();
                
                $new_milestone_start_on = $milestone_start_on->advance($diff, false);
                $new_milestone_due_on = $milestone_due_on->advance($diff, false);
                
                $to_move_milestone->reschedule($new_milestone_start_on, $new_milestone_due_on, $reschedule_tasks);
              } // foreach
            } // if
          } // if
          
          db_commit();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success('Milestone ":name" has been updated', array('name' => $this->active_milestone->getName()));
            $this->redirectToUrl($this->active_milestone->getViewUrl());
          } else {
            $this->serveData($this->active_milestone);
          } // if
        } else {
          db_rollback();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            $this->smarty->assign('errors', $reschedule);
          } else {
            $this->serveData($save);
          } // if
        } // if
      } // if
    } // edit
    
    /**
     * Export project milestones
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
      
      $active_milestones = array();
      $completed_milestones = array();
      
      $all_milestones = Milestones::findAllByProject($this->active_project, $object_visibility);
      if(is_foreachable($all_milestones)) {
        $output_builder->setFileTemplate(MILESTONES_MODULE, 'milestones', 'object');
        foreach($all_milestones as $milestone) {
          if ($milestone->isCompleted()) {
            $completed_milestones[] = $milestone;
          } else {
            $active_milestones[] = $milestone;
          } // if
          
          // Build milestone details page
          
          $objects = array();
          event_trigger('on_milestone_objects_by_visibility', array(&$milestone, &$objects, $object_visibility));
        	  
      	  $total_objects = 0;
      	  if (is_foreachable($objects)) {
      	    foreach ($objects as $objects_per_module) {
      	    	$total_objects += count($objects_per_module);
      	    } // foreach
      	  } // if
      	  
      	  $output_builder->smarty->assign(array(
      	    'active_milestone' => $milestone,
      	    'active_milestone_objects' => $objects,
      	    'total_objects' => $total_objects,
      	  ));
      	  
      	  $output_builder->outputToFile('milestone_'.$milestone->getId());
        } // foreach
      } // if
            
      // export milestones front page
      $output_builder->setFileTemplate(MILESTONES_MODULE, 'milestones', 'index');
      $output_builder->smarty->assign('active_milestones', $active_milestones);
      $output_builder->smarty->assign('completed_milestones', $completed_milestones);
      $output_builder->outputToFile('index');

      $this->serveData($output_builder->execution_log, 'execution_log', null, FORMAT_JSON);
    } // export
  
  }

?>