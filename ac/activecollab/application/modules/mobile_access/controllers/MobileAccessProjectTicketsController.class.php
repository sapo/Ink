<?php

  // We need MobileAccessProjectController
  use_controller('mobile_access_project', MOBILE_ACCESS_MODULE);

  /**
   * Mobile Access Project Tickets controller
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage controllers
   */
  class MobileAccessProjectTicketsController extends MobileAccessProjectController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'mobile_access_project_tickets';
    
    /**
     * Active ticket (if exists)
     *
     * @var Ticket
     */
    var $active_ticket;
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return MobileAccessController extends ApplicationController 
     */
    function __construct($request) {
      parent::__construct($request);
      
      if($this->logged_user->getProjectPermission('ticket', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->controller_description_name = lang('Tickets');
      $this->active_project_section = 'tickets';
      $this->enableCategories();
      
      $ticket_id = $this->request->getId('object_id');
      if($ticket_id) {
        $this->active_ticket = ProjectObjects::findById($ticket_id);
      } // if
      
      if(!instance_of($this->active_ticket, 'Ticket')) {
        $this->active_ticket = new Ticket();
      } // if
      
      $this->smarty->assign(array(
        "active_ticket" => $this->active_ticket,
        "active_project_section" => $this->active_project_section,
      ));
      
      $this->addBreadcrumb($this->controller_description_name, assemble_url('mobile_access_view_tickets',array('project_id' => $this->active_project->getId())));
    } // __construct
    
    /**
     * List of tickets
     *
     */
    function index() {
      $this->addBreadcrumb(lang('List'));
      
      if($this->active_category->isLoaded()) {
        $tickets = Milestones::groupByMilestone(
          Tickets::findOpenByCategory($this->active_category, STATE_VISIBLE, $this->logged_user->getVisibility()),  STATE_VISIBLE, $this->logged_user->getVisibility()
        );
      } else {
        $tickets = Milestones::groupByMilestone(
          Tickets::findOpenByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility()), STATE_VISIBLE, $this->logged_user->getVisibility()
        );
      } // if
    
      $this->smarty->assign(array(
        'categories' => Categories::findByModuleSection($this->active_project, TICKETS_MODULE, 'tickets'),
        'tickets' => $tickets,
        'pagination_url' => assemble_url('mobile_access_view_tickets', array('project_id' => $this->active_project->getId())),
        'page_back_url' => assemble_url('mobile_access_view_project', array('project_id' => $this->active_project->getId())),
      ));
    } // index
    
    /**
     * View the ticket
     *
     */
    function view() {
      if($this->active_ticket->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_ticket->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      ProjectObjectViews::log($this->active_ticket, $this->logged_user);
      
      $this->smarty->assign(array(
        'page_back_url' => assemble_url('mobile_access_view_tickets', array('project_id' => $this->active_project->getId())),
      ));
      
      $this->addBreadcrumb(str_excerpt(clean($this->active_ticket->getName()),10),mobile_access_module_get_view_url($this->active_ticket));
      $this->addBreadcrumb(lang('View'));
     
    } // view
    
  } // MobileAccessProjectTicketsController
?>