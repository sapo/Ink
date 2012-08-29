<?php
  
  /**
   * Tasks file that is executed frequently
   * 
   * @package activeCollab
   * @subpackage tasks
   */
  
  require_once dirname(__FILE__) . '/init.php';
  
  echo 'Frequently event started on ' . strftime(FORMAT_DATETIME) . ".\n";
  event_trigger('on_frequently');
  ConfigOptions::setValue('last_frequently_activity', time());
  echo 'Frequently event finished on ' . strftime(FORMAT_DATETIME) . ".\n";

?>
