<?php

  /**
  * Return image URL
  * 
  * Parameters:
  * 
  * - name - image filename
  * - module - name of the module, if not present global data is used
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_image_url($params, &$smarty) {
    $name = array_var($params, 'name');
    return get_image_url($name, array_var($params, 'module'));
  } // smarty_function_image_url

?>