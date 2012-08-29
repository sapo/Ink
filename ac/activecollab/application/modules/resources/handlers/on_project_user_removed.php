<?php

  /**
   * Resources module on_project_user_removed event handler
   *
   * @package activeCollab.modules.resources
   * @subpackage handlers
   */

  /**
   * Handle on_project_user_removed event
   *
   * @param Project $project
   * @param User $user
   * @return null
   */
  function resources_handle_on_project_user_removed($project, $user) {
    $rows = db_execute('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ?', $project->getId());
    if(is_foreachable($rows)) {
      $object_ids = array();
      foreach($rows as $row) {
        $object_ids[] = (integer) $row['id'];
      } // foreach
      
      $user_id = $user->getId();
      
      // Assignments cleanup
      db_execute('DELETE FROM ' . TABLE_PREFIX . 'assignments WHERE user_id = ? AND object_id IN (?)', $user_id, $object_ids);
      cache_remove('object_starred_by_' . $user_id);
      cache_remove('object_assignments_*');
      cache_remove('object_assignments_*_rendered');
      
      // Starred objects cleanup
      db_execute('DELETE FROM ' . TABLE_PREFIX . 'starred_objects WHERE user_id = ? AND object_id IN (?)', $user_id, $object_ids);
      cache_remove('object_starred_by_' . $user_id);
      
      // Subscriptions cleanup
      db_execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE user_id = ? AND parent_id IN (?)', $user_id, $object_ids);
      cache_remove('user_subscriptions_' . $user_id);
    } // if
  } // resources_handle_on_project_user_removed

?>