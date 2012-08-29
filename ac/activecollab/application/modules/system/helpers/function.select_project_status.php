<?php

  /**
   * select_project_status helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select project status control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project_status($params, &$smarty) {
    $possible_values = array(
      PROJECT_STATUS_ACTIVE => lang('Active'),
      PROJECT_STATUS_PAUSED => lang('Paused'),
      PROJECT_STATUS_COMPLETED => lang('Completed'),
      PROJECT_STATUS_CANCELED => lang('Canceled'),
    );
    
    $value = null;
    if(isset($params['value'])) {
      $value = $params['value'];
      unset($params['value']);
    } // if
    
    $options = array();
    foreach($possible_values as $k => $v) {
      $option_attributes = $k == $value ? array('selected' => true) : null;
      $options[] = option_tag($v, $k, $option_attributes);
    } // foreach
    
    return select_box($options, $params);
  } // smarty_function_select_project_status

?>