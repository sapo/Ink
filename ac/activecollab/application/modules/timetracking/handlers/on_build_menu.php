<?php

  /**
   * Timetracking module on_build_menu event handler
   *
   * @package activeCollab.modules.timetracking
   * @subpackage handlers
   */
  
  /**
   * Add options to main menu
   *
   * @param Menu $menu
   * @param User $user
   * @return null
   */
  function timetracking_handle_on_build_menu(&$menu, &$user) {
    if($user->isAdministrator() || $user->getSystemPermission('use_time_reports')) {
      $menu->addToGroup(array(
        new MenuItem('time', lang('Time'), assemble_url('global_time'), get_image_url('navigation/time.gif')),
      ), 'main');
    } // if
  } // timetracking_handle_on_build_menu

?>