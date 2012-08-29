<?php
  /**
   * Status module initialization file
   * 
   * @package activeCollab.modules.status
   */
  
  define('STATUS_MODULE', 'status');
  define('STATUS_MODULE_PATH', APPLICATION_PATH . '/modules/status'); 
  
  use_model('status_updates', STATUS_MODULE);
?>