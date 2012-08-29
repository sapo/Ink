<?php

  /**
  * Render input text
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
  function smarty_function_text_field($params, &$smarty) {
    $type = 'text';
    if(!empty($params['type'])) {
    	$type = array_var($params, 'type');
    	unset($params['type']);
    } // if
    $params['type'] = $type;
    
    return open_html_tag('input', $params, true);
  } // smarty_function_text_field

?>