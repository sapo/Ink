<?php

  /**
  * Include layout
  * 
  * Parameters:
  * 
  * - name - layout name
  * - module - module name
  *
  * @param array $params
  * @param Smarty $smarty
  * @return null
  */
  function smarty_function_include_layout($params, &$smarty) {
    $name = array_var($params, 'name');
    if(empty($name)) {
      return new InvalidParamError('name', $name, "'name' property is required for 'include_layout' helper", true);
    } // if
    
    $module = array_var($params, 'module');
    if(empty($module)) {
      return new InvalidParamError('module', $module, "'module' property is required for 'include_layout' helper", true);
    } // if
    
    return tpl_fetch(get_layout_path($name, $module));
  } // smarty_function_include_layout

?>