<?php

  /**
   * milestone_link helper
   *
   * @package activeCollab.modules.milestones
   * @subpackage helpers
   */
  
  /**
   * Render milestone link
   * 
   * Parameters:
   * 
   * - milestone - Milestone instance that need to be linked
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_milestone_link($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    $milestone = $object->getMilestone();
    if(instance_of($milestone, 'Milestone')) {
      return '<a href="' . $milestone->getViewUrl() . '">' . clean($milestone->getName()) . '</a>';
    } else {
      return '<span class="unknown_milestone unknown_object_link">' . clean(lang('Unknown Milestone')) . '</span>';
    } // if
  } // smarty_function_milestone_link

?>