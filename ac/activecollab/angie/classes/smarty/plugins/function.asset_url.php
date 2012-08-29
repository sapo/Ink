<?php

  /**
  * Return asset URL
  * 
  * Params:
  * 
  * - name - resource path relative to assets folder
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_asset_url($params, &$smarty) {
    $name = array_var($params, 'name');
    return get_asset_url($name, array_var($params, 'module'));
  } // smarty_function_asset_url

?>