<?php

  /**
   * Time tracking on_system_permissions handler
   *
   * @package activeCollab.modules.timetracking
   * @subpackage handlers
   */
  
  /**
   * Handle on_system_permissions
   *
   * @param array $permissions
   * @return null
   */
  function timetracking_handle_on_system_permissions(&$permissions) {
  	$permissions[] = 'use_time_reports';
  	$permissions[] = 'manage_time_reports';
  } // timetracking_handle_on_system_permissions

?>