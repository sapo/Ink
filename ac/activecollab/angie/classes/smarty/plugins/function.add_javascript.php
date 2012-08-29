<?php

  /**
   * Add javascript link to the page
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_add_javascript($params, &$smarty) {
    $name = array_var($params, 'name');
    if(empty($name)) {
      return new InvalidParamError('name', $name, "'name' parameter is required for 'add_javascript' helper", true);
    } // if
    
    $module = array_var($params, 'module');
    
    if(!isset($params['type'])) {
      $params['type'] = 'text/javascript';
    } // if
    
    unset($params['name']);
    if(isset($params['module'])) {
      unset($params['module']);
    } // if
    
    PageConstruction::addScript(get_javascript_url($name, $module), false, $params);
    return '';
  } // smarty_function_add_javascript

?>