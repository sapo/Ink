<?php
  
  /**
   * Select weekday control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_week_day($params, &$smarty) {
    $value = null;
    if(isset($params['value'])) {
      $value = (integer) array_var($params, 'value');
      unset($params['value']);
    } // if
    
    $days = array(
      0 => lang('Sunday'),
      1 => lang('Monday'),
      2 => lang('Tuesday '),
      3 => lang('Wednesday'),
      4 => lang('Thursday'),
      5 => lang('Friday'),
      6 => lang('Saturday'),
    );
    
    foreach($days as $key => $day) {
      $option_attributes = $value == $key ? array('selected' => true) : null;
    	
      $options[] = option_tag($day, $key, $option_attributes);
    } // foreach
    
    return select_box($options, $params);
  } // smarty_function_select_week_day

?>