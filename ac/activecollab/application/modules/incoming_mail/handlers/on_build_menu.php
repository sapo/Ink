<?php

  /**
   * Incoming Mail module on_build_menu event handler
   *
   * @package activeCollab.modules.incoming_mail
   * @subpackage handlers
   */
  
  /**
   * Build menu
   *
   * @param Menu $menu
   * @param User $user
   * @return array
   */
  function incoming_mail_handle_on_build_menu(&$menu, &$user) {
    if(($user->isAdministrator() || $user->getSystemPermission('can_use_incoming_mail_frontend')) && (($count_pending = IncomingMails::countPending()) > 0)) {
      $menu->addToGroup(array(
        new MenuItem('incoming_mail', lang('Inbox'), assemble_url('incoming_mail'), get_image_url('icon_menu.gif', INCOMING_MAIL_MODULE), $count_pending)
      ), 'main');
    } // if
  } // incoming_mail_handle_on_build_menu

?>