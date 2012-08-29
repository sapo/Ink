<?php

  /**
   * Tasks file that is executed every hour
   * 
   * @package activeCollab
   * @subpackage tasks
   */
  
  require_once dirname(__FILE__) . '/init.php';
  
  echo 'Hourly event started on ' . strftime(FORMAT_DATETIME) . ".\n";
  event_trigger('on_hourly');
  ConfigOptions::setValue('last_hourly_activity', time());
  echo 'Hourly event finished on ' . strftime(FORMAT_DATETIME) . ".\n";
?>