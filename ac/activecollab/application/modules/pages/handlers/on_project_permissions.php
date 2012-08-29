<?php

  /**
   * Pages handle on_project_permissions event
   *
   * @package activeCollab.modules.pages
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   * @return null
   */
  function pages_handle_on_project_permissions(&$permissions) {
  	$permissions['page'] = lang('Pages');
  } // pages_handle_on_project_permissions

?>