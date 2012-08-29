<?php

  /**
  * Render select box for USA States
  * 
  * Parameters:
  * 
  * - all HTML attributes
  * - value - value of selected company
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_select_usa_state($params, &$smarty) {
    
    $value = null;
    if(isset($params['value'])) {
      $value = (integer) array_var($params, 'value');
      unset($params['value']);
    } // if
    
    $states = get_usa_states();
    
    $options = array();
    foreach($states as $code => $state) {
      $option_attributes = $value == $code ? array('selected' => true) : null;
      $options[] = option_tag($state, $code, $option_attributes);
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_usa_state

?>