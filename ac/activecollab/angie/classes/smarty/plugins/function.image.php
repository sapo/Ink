<?php

  /**
   * Generate image tag based on properties (same as for image_url)
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_image($params, &$smarty) {
    require_once SMARTY_PATH . '/plugins/function.image_url.php';
    
    $name = array_var($params, 'name', null, true);
    $module = array_var($params, 'module', null, true);
    
    $params['src'] = smarty_function_image_url(array(
      'name' => $name,
      'module' => $module,
    ), $smarty);
    
    if(!isset($params['alt'])) {
      $params['alt'] = '';
    } // if
    
    return open_html_tag('img', $params, true);
  } // smarty_function_image

?>