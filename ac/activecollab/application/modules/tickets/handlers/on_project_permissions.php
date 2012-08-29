<?php

  /**
   * Tickets handle on_project_permissions event
   *
   * @package activeCollab.modules.tickets
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   * @return null
   */
  function tickets_handle_on_project_permissions(&$permissions) {
  	$permissions['ticket'] = lang('Tickets');
  } // tickets_handle_on_project_permissions

?>