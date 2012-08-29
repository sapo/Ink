<?php

  /**
   * System module on_project_object_completed event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Handle object completition
   *
   * @param ProjectObject $object
   * @param User $by
   * @param string $comment
   * @return null
   */
  function system_handle_on_project_object_completed(&$object, &$by, $comment) {
    if(instance_of($object, 'ProjectObject') && $object->can_be_completed) {
      if($object->can_have_tasks) {
        $open_tasks = $object->getOpenTasks();
        if(is_foreachable($open_tasks)) {
          foreach($open_tasks as $open_task) {
            $open_task->complete($object->getCompletedBy());
          } // foreach
        } // if
      } // if
      
      if($object->can_have_subscribers) {
        $created_by = $object->getCreatedBy();
        $completed_by = $object->getCompletedBy();
        
        $template_params = array(
          'created_by_name'   => $created_by->getDisplayName(),
          'created_by_url'    => $created_by->getViewUrl(),
          'completed_by_name' => $completed_by->getDisplayName(),
          'completed_by_url'  => $completed_by->getViewUrl(),
        );
        
        if($comment) {
          $template_params['completion_comment_body'] = $comment;
          $object->sendToSubscribers('resources/task_completed_with_comment', $template_params, $by->getId(), $object->getNotificationContext());
        } else {
          $object->sendToSubscribers('resources/task_completed', $template_params, $by->getId(), $object->getNotificationContext());
        } // if
      } // if
    } // if
  } // system_handle_on_project_object_completed

?>