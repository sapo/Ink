<?php

  /**
   * mobile_access_object_tasks helper
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */
  
  /**
   * Shows a list of tasks that are attached to object
   * 
   * Parameters:
   * 
   * - object - parent object
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
    
  function smarty_function_mobile_access_object_tasks($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    $completed_tasks = $object->getCompletedTasks();
    $active_tasks = $object->getOpenTasks();
    
    $smarty->assign(array(
      '_mobile_access_object_tasks_completed'                 => $completed_tasks,
      '_mobile_access_object_tasks_active'                    => $active_tasks,
    ));
    return $smarty->fetch(get_template_path('_object_tasks', null, MOBILE_ACCESS_MODULE));
  } // smarty_function_mobile_access_object_tasks

?>