<?php

  /**
   * Resources module on_system_permissions handler
   *
   * @package activeCollab.modules.resources
   * @subpackage handlers
   */

  /**
   * Handle on_system_permissions event
   *
   * @param array $permissions
   * @return null
   */
  function resources_handle_on_system_permissions(&$permissions) {
    $permissions[] = 'manage_assignment_filters';
  } // resources_handle_on_system_permissions

?>