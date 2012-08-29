<?php

  // We need projects controller
  use_controller('project', SYSTEM_MODULE);

  /**
   * Tickets controller
   *
   * @package activeCollab.modules.tickets
   * @subpackage controllers
   */
  class TicketsController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = TICKETS_MODULE;
    
    /**
     * Active ticket
     *
     * @var Ticket
     */
    var $active_ticket;
    
    /**
     * Enable categories support for this controller
     *
     * @var boolean
     */
    var $enable_categories = true;
    
    /**
     * Actions that are exposed through API
     *
     * @var array
     */
    var $api_actions = array('index', 'archive', 'view', 'add', 'edit');
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return TicketsController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if($this->logged_user->getProjectPermission('ticket', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $tickets_url = tickets_module_url($this->active_project);
      $archive_url = assemble_url('project_tickets_archive', array('project_id' => $this->active_project->getId()));
      
      $this->wireframe->addBreadCrumb(lang('Tickets'), $tickets_url);
      
      $add_ticket_url = false;
      if(Ticket::canAdd($this->logged_user, $this->active_project)) {
        $params = null;
        if($this->active_category->isLoaded()) {
          $params = array('category_id' => $this->active_category->getId());
        } // if
        $add_ticket_url = tickets_module_add_ticket_url($this->active_project, $params);
        
        $this->wireframe->addPageAction(lang('New Ticket'), $add_ticket_url);
      } // if
      
      $ticket_id = $this->request->getId('ticket_id');
      if($ticket_id) {
        $this->active_ticket = Tickets::findByTicketId($this->active_project, $ticket_id);
      } // if
      
      if(instance_of($this->active_category, 'Category') && $this->active_category->isLoaded()) {
        $this->wireframe->addBreadCrumb($this->active_category->getName(), $this->active_category->getViewUrl());
      } // if
      
      if(instance_of($this->active_ticket, 'Ticket')) {
        if($this->active_ticket->isCompleted()) {
          $this->wireframe->addBreadCrumb(lang('Archive'), $archive_url);
        } // if
        $this->wireframe->addBreadCrumb($this->active_ticket->getName(), $this->active_ticket->getViewUrl());
      } else {
        $this->active_ticket = new Ticket();
      } // if
      
      $this->smarty->assign(array(
        'tickets_url'           => $tickets_url,
        'tickets_archive_url'   => $archive_url,
        'add_ticket_url'        => $add_ticket_url,
        'active_ticket'         => $this->active_ticket,
        'page_tab'              => 'tickets',
        'mass_edit_tickets_url' => assemble_url('project_tickets_mass_edit', array('project_id' => $this->active_project->getId())),
      ));
    } // __construct
    
    /**
     * Show tickets index page
     *
     * @param void
     * @return null
     */
    function index() {
      if($this->request->isApiCall()) {
        $this->serveData(Tickets::findOpenByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility()), 'tickets');
      } else {
        if($this->active_category->isLoaded()) {
          $tickets = Milestones::groupByMilestone(
            Tickets::findOpenByCategory($this->active_category, STATE_VISIBLE, $this->logged_user->getVisibility()), 
            STATE_VISIBLE, $this->logged_user->getVisibility()
          );
        } else {
          $tickets = Milestones::groupByMilestone(
            Tickets::findOpenByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility()), 
            STATE_VISIBLE, $this->logged_user->getVisibility()
          );
        } // if
        
        $this->smarty->assign(array(
          'categories' => Categories::findByModuleSection($this->active_project, TICKETS_MODULE, 'tickets'),
          'groupped_tickets' => $tickets,
          'milestones' => Milestones::findActiveByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility()),
          'can_add_ticket' => Ticket::canAdd($this->logged_user, $this->active_project),
          'can_manage_categories' => $this->logged_user->isProjectLeader($this->active_project) || $this->logged_user->isProjectManager(), 
        ));
        
        js_assign('can_manage_tickets', Ticket::canManage($this->logged_user, $this->active_project));
      } // if
    } // index
    
    /**
     * Override view category page
     *
     * @param void
     * @return null
     */
    function view_category() {
      $this->redirectTo('project_tickets', array(
        'project_id' => $this->active_project->getId(),
        'category_id' => $this->request->getId('category_id')
      ));
    } // view_category
    
    /**
     * Show completed tickets
     *
     * @param void
     * @return null
     */
    function archive() {
      if($this->request->isApiCall()) {
        $this->serveData(Tickets::findCompletedByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility()), 'tickets');
      } else {
        $this->wireframe->addBreadCrumb(lang('Archive'), assemble_url('project_tickets_archive', array('project_id' => $this->active_project->getId())));
      
        $per_page = 15;
        $page = (integer) $this->request->get('page');
        if($page < 1) {
          $page = 1;
        } // if
        
        if($this->active_category->isLoaded()) {
          list($tickets, $pagination) = Tickets::paginateCompletedByCategory($this->active_category, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
        } else {
          list($tickets, $pagination) = Tickets::paginateCompletedByProject($this->active_project, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
        } // if
        
        $this->smarty->assign(array(
          'tickets' => $tickets,
          'pagination' => $pagination,
          'categories' => Categories::findByModuleSection($this->active_project, TICKETS_MODULE, 'tickets'),
        ));
      } // if
    } // archive
    
    /**
     * Show single ticket
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_ticket->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_ticket->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isApiCall()) {
        $this->serveData($this->active_ticket, 'ticket', array(
          'describe_comments'    => true, 
          'describe_tasks'       => true, 
          'describe_attachments' => true,
          'describe_assignees'   => true,
        ));
      } else {
        ProjectObjectViews::log($this->active_ticket, $this->logged_user);
        
        $page = (integer) $this->request->get('page');
        if($page < 1) {
          $page = 1;
        } // if
        
        list($comments, $pagination) = $this->active_ticket->paginateComments($page, $this->active_ticket->comments_per_page, $this->logged_user->getVisibility());
        
        $this->smarty->assign(array(
          'comments' => $comments,
          'pagination' => $pagination,
          'counter' => ($page - 1) * $this->active_ticket->comments_per_page,
        ));
      } // if
    } // view
    
    /**
     * Create a new ticket
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // ifs
      
      if(!Ticket::canAdd($this->logged_user, $this->active_project)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $ticket_data = $this->request->post('ticket');
      if(!is_array($ticket_data)) {
        $ticket_data = array(
          'visibility'   => $this->active_project->getDefaultVisibility(),
          'milestone_id' => $this->request->get('milestone_id'),
          'parent_id'    => $this->request->get('category_id'),
        );
      } // if
      
      $this->smarty->assign('ticket_data', $ticket_data);
      
      if($this->request->isSubmitted()) {
        db_begin_work();
               
        $this->active_ticket = new Ticket();
        
        attach_from_files($this->active_ticket, $this->logged_user);
        
        $this->active_ticket->setAttributes($ticket_data);
        $this->active_ticket->setProjectId($this->active_project->getId());
        $this->active_ticket->setCreatedBy($this->logged_user);
        $this->active_ticket->setState(STATE_VISIBLE);
        
        $save = $this->active_ticket->save();
        
        if($save && !is_error($save)) {
          $subscribers = array($this->logged_user->getId());
          if(is_foreachable(array_var($ticket_data['assignees'], 0))) {
            $subscribers = array_merge($subscribers, array_var($ticket_data['assignees'], 0));
          } else {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          
          if(!in_array($this->active_project->getLeaderId(), $subscribers)) {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          
          Subscriptions::subscribeUsers($subscribers, $this->active_ticket);
          
          db_commit();
          $this->active_ticket->ready();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success('Ticket #:ticket_id has been added', array('ticket_id' => $this->active_ticket->getTicketId()));
            $this->redirectToUrl($this->active_ticket->getViewUrl());
          } else {
            $this->serveData($this->active_ticket, 'ticket');
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
     * Quick add ticket
     *
     * @param void
     * @return null
     */
    function quick_add() {
      if(!Ticket::canAdd($this->logged_user, $this->active_project)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN, lang("You don't have permission for this action"), true, true);
      } // if
      
      $this->skip_layout = true;
           
      $ticket_data = $this->request->post('ticket');
      if(!is_array($ticket_data)) {
        $ticket_data = array(
          'visibility'   => $this->active_project->getDefaultVisibility(),
        );
      } // if
      
      $this->smarty->assign(array(
        'ticket_data' => $ticket_data,
        'quick_add_url' => assemble_url('project_tickets_quick_add', array('project_id' => $this->active_project->getId())),
      ));
      
      if ($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_ticket = new Ticket();
        
        if (count($_FILES > 0)) {
          attach_from_files($this->active_ticket, $this->logged_user);  
        } // if
        
        $this->active_ticket->setAttributes($ticket_data);
        $this->active_ticket->setBody(clean(array_var($ticket_data, 'body', null)));
        if(!isset($ticket_data['priority'])) {
          $this->active_ticket->setPriority(PRIORITY_NORMAL);
        } // if
        $this->active_ticket->setProjectId($this->active_project->getId());
        $this->active_ticket->setCreatedBy($this->logged_user);
        $this->active_ticket->setState(STATE_VISIBLE);
        
        $save = $this->active_ticket->save();
        if($save && !is_error($save)) {
          $subscribers = array($this->logged_user->getId());
          if(is_foreachable(array_var($ticket_data['assignees'], 0))) {
            $subscribers = array_merge($subscribers, array_var($ticket_data['assignees'], 0));
          } else {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          Subscriptions::subscribeUsers($subscribers, $this->active_ticket);
          
          db_commit();
          $this->active_ticket->ready(); // ready
          
          $this->smarty->assign(array(
            'ticket_data' => array('visibility' => $this->active_project->getDefaultVisibility()),
            'active_ticket' => $this->active_ticket,
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
     * Update existing ticket
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // ifs
      
      if($this->active_ticket->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_ticket->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $ticket_data = $this->request->post('ticket');
      if(!is_array($ticket_data)) {
        $ticket_data = array(
          'name' => $this->active_ticket->getName(),
          'body' => $this->active_ticket->getBody(),
          'visibility' => $this->active_ticket->getVisibility(),
          'parent_id' => $this->active_ticket->getParentId(),
          'milestone_id' => $this->active_ticket->getMilestoneId(),
          'priority' => $this->active_ticket->getPriority(),
          'assignees' => Assignments::findAssignmentDataByObject($this->active_ticket),
          'tags' => $this->active_ticket->getTags(),
          'due_on' => $this->active_ticket->getDueOn(),
        );
      } // if
      $this->smarty->assign('ticket_data', $ticket_data);
      
      if($this->request->isSubmitted()) {
        if(!isset($ticket_data['assignees'])) {
          $ticket_data['assignees'] = array(array(), 0);
        } // if
        
        db_begin_work();
        $this->active_ticket->setAttributes($ticket_data);
        $save = $this->active_ticket->save();
        
        if($save && !is_error($save)) {
          db_commit();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success('Ticket #:ticket_id has been updated', array('ticket_id' => $this->active_ticket->getTicketId()));
            $this->redirectToUrl($this->active_ticket->getViewUrl());
          } else {
            $this->serveData($this->active_ticket, 'ticket');
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
     * Update multiple tickets
     *
     * @param void
     * @return null
     */
    function mass_update() {
      if($this->request->isSubmitted()) {
        $action = $this->request->post('with_selected');
        if(trim($action) == '') {
          flash_error('Please select what you want to do with selected tickets');
          $this->redirectToReferer($this->smarty->get_template_vars('tickets_url'));
        } // if
        
        $ticket_ids = $this->request->post('tickets');
        $tickets = Tickets::findByIds($ticket_ids, STATE_VISIBLE, $this->logged_user->getVisibility());
        
        $updated = 0;
        if(is_foreachable($tickets)) {
          
          // Complete selected tickets
          if($action == 'complete') {
            $message = lang(':count tickets completed');
            foreach($tickets as $ticket) {
              if($ticket->isOpen() && $ticket->canChangeCompleteStatus($this->logged_user)) {
                $complete = $ticket->complete($this->logged_user);
                if($complete && !is_error($complete)) {
                  $updated++;
                } // if
              } // if
            } // foreach
            
          // Open selected tickets
          } elseif($action == 'open') {
            $message = lang(':count tickets opened');
            foreach($tickets as $ticket) {
              if($ticket->isCompleted() && $ticket->canChangeCompleteStatus($this->logged_user)) {
                $open = $ticket->open($this->logged_user);
                if($open && !is_error($open)) {
                  $updated++;
                } // if
              } // if
            } // foreach
            
          // Mark object as starred
          } elseif($action == 'star') {
            $message = lang(':count tickets starred');
            foreach($tickets as $ticket) {
              $star = $ticket->star($this->logged_user);
              if($star && !is_error($star)) {
                $updated++;
              } // if
            } // foreach
            
          // Unstar objects
          } elseif($action == 'unstar') {
            $message = lang(':count tickets unstarred');
            foreach($tickets as $ticket) {
              $unstar = $ticket->unstar($this->logged_user);
              if($unstar && !is_error($unstar)) {
                $updated++;
              } // if
            } // foreach
            
          // Move selected objects to Trash
          } elseif($action == 'trash') {
            $message = lang(':count tickets moved to Trash');
            foreach($tickets as $ticket) {
              if($ticket->canDelete($this->logged_user)) {
                $delete = $ticket->moveToTrash();
                if($delete && !is_error($delete)) {
                  $updated++;
                } // if
              } // if
            } // foreach
            
          // Set a selected priority
          } elseif(str_starts_with($action, 'set_priority')) {
            $priority = (integer) substr($action, 13);
            $message = lang(':count tickets updated');
            foreach($tickets as $ticket) {
              if($ticket->canEdit($this->logged_user)) {
                $ticket->setPriority($priority);
                $save = $ticket->save();
                if($save && !is_error($save)) {
                  $updated++;
                } // if
              } // if
            } // foreach
            
          // Set visibility
          } elseif(str_starts_with($action, 'set_visibility')) {
            $visibility = (integer) substr($action, 15);
            $message = lang(':count tickets updated');
            foreach($tickets as $ticket) {
              if($ticket->canEdit($this->logged_user)) {
                $ticket->setVisibility($visibility);
                $save = $ticket->save();
                if($save && !is_error($save)) {
                  $updated++;
                } // if
              } // if
            } // foreach
            
          // Move this ticket to a given milestone
          } elseif(str_starts_with($action, 'move_to_milestone')) {
            if($action == 'move_to_milestone') {
              $milestone_id = null;
            } else {
              $milestone_id = (integer) substr($action, 18);
            } // if
            
            $message = lang(':count tickets updated');
            foreach($tickets as $ticket) {
              if($ticket->canEdit($this->logged_user)) {
                $ticket->setMilestoneId($milestone_id);
                $save = $ticket->save();
                if($save && !is_error($save)) {
                  $updated++;
                } // if
              } // if
            } // foreach
            
          // Move selected tickets to selected category
          } elseif(str_starts_with($action, 'move_to_category')) {
            if($action == 'move_to_category') {
              $category_id = null;
            } else {
              $category_id = (integer) substr($action, 17);
            } // if
            
            $category = $category_id ? Categories::findById($category_id) : null;
            
            $message = lang(':count tickets updated');
            foreach($tickets as $ticket) {
              if($ticket->canEdit($this->logged_user)) {
                $ticket->setParent($category, false);
                $save = $ticket->save();
                if($save && !is_error($save)) {
                  $updated++;
                } // if
              } // if
            } // foreach
            
          } else {
            $this->httpError(HTTP_ERR_BAD_REQUEST);
          } // if
          
          flash_success($message, array('count' => $updated));
          $this->redirectToReferer($this->smarty->get_template_vars('tickets_url'));
        } else {
          flash_error('Please select tickets that you would like to update');
          $this->redirectToReferer($this->smarty->get_template_vars('tickets_url'));
        } // if
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // mass_update
    
    /**
     * Show ticket changes
     *
     * @param void
     * @return null
     */
    function changes() {
      if($this->active_ticket->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_ticket->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->skip_layout = $this->request->isApiCall() || $this->request->isAsyncCall();
      
      $this->smarty->assign('changes', $this->active_ticket->getChanges());
    } // changes
    
    /**
     * Export tickets
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
      
      $module_categories = Categories::findByModuleSection($this->active_project, $this->active_module, $this->active_module);
      $module_objects = Tickets::findByProject($this->active_project, null , STATE_VISIBLE, $object_visibility);

      $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'index');
      $output_builder->smarty->assign('categories',$module_categories);
      $output_builder->smarty->assign('objects', $module_objects);
      $output_builder->outputToFile('index');
                 
      // export tickets by categories
      if (is_foreachable($module_categories)) {
        foreach ($module_categories as $module_category) {
          if (instance_of($module_category,'Category')) {
            $output_builder->smarty->assign(array(
              'current_category' => $module_category,
              'objects' => Tickets::findByProject($this->active_project, $module_category, STATE_VISIBLE, $object_visibility),
            ));
            $output_builder->outputToFile('category_'.$module_category->getId());
          } // if
        } // foreach
      } // if
      
      // export tickets
      if (is_foreachable($module_objects)) {
        $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'object');
        foreach ($module_objects as $module_object) {
          if (instance_of($module_object,'Ticket')) {
            $output_builder->outputAttachments($module_object->getAttachments());
            
            $comments = $module_object->getComments($object_visibility);
            $output_builder->outputObjectsAttachments($comments);
            
            if (module_loaded('timetracking')) {
              $timerecords = TimeRecords::findByParent($module_object, null, STATE_VISIBLE, $object_visibility);
              $total_time = TimeRecords::calculateTime($timerecords);
            } else {
              $timerecords = null;
              $total_time = 0;
            } // if
            
            $output_builder->smarty->assign(array(
              'timerecords' => $timerecords,
              'total_time'  => $total_time,
            	'object' => $module_object,
            	'comments' => $comments,
            ));
            $output_builder->outputToFile('ticket_'.$module_object->getId());
          } // if
        } // foreach
      } // if
      
      $this->serveData($output_builder->execution_log, 'execution_log', null, FORMAT_JSON);
    } // export
    
    /**
     * Show and process reorder task form
     *
     * @param void
     * @return null
     */
    function reorder_tickets() {
      $this->wireframe->print_button = false;
      
      $milestone = Milestones::findById($this->request->get('milestone_id'));
      if (instance_of($milestone, 'Milestone')) {
        $milestone_id = $milestone->getId();
      } else {
        $milestone_id = null;
      } // if
      
      if (!$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST, null, true, true);
      } // if
      
      if (!Ticket::canManage($this->logged_user, $this->active_project)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, true);
      } // if     
      
      $order_data = $this->request->post('reorder_ticket');
      $ids = array_keys($order_data);
      if (is_foreachable($order_data)) {
      	$x = 1;
        foreach ($order_data as $key=>$value) {
        	$order_data[$key] = $x;
        	$x++;
        } // foreach
      } // if
      
      $tickets = Tickets::findByIds($ids, STATE_VISIBLE, $this->logged_user->getVisibility());
      if (is_foreachable($tickets)) {
        foreach ($tickets as $ticket) {
          $ticket->setMilestoneId($milestone_id);
          $ticket->setPosition(array_var($order_data, $ticket->getId()));
          $ticket->save();
        } // foreach
      } // if
      $this->httpOk();
    } // reorder
  
  } // TicketsController

?>