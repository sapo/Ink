<?php
  
  /**
   * Tasks file that is executed every day
   * 
   * @package activeCollab
   * @subpackage tasks
   */

  require_once dirname(__FILE__) . '/init.php';
  
  echo 'Daily event started on ' . strftime(FORMAT_DATETIME) . ".\n";
  event_trigger('on_daily');
  ConfigOptions::setValue('last_daily_activity', time());
  echo 'Daily event finished on ' . strftime(FORMAT_DATETIME) . ".\n";
?>