<?php

  /**
   * Init tickets module
   *
   * @package activeCollab.modules.tickets
   */
  
  define('TICKETS_MODULE', 'tickets');
  define('TICKETS_MODULE_PATH', APPLICATION_PATH . '/modules/tickets');
  
  set_for_autoload(array(
    'Ticket' => TICKETS_MODULE_PATH . '/models/tickets/Ticket.class.php',
    'Tickets' => TICKETS_MODULE_PATH . '/models/tickets/Tickets.class.php',
    'TicketChange' => TICKETS_MODULE_PATH . '/models/ticket_changes/TicketChange.class.php',
    'TicketChanges' => TICKETS_MODULE_PATH . '/models/ticket_changes/TicketChanges.class.php'
  ));
  
  /**
   * Return section URL
   *
   * @param Project $project
   * @return string
   */
  function tickets_module_url($project) {
    return assemble_url('project_tickets', array('project_id' => $project->getId()));
  } // tickets_module_url
  
  /**
   * Return add ticket URL
   *
   * @param Project $project
   * @param array $additional_params
   * @return string
   */
  function tickets_module_add_ticket_url($project, $additional_params = null) {
    $params = array('project_id' => $project->getId());
    
    if($additional_params !== null) {
      $params = array_merge($params, $additional_params);
    } // if
    
    return assemble_url('project_tickets_add', $params);
  } // tickets_module_add_ticket_url
  
  // ---------------------------------------------------
  //  Portal public methods
  // ---------------------------------------------------
  
  /**
   * Return portal tickets section URL
   *
   * @param Portal $portal
   * @return string
   */
  function portal_tickets_module_url($portal) {
  	return assemble_url('portal_tickets', array('portal_name' => $portal->getSlug()));
  } // portal_tickets_module_url
  
  /**
   * Return add ticket URL via public portal
   *
   * @param Portal $portal
   * @param array $additional_params
   * @return string
   */
  function portal_tickets_module_add_ticket_url($portal, $additional_params = null) {
  	$params = array('portal_name' => $portal->getSlug());
  	
  	if($additional_params !== null) {
  		$params = array_merge($params, $additional_params);
  	} // if
  	
  	return assemble_url('portal_tickets_add', $params);
  } // portal_tickets_module_add_ticket_url

?>