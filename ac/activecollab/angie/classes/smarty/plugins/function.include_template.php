<?php

  /**
   * Include template
   * 
   * Parameters:
   * 
   * - name - layout name
   * - controller - controller name
   * - module - module name
   *
   * @param array $params
   * @param Smarty $smarty
   * @return null
   */
  function smarty_function_include_template($params, &$smarty) {
    $name = array_var($params, 'name');
    if(empty($name)) {
      return new InvalidParamError('name', $name, "'name' property is required for 'include_template' helper", true);
    } // if
    
    $module = array_var($params, 'module');
    if(empty($module)) {
      return new InvalidParamError('module', $module, "'module' property is required for 'include_template' helper", true);
    } // if
    return $smarty->fetch(get_template_path($name, array_var($params, 'controller'), $module));
  } // smarty_function_include_template

?>