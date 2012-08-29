<?php

  /**
   * Calendar module on_user_options event handler
   *
   * @package activeCollab.modules.calendar
   * @subpackage handlers
   */
  
  /**
   * Return array of options $logged_user can do to $user account
   *
   * @param User $user
   * @param NamedList $options
   * @param User $logged_user
   * @return null
   */
  function calendar_handle_on_user_options(&$user, &$options, &$logged_user) {
    if(can_access_profile_calendar($logged_user, $user)) {
      $options->add('calendar', array(
        'text' => lang('Schedule'),
        'icon' => get_image_url('gray-calendar.gif'),
        'url'  => Calendar::getProfileCalendarUrl($user),
      ));
    } // if
  } // calendar_handle_on_user_options

?>