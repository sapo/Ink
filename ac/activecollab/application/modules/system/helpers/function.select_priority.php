<?php

  /**
   * select_priority helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Select priority control
   * 
   * Params:
   * 
   * - Commong SELECT attributes
   * - Value - Selected priority
   *
   * @param array $params
   * @return string
   */
  function smarty_function_select_priority($params, &$smarty) {
    $priorities = array(
      PRIORITY_HIGHEST => lang('Highest'),
      PRIORITY_HIGH    => lang('High'),
      PRIORITY_NORMAL  => lang('Normal'),
      PRIORITY_LOW     => lang('Low'),
      PRIORITY_LOWEST  => lang('Lowest'),
    );
    
    $value = 0;
    if(isset($params['value'])) {
      $value = (integer) $params['value'];
      if($value > PRIORITY_HIGHEST || $value < PRIORITY_LOWEST) {
        $value = 0;
      } // if
      unset($params['value']);
    } // if
    
    $options = array();
    foreach($priorities as $priority => $priority_text) {
      $option_attribites = $priority == $value ? array('selected' => true) : null;
      $options[] = option_tag($priority_text, $priority, $option_attribites);
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_priority

?>