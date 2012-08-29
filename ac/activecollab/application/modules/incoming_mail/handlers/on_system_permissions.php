<?php

  /**
   * Incoming Mail on_system_permissions handler
   *
   * @package activeCollab.modules.incoming_mail
   * @subpackage handlers
   */
  
  /**
   * Handle on_system_permissions
   *
   * @param array $permissions
   * @return null
   */
  function incoming_mail_handle_on_system_permissions(&$permissions) {
  	$permissions[] = 'can_use_incoming_mail_frontend';
  } // incoming_mail_handle_on_system_permissions

?>