<?php

/**
 * Select_repository helper
 *
 * @package activeCollab.modules.source
 * @subpackage helpers
 */


/**
 * Select repository widget
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_select_repository_update_type($params, &$smarty) {
  
  $selected = null;
  if(isset($params['selected'])) {
    $selected = $params['selected'];
    unset($params['selected']);
  } // if

  $options = array();
  foreach ($params['data'] as $key=>$item) {
    $option_attributes = $key == $selected ? array('selected' => true) : null;
    $options[] = option_tag($item, $key, $option_attributes);
  }

  return select_box($options, $params);
} // smarty_function_select_repository

?>