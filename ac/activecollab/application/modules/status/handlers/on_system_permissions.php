<?php

  /**
   * Status on_system_permissions handler
   *
   * @package activeCollab.modules.status
   * @subpackage handlers
   */
  
  /**
   * Handle on_system_permissions
   *
   * @param array $permissions
   * @return null
   */
  function status_handle_on_system_permissions(&$permissions) {
  	$permissions[] = 'can_use_status_updates';
  } // status_handle_on_system_permissions

?>