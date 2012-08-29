<?php

  /**
   * Checklists handle on_project_permissions event
   *
   * @package activeCollab.modules.checklists
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   * @return null
   */
  function checklists_handle_on_project_permissions(&$permissions) {
  	$permissions['checklist'] = lang('Checklists');
  } // checklists_handle_on_project_permissions

?>