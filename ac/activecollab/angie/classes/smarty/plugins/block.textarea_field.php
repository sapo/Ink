<?php

  /**
   * Render textarea
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
  */
  function smarty_block_textarea_field($params, $content, &$smarty, &$repeat) {
    if(!isset($params['rows'])) {
      $params['rows'] = 10;
    } // if
    
    if(!isset($params['cols'])) {
      $params['cols'] = 48;
    } // if
    
    return open_html_tag('textarea', $params) . clean($content) . '</textarea>';
  } // smarty_block_textarea_field

?>