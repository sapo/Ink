<?php

  /**
   * object_time helper definition
   * 
   * Reason why this helper needs to be here is because it is used across entire 
   * system and other modules may required it without check if timetracking 
   * module is installed
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */

  /**
   * Render object time widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_time($params, &$smarty) {
    if(!module_loaded('timetracking')) {
      return '';
    } // if
    
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('$object', $object, '$object is expected to be a valid instance of ProjectObject class');
    } // if
    
    $show_time = '';
    $additional_class = '';
    if(array_var($params, 'show_time', true)) {
      $object_time = TimeRecords::sumObjectTime($object);
      if($object->can_have_tasks) {
        $tasks_time = TimeRecords::sumTasksTime($object);
      } else {
        $tasks_time = 0;
      } // if
      
      $additional_class = 'with_text';
      
      $total_time = $object_time + $tasks_time;
      if($object_time == 0 && $tasks_time == 0) {
        $show_time = '<span class="time_widget_text">' . lang('No time tracked') . '</span> ';
      } elseif($tasks_time == 0) {
        $show_time = '<span class="time_widget_text">' . lang(':total hours logged', array(
          'total' => float_format($total_time, 2),
        )) . '</span> ';
      } else {
        $show_time = '<span class="time_widget_text">' . lang(':total hours logged - :object_time for the ticket and :tasks_time for tasks', array(
          'type'        => $object->getVerboseType(true),
          'total'       => float_format($total_time, 2),
          'object_time' => float_format($object_time, 2),
          'tasks_time'  => float_format($tasks_time, 2),
        )) . '</span> ';
      } // if
    } // if
    
    $wrapper_id = 'object_time_widget_' . $object->getId();
    $image_url = $object->getHasTime() ? get_image_url('clock-small.gif') : get_image_url('gray-clock-small.gif');
    
    return '<span id="' . $wrapper_id . '" class="time_popup_widget ' . $additional_class . '">' . $show_time . '<a href="' . $object->getTimeUrl() . '" title="' . lang('Time') . '"><img src="' . $image_url . '" alt="" /></a></span><script type="text/javascript">App.TimePopup.init("' . $wrapper_id . '")</script>';
  } // smarty_function_object_time

?>