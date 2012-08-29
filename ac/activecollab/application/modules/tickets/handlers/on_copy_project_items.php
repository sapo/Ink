<?php

  /**
   * Tickets handle on_copy_project_items event
   *
   * @package activeCollab.modules.tickets
   * @subpackage handlers
   */

  /**
   * Handle on_copy_project_items event
   *
   * @param Project $from
   * @param Project $to
   * @param array $milestones_map
   * @param array $categories_map
   * @return null
   */
  function tickets_handle_on_copy_project_items(&$from, &$to, $milestones_map, $categories_map) {
  	$tickets = Tickets::findByProject($from, null, STATE_VISIBLE, VISIBILITY_PRIVATE);
  	if(is_foreachable($tickets)) {
  	  foreach($tickets as $ticket) {
  	    $ticket->copyToProject($to, array_var($milestones_map, $ticket->getMilestoneId()), array_var($categories_map, $ticket->getParentId()), array('Task', 'Attachment'));
  	  } // foreach
  	} // if
  } // tickets_handle_on_copy_project_items

?>