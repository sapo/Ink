<?php

  /**
  * Render select box for for Yes, No, Use Global
  * 
  * Parameters:
  * 
  * - all HTML attributes
  * - value - value of selected option
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_select_yes_no_none($params, &$smarty) {
    
    $value = null;
    if(isset($params['value'])) {
      $value = (boolean) array_var($params, 'value');
      unset($params['value']);
    } // if
    
    $options[] = option_tag(lang('Use Global Settings'), '', empty($value) ? array('selected' => true) : null);
    $options[] = option_tag(lang('No'), 0, $value == false ? array('selected' => true) : null);
    $options[] = option_tag(lang('Yes'), 1, $value == true ? array('selected' => true) : null);
    
    return select_box($options, $params);
  } // smarty_function_select_yes_no_none

?>