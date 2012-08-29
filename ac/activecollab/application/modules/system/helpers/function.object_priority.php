<?php

  /**
   * object_priority helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Return object priority icon
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_priority($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is not valid instance of ProjectObject class', true);
    } // if
    
    if(!$object->can_be_completed) {
      return '';
    } // if
    
    switch($object->getPriority()) {
      case PRIORITY_HIGHEST:
        $icon_url = get_image_url('icons/priority/highest.gif');
        $icon_tip = lang('Highest Priority');
        break;
      case PRIORITY_HIGH:
        $icon_url = get_image_url('icons/priority/high.gif');
        $icon_tip = lang('High Priority');
        break;
      case PRIORITY_NORMAL:
        $icon_url = get_image_url('icons/priority/normal.gif');
        $icon_tip = lang('Normal Priority');
        break;
      case PRIORITY_LOW:
        $icon_url = get_image_url('icons/priority/low.gif');
        $icon_tip = lang('Low Priority');
        break;
      case PRIORITY_LOWEST:
        $icon_url = get_image_url('icons/priority/lowest.gif');
        $icon_tip = lang('Lowest Priority');
        break;
    } // switch
    
    return '<img src="' . $icon_url . '" alt="" title="' . $icon_tip .'" />';
  } // smarty_function_object_priority

?>