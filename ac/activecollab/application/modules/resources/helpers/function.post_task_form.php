<?php

  /**
   * post_task_form helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render post tasks form
   *
   * Parameteres:
   * 
   * - object - Attach task to this object
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_post_task_form($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    $smarty->assign(array(
      '_post_task_form_object' => $object,
    ));
    
    return $smarty->fetch(get_template_path('_post_task_form', 'tasks', RESOURCES_MODULE));
  } // smarty_function_post_task_form

?>