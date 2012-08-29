<?php

  /**
   * Tickets on_user_cleanup event handler
   *
   * @package activeCollab.modules.tickets
   * @subpackage handlers
   */

  /**
   * Handle on_user_cleanup event
   *
   * @param array $cleanup
   * @return null
   */
  function tickets_handle_on_user_cleanup(&$cleanup) {
    if(!isset($cleanup['ticket_changes'])) {
      $cleanup['ticket_changes'] = array();
    } // if
    
    $cleanup['ticket_changes'][] = 'created_by';
  } // tickets_handle_on_user_cleanup

?>