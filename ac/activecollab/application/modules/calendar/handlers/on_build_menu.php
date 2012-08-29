<?php

  /**
   * Calendar module on_build_menu event handler
   *
   * @package activeCollab.modules.calendar
   * @subpackage handlers
   */
  
  /**
   * Add options to main menu
   *
   * @param Menu $menu
   * @param User $user
   * @return null
   */
  function calendar_handle_on_build_menu(&$menu, &$user) {
    $menu->addToGroup(array(
      new MenuItem('calendar', lang('Calendar'), Calendar::getDashboardCalendarUrl(), get_image_url('navigation/calendar.gif')),
    ), 'main');
  } // calendar_handle_on_build_menu

?>