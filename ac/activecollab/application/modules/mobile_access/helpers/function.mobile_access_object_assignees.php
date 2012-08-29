<?php

  /**
   * mobile_access_object_assignees helper
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */
  
  /**
   * Render object assignees list
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mobile_access_object_assignees($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    $owner = $object->getResponsibleAssignee();
    if(!instance_of($owner, 'User')) {
      Assignments::deleteByObject($object);
      return lang('No one is responsible');
    } // if
    
    require_once SYSTEM_MODULE_PATH . '/helpers/function.user_link.php';
    
    $other_assignees = array();
    
    $assignees = $object->getAssignees();
    if(is_foreachable($assignees)) {
      foreach($assignees as $assignee) {
        if($assignee->getId() != $owner->getId()) {
          $other_assignees[] = '<a href="'.mobile_access_module_get_view_url($assignee).'">'.clean($assignee->getName()).'</a>';
        } // if
      } // foreach
    } // if
        
    if(count($other_assignees)) {
      return '<a href="'.mobile_access_module_get_view_url($owner).'">'.clean($owner->getName()).'</a> '.lang('is responsible').'. '.lang('Other assignees').': ' . implode(', ', $other_assignees);
    } else {
      return '<a href="'.mobile_access_module_get_view_url($owner).'">'.clean($owner->getName()).'</a> '.lang('is responsible').'.';
    } // if
  } // smarty_function_object_assignees

?>