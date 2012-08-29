<?php

  /**
   * select_tags helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render select tags control
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_tags($params, &$smarty) {
    $value = array_var($params, 'value', null, true);
    if(is_array($value)) {
      $value = implode(', ', $value);
    } // if
    
    $params['type'] = 'text';
    $params['value'] = $value;
    
    return open_html_tag('input', $params, true);
  } // smarty_function_select_tags

?>