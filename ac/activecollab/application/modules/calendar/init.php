<?php

  /**
   * Calendar module initialization file
   * 
   * @package activeCollab.modules.calendar
   */
  
  define('CALENDAR_MODULE', 'calendar');
  define('CALENDAR_MODULE_PATH', APPLICATION_PATH . '/modules/calendar');
  
  set_for_autoload('Calendar', CALENDAR_MODULE_PATH . '/models/Calendar.class.php');
  
  /**
   * Returns true if $user can see $profile calendar
   *
   * @param User $user
   * @param User $profile
   * @return boolean
   */
  function can_access_profile_calendar($user, $profile) {
    return $user->isProjectManager();
  } // can_access_profile_calendar

?>