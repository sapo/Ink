<?php

  /**
   * System on_system_permissions handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Handle on_system_permissions
   *
   * @param array $permissions
   * @return null
   */
  function system_handle_on_system_permissions(&$permissions) {
  	$permissions = array_merge($permissions, array(
  	  'system_access',
  	  'admin_access',
  	  'project_management',
  	  'people_management',
  	  'add_project',
  	  'manage_company_details',
  	  'can_see_private_objects',
  	  'manage_trash',
  	));
  } // system_handle_on_system_permissions

?>