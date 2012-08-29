<?php

  /**
  * Render input file
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
  function smarty_function_file_field($params, &$smarty) {
    $params['type'] = 'file';
    return open_html_tag('input', $params, true);
  } // smarty_function_text_field

?>