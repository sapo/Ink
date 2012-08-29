<?php

  /**
   * scheduled_task_url handler implementation file
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Return task URL based on parameters
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_scheduled_task_url($params, &$smarty) {
    if(defined('PROTECT_SCHEDULED_TASKS') && PROTECT_SCHEDULED_TASKS) {
      $url_params = array(
        'code' => substr()
      );
    } else {
      $url_params = null;
    } // if
    
    $task = array_var($params, 'task');
    if($task && in_array($task, array(SCHEDULED_TASK_FREQUENTLY, SCHEDULED_TASK_HOURLY, SCHEDULED_TASK_DAILY))) {
      return assemble_url($task, $url_params);
    } else {
      return '';
    } // if
  } // smarty_function_scheduled_task_url

?>