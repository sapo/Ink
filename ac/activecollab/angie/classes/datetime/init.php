<?php

  /**
   * Initial datetime values
   *
   * @package angie.library.datetime
   */
  
  define('DATETIME_LIB_PATH', ANGIE_PATH . '/classes/datetime');
  
  require_once DATETIME_LIB_PATH . '/DateValue.class.php';
  require_once DATETIME_LIB_PATH . '/DateTimeValue.class.php';
  require_once DATETIME_LIB_PATH . '/Timezone.class.php';
  require_once DATETIME_LIB_PATH . '/Timezones.class.php';
  
  ini_set('date.timezone', 'GMT');
  if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set('GMT');
  } else {
    @putenv('TZ=GMT'); // Don't throw a warning if system in safe mode
  } // if
  
  /**
   * Check if $date is in range between $from and $to
   *
   * @param DateValue $date
   * @param DateValue $from
   * @param DateValue $to
   * @return boolean
   */
  function date_in_range($date,$from,$to) {
    return ($date->getTimestamp() >= $from->getTimestamp()) && ($date->getTimestamp() <= $to->getTimestamp());
  } // date_in_range
       
?>