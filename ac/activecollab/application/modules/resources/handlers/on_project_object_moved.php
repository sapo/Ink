<?php

  /**
   * Resources handle on_project_object_moved event
   *
   * @package activeCollab.modules.resources
   * @subpackage handlers
   */
  
  /**
   * Handle on_project_object_moved event
   *
   * @param ProjectObject $object
   * @param Project $source
   * @param Project $destination
   * @return null
   */
  function resources_handle_on_project_object_moved(&$object, &$source, &$destination) {
    if($object->can_have_subscribers) {
      $subscribers = $object->getSubscribers();
      if(is_foreachable($subscribers)) {
        foreach($subscribers as $subscriber) {
          if(!$subscriber->isProjectMember($destination)) {
            Subscriptions::unsubscribe($subscriber, $object);
          } // if
        } // foreach
      } // if
    } // if
    
    $object_ids = array();
    
    // Relations with milestones are carried out via milestone_id field
    if (instance_of($object, 'Milestone')) {
      db_execute('UPDATE '.TABLE_PREFIX.'project_objects SET milestone_id = 0 WHERE milestone_id = ?', $object->getId());
    } // if
    
    $rows = db_execute_all('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE type IN (?) AND parent_id = ?', array('task', 'comment', 'attachment', 'timerecord'), $object->getId());
    if(is_foreachable($rows)) {
      foreach($rows as $row) {
        $object_ids[] = (integer) $row['id'];
      } // foreach
      
      // Sub-objects (attachments for comments, time records for tasks, tasks for tickets)
      $rows = db_execute_all('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE parent_id IN (?)', $object_ids);
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $object_ids[] = (integer) $row['id'];
        } // foreach
      } // if

      // Update objects and activity logs
      db_execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET project_id = ? WHERE id IN (?)', $destination->getId(), $object_ids);
      db_execute('UPDATE ' . TABLE_PREFIX . 'activity_logs SET project_id = ? WHERE object_id IN (?)', $destination->getId(), $object_ids);
      
      // Clear cache
      cache_remove_by_pattern(TABLE_PREFIX . 'activity_logs_id_*');
      cache_remove_by_pattern(TABLE_PREFIX . 'project_objects_id_*');
    } // if
  } // resources_handle_on_project_object_moved

?>