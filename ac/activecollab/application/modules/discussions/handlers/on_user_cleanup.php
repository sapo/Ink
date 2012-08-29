<?php

  /**
   * Discussions on_user_cleanup event handler
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Handle on_user_cleanup event
   *
   * @param array $cleanup
   * @return null
   */
  function discussions_handle_on_user_cleanup(&$cleanup) {
    if(!isset($cleanup['project_objects'])) {
      $cleanup['project_objects'] = array();
    } // if
    
    $cleanup['project_objects'][] = array(
      'id' => 'integer_field_1',
      'name' => 'varchar_field_1',
      'email' => 'varchar_field_2',
      'condition' => 'type = ' . db_escape('Discussion'),
    );
  } // discussions_handle_on_user_cleanup

?>