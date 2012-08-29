<?php

  /**
   * object_tasks helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render object tasks section
   * 
   * Parameters:
   * 
   * - object - Selected project object
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_tasks($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    $open_tasks = $object->getOpenTasks();
    if(is_foreachable($open_tasks)) {
      foreach($open_tasks as $open_task) {
        ProjectObjectViews::log($open_task, $smarty->get_template_vars('logged_user'));
      } // foreach
    } // if
    
    $completed_tasks = $object->getCompletedTasks(COMPLETED_TASKS_PER_OBJECT);
    if(is_foreachable($completed_tasks)) {
      foreach($completed_tasks as $completed_task) {
        ProjectObjectViews::log($completed_task, $smarty->get_template_vars('logged_user'));
      } // foreach
    } // if
    
    $smarty->assign(array(
      '_object_tasks_object' => $object,
      '_object_tasks_open' => $open_tasks,
      '_object_tasks_can_reorder' => (int) $object->canEdit($smarty->get_template_vars('logged_user')),
      '_object_tasks_completed' => $completed_tasks,
      '_object_tasks_completed_remaining' => $object->countCompletedTasks() - COMPLETED_TASKS_PER_OBJECT,
      '_object_tasks_skip_wrapper' => array_var($params, 'skip_wrapper', false),
      '_object_tasks_skip_head' => array_var($params, 'skip_head', false),
      '_object_tasks_force_show' => array_var($params, 'force_show', false),
    ));
    
    return $smarty->fetch(get_template_path('_object_tasks', 'tasks', RESOURCES_MODULE));
  } // smarty_function_object_tasks

?>