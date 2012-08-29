<?php

  /**
   * mobile_access_get_task_toggle_url
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */

  /**
   * returns toggle completed status for object
   * 
   * - object - object
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mobile_access_get_task_toggle_url($params, &$smarty) {
    return mobile_access_module_get_task_toggle_url(array_var($params, 'object', null));
  } // smarty_function_mobile_access_get_view_url

?>