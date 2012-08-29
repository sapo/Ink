<?php

  /**
  * Add stylesheet to selected page
  * 
  * Parameters:
  * 
  * - name - name of the CSS file
  * - module - name of the module, if null global stylesheet is used
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_add_stylesheet($params, &$smarty) {
    $name = array_var($params, 'name');
    if(empty($name)) {
      return new InvalidParamError('name', $name, "'name' parameter is required for 'add_stylesheet' helper", true);
    } // if
    
    $module = array_var($params, 'module');
    
    if(!isset($params['type'])) {
      $params['type'] = 'text/css';
    } // if
    
    unset($params['name']);
    if(isset($params['module'])) {
      unset($params['module']);
    } // if
    
    PageConstruction::addLink(get_stylesheet_url($name, $module), 'stylesheet', $params);
    return '';
  } // smarty_function_add_stylesheet

?>