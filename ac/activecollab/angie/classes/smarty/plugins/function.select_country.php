<?php

  /**
  * Render select box for Countries
  * 
  * Parameters:
  * 
  * - all HTML attributes
  * - value - value of selected country
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_select_country($params, &$smarty) {
    
    $value = null;
    if(isset($params['value'])) {
      $value = (integer) array_var($params, 'value');
      unset($params['value']);
    } // if
    
    $countries = get_countries();
    
    $options = array();
    foreach($countries as $code => $country) {
      $option_attributes = $value == $code ? array('selected' => true) : null;
      $options[] = option_tag($country, $code, $option_attributes);
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_country

?>