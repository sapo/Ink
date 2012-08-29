<?php

  /**
   * ticket_changes helper
   *
   * @package activeCollab.modules.tickets
   * @subpackage helpers
   */
  
  /**
   * Render ticket changes block
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_ticket_changes($params, &$smarty) {
    $ticket = array_var($params, 'ticket');
    if(!instance_of($ticket, 'Ticket')) {
      return new InvalidParamError('ticket', $ticket, '$ticket is expected to be an instance of Ticket class', true);
    } // if
    
    $total_changes = $ticket->countChanges();
    if($total_changes == 0) {
      return '';
    } // if
    
    $changes = $ticket->getChanges(3);
    $smarty->assign(array(
      '_changes' => $changes,
      '_total_changes' => $total_changes,
    ));
    return $smarty->fetch(get_template_path('_ticket_changes', 'tickets', TICKETS_MODULE));
  } // smarty_function_ticket_changes

?>