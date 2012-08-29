<?php

  /**
   * Status module on_build_menu event handler
   *
   * @package activeCollab.modules.status
   * @subpackage handlers
   */
  
  /**
   * Build menu
   *
   * @param Menu $menu
   * @param User $user
   * @return array
   */
  function status_handle_on_build_menu(&$menu, &$user) {
    if($user->isAdministrator() || $user->getSystemPermission('can_use_status_updates')) {
      $last_visit = UserConfigOptions::getValue('status_update_last_visited', $user);
      
      $menu->addToGroup(array(
        new MenuItem('status', lang('Status'), assemble_url('status_updates'), get_image_url('icon_menu.gif', STATUS_MODULE), StatusUpdates::countNewMessagesForUser($user, $last_visit))
      ), 'main');
    } // if
  } // status_handle_on_build_menu

?>