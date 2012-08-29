<?php

  /**
   * Set module information
   *
   * @package activeCollab.modules.mobile_access
   */

  // This file need to be included from the Module object
  if(!isset($this) || !instance_of($this, 'Module')) {
    return;
  } // if
  
  // Set info
  $this->info = array(
    'description' => 'Interface optimized for mobile devices (iPhone, Opera, S60 etc)',
    'version' => '1.1',
    'uninstall_message' => 'Module will be deactivated. Data inserted using this module will not be deleted.',
  );

?>