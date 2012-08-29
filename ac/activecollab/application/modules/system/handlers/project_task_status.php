<?php

  /**
   * System module project_task_status handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Handle project task object status change
   *
   * @param ProjectObject $object
   * @return null
   */
  function system_handle_project_task_status($object) {    
    if(instance_of($object, 'ProjectObject')) {
      if($object->can_be_completed) {
        $project = $object->getProject();
        if(instance_of($project, 'Project')) {
          $project->refreshTasksCount();
        } // if
      } // if
    } // if
  } // system_handle_project_task_status

?>