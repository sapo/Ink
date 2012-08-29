<?php

  /**
  * Assemble URL
  *
  * @param array $params
  * @param Smarty $smarty
  * @return null
  */
  function smarty_function_assemble($params, &$smarty) {
    $route = array_var($params, 'route');
    if(empty($route)) {
      return new InvalidParamError('route', $route, "'route' is required parameter of 'assemble' helper", true);
    } // if
    
    unset($params['route']);
    
    $options = null;
    if(isset($params['options'])) {
      $options_sring = array_var($params, 'options');
      if($options_sring) {
        parse_str($options_sring, $options);
      } // if
      unset($params['options']);
    } // if
    
    return assemble_url($route, $params, $options);
  } // smarty_function_assemble

?>