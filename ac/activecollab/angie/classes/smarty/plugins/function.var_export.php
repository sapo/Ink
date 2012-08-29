<?php

  /**
  * Export variable as value PHP string that can reconstruct it
  * 
  * Parameters:
  * 
  * - $var - Variable that need to be exported
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_var_export($params, &$smarty) {
    return var_export(array_var($params, 'var'), true);
  } // smarty_function_var_export

?>