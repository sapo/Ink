<?php

  /**
   * Resources handle on_project_object_copied event
   *
   * @package activeCollab.modules.resources
   * @subpackage handlers
   */
  
  /**
   * Hnalde on_project_object_copied event
   *
   * @param ProjectObject $original
   * @param ProjectObject $copy
   * @param Project $destination
   * @param mixed $cascade
   * @return null
   */
  function resources_handle_on_project_object_copied(&$original, &$copy, &$destination, $cascade) {
    if($original->can_have_subscribers) {
      $subscribers = $original->getSubscribers();
      if(is_foreachable($subscribers)) {
        foreach($subscribers as $subscriber) {
          if($subscriber->isProjectMember($destination)) {
            Subscriptions::subscribe($subscriber, $copy);
          } // if
        } // foreach
      } // if
    } // if
    
    if($original->can_have_assignees) {
      Assignments::cloneAssignments($original, $copy);
    } // if
    
    if($original->can_have_attachments) {
      Attachments::cloneAttachments($original, $copy);
    } // if
    
    // Copy child objects
    if($cascade === true || is_foreachable($cascade)) {
      if($cascade === true) {
        $rows = db_execute_all('SELECT * FROM ' . TABLE_PREFIX . 'project_objects WHERE parent_id = ?', $original->getId());
      } else {
        $rows = db_execute_all('SELECT * FROM ' . TABLE_PREFIX . 'project_objects WHERE parent_id = ? AND type IN (?)', $original->getId(), $cascade);
      } // if
      
      if(is_foreachable($rows)) {
        
        // We'll remember original and copy tasks ID-s here so we can move 
        // assignments later on, when we have both instances
        $tasks = array(); 
        
        foreach($rows as $row) {
          $subobject_original_id = $row['id'];
          $subobject_original_type = strtolower($row['type']);
          
          unset($row['id']);
          
          $row['project_id']  = $destination->getId();
          $row['parent_id'] = $copy->getId();
          $row['milestone_id'] = 0;
          
          // Copy file
          if($subobject_original_type == 'attachment') {
            $path = UPLOAD_PATH . '/' . $row['varchar_field_1'];
            
            if(is_file($path)) {
              $destination_file = get_available_uploads_filename();              
              if(copy($path, $destination_file)) {
                $row['varchar_field_1'] = basename($destination_file);
              } // if
            } // if
          } // if
          
          // Escape values
          foreach($row as $k => $v) {
            $row[$k] = db_escape($v);
          } // foreach
          
          db_execute('INSERT INTO ' . TABLE_PREFIX . 'project_objects (' . implode(', ', array_keys($row)) . ') VALUES (' . implode(', ', $row) . ')');
          
          if($subobject_original_type == 'task') {
            $tasks[$subobject_original_id] = db_last_insert_id();
          } // if
        } // foraech
        
        // Lets move task assinments if we have any tasks
        if(is_foreachable($tasks)) {
          foreach($tasks as $task_original_id => $task_copy_id) {
            $task_original = Tasks::findById($task_original_id);
            $task_copy = Tasks::findById($task_copy_id);
            
            if(instance_of($task_original, 'Task') && instance_of($task_copy, 'Task')) {
              Assignments::cloneAssignments($task_original, $task_copy);
              Subscriptions::cloneSubscriptions($task_original, $task_copy);
            } // if
          } // foreach
        } // if
      } // if
    } // if
  } // resources_handle_on_project_object_copied

?>