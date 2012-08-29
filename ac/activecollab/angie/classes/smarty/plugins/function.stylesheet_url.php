<?php

  /**
  * Return stylesheet URL
  * 
  * Parameters:
  * 
  * - name - stylesheet filename
  * - module - name of the module, if not present global data is used
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_stylesheet_url($params, &$smarty) {
    $name = array_var($params, 'name');
    //if(empty($name)) {
    //  return new InvalidParamError('name', $name, "'name' parameter is required for 'stylesheet_url' helper", true);
    //} // if
    
    return get_stylesheet_url($name, array_var($params, 'module'));
  } // smarty_function_stylesheet_url

?>