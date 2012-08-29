<?php

  /**
  * Render password field
  * 
  * Parameters:
  * 
  * - name - field name
  * - value - initial value
  * - array of additional attributes
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_password_field($params, &$smarty) {
    $params['type'] = 'password';
    return open_html_tag('input', $params, true);
  } // smarty_function_password_field

?>