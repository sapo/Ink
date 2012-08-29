<?php

  /**
   * permission_name helper definition
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render verbose permission name
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_permission_name($params, &$smarty) {
    $name = array_var($params, 'name', 'unknown');
    
    if(str_ends_with($name, '_add')) {
      return lang('Add');
    } elseif(str_ends_with($name, '_manage')) {
      return lang('Edit and Delete');
    } else {
      return $name;
    } // if
  } // smarty_function_permission_name

?>