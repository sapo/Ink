<?php

  /**
   * Timetracking handle on_project_permissions event
   *
   * @package activeCollab.modules.timetracking
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   * @return null
   */
  function timetracking_handle_on_project_permissions(&$permissions) {
  	$permissions['timerecord'] = lang('Time Records');
  } // timetracking_handle_on_project_permissions

?>