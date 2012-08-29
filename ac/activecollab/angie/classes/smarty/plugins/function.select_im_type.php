<?php

  /**
   * Render select IM type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_im_type($params, &$smarty) {
    $im_types = array('AIM', 'ICQ', 'MSN', 'Yahoo!', 'Jabber', 'Skype', 'Google');
    
    $value = null;
    if(isset($params['value'])) {
      $value = $params['value'];
      unset($params['value']);
    } // if
    
    $options = array();
    foreach($im_types as $im_type) {
      $option_attributes = $im_type == $value ? array('selected' => true) : null;
      $options[] = option_tag($im_type, $im_type, $option_attributes);
    } // foreach
    
    return select_box($options, $params);
  } // smarty_function_select_im_type

?>