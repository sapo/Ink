<?php

  /**
   * Discussions handle on_project_permissions event
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   * @return null
   */
  function discussions_handle_on_project_permissions(&$permissions) {
  	$permissions['discussion'] = lang('Discussions');
  } // discussions_handle_on_project_permissions

?>