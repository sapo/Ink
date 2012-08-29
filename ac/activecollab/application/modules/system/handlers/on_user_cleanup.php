<?php

  /**
   * System on_user_cleanup event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle on_user_cleanup event
   *
   * @param array $cleanup
   * @return null
   */
  function system_handle_on_user_cleanup(&$cleanup) {
    if(!isset($cleanup['activity_logs'])) {
      $cleanup['activity_logs'] = array();
    } // if
    
    $cleanup['activity_logs'][] = 'created_by';
    
    if(!isset($cleanup['projects'])) {
      $cleanup['projects'] = array();
    } // if
    
    $cleanup['projects'][] = 'leader';
    $cleanup['projects'][] = 'created_by';
    $cleanup['projects'][] = 'completed_by';
    
    if(!isset($cleanup['project_objects'])) {
      $cleanup['project_objects'] = array();
    } // if
    
    $cleanup['project_objects'][] = 'created_by';
    $cleanup['project_objects'][] = 'updated_by';
    $cleanup['project_objects'][] = 'completed_by';
  } // system_handle_on_user_cleanup

?>