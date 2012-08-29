<?php

  /**
   * Documents module on_build_menu event handler
   *
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */
  
  /**
   * Build menu
   *
   * @param Menu $menu
   * @param User $user
   * @return array
   */
  function documents_handle_on_build_menu(&$menu, &$user) {
    if($user->isAdministrator() || $user->getSystemPermission('can_use_documents')) {
      $menu->addToGroup(array(
        new MenuItem('documents', lang('Docs'), assemble_url('documents'), get_image_url('icon.gif', DOCUMENTS_MODULE))
      ), 'main');
    } // if
  } // documents_handle_on_build_menu

?>