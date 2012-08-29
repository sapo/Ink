<?php

  /**
  * Render select box for timezone
  * 
  * Parameters:
  * 
  * - all HTML attributes
  * - value - value of selected timezone
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_select_timezone($params, &$smarty) {
    $value = array_var($params, 'value', 0, true);
    $optional = array_var($params, 'optional', true, true);
    
    $timezones = Timezones::getAll();
    
    $options = array();
    if($optional) {
      $options[] = option_tag(lang('-- Select timezone --'), '');
    } // if
    
    foreach($timezones as $timezone) {
      $option_attributes = $value == $timezone->getOffset() ? array('selected' => true) : null;
      $options[] = option_tag($timezone->__toString(), $timezone->getOffset(), $option_attributes);
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_timezone

?>