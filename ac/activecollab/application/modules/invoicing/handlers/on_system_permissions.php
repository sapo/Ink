<?php

  /**
   * Invoicing on_system_permissions handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */
  
  /**
   * Handle on_system_permissions
   *
   * @param array $permissions
   * @return null
   */
  function invoicing_handle_on_system_permissions(&$permissions) {
  	$permissions[] = 'can_manage_invoices';
  } // invoicing_handle_on_system_permissions

?>