<?php

  /**
   * Render specific empty slate
   * 
   * Parameters:
   * 
   * - module - module name
   * - name - template name, default is index
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_empty_slate($params, &$smarty) {
  	$module = array_var($params, 'module', SYSTEM_MODULE);
  	$name = array_var($params, 'name', 'index');
  	
  	return $smarty->fetch(get_template_path($name, 'empty_slates', $module));
  } // smarty_function_empty_slate

?>