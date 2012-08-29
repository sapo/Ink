<?php

  /**
   * Documents on_system_permissions handler
   *
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */
  
  /**
   * Handle on_system_permissions
   *
   * @param array $permissions
   * @return null
   */
  function documents_handle_on_system_permissions(&$permissions) {
    $permissions[] = 'can_use_documents';
    $permissions[] = 'can_add_documents';
  } // documents_handle_on_system_permissions

?>