<?php

  /**
  * Dump variable value
  * 
  * Parameters:
  * 
  * - var - variable that need to be printed
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_var_dump($params, &$smarty) {
    ob_start();
    var_dump($params);
    return "<pre style=\"text-align: left\">\n" . ob_get_clean() . "</pre>";
  } // smarty_function_var_dump

?>