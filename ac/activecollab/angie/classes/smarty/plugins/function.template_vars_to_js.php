<?php

  /**
  * Assign template vars to javascript
  * 
  * This function will make available all template vars to JavaScript by 
  * converting them to JSON
  * 
  * Parameters:
  * 
  * - domain - domain where we'll put variables. Default is 'App.data'
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_template_vars_to_js($params, &$smarty) {
    $vars = gs_get('assigned_to_js');
    if(is_foreachable($vars)) {
      $prefix = array_var($params, 'domain', 'App.data');
      $code = "if(!$prefix) { $prefix = {}; }\n";
      foreach($vars as $k => $v) {
        $code .= "$prefix.$k = " . do_json_encode($v) . ";\n";
      } // foreach
      return "<script type=\"text/javascript\">\n$code\n</script>";
    } // if
    return '';
  } // smarty_function_template_vars_to_js

?>