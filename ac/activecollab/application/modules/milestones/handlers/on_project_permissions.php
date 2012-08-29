<?php

  /**
   * Milestones handle on_project_permissions event
   *
   * @package activeCollab.modules.milestones
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   * @return null
   */
  function milestones_handle_on_project_permissions(&$permissions) {
  	$permissions['milestone'] = lang('Milestones');
  } // milestones_handle_on_project_permissions

?>