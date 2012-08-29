<?php

  /**
   * System module on_shutdown event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Handle on shutdown
   *
   * @param void
   * @return null
   */
  function system_handle_on_shutdown() {
    ProjectObjectViews::save();
    
    // Lets kill a transaction if we have something open
    $database =& DBConnection::instance();
    if(instance_of($database, 'DBConnection') && ($database->transaction_level > 0)) {
      $database->rollback();
    } // if
    
    if(DEBUG >= DEBUG_DEVELOPMENT) {
      $logger =& Logger::instance();
      $logger->logToFile(ENVIRONMENT_PATH . '/logs/' . date('Y-m-d') . '.txt');
    } // if
  } // system_handle_on_shutdown

?>