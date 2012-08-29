<?php

  /**
  * pre_var_dump object
  * 
  * - object
  *
  * @param array $params
  * @param Smarty $smarty
  * @return null
  */
  function smarty_function_pre_var_dump($params, &$smarty) {
    return "<pre>".var_dump(array_var($params, 'object', null), true)."</pre>";
  } // smarty_function_pre_var_dump

?>