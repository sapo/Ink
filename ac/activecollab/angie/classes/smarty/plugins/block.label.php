<?php

  /**
  * Render label
  * 
  * Paramteres:
  * 
  * - after_text - text that will be put after label text. Default is ''
  * - required - puts a star after label text if this field is required
  *
  * @param array $params
  * @param string $connect
  * @param Smarty $smarty
  * @param boolean $repeat
  * @return string
  */
  function smarty_block_label($params, $content, &$smarty, &$repeat) {
    $after_text = '';
    if(isset($params['after_text'])) {
      $after_text = $params['after_text'];
      unset($params['after_text']);
    } // if
    
    $is_required = false;
    if(isset($params['required'])) {
      $is_required = (boolean) $params['required'];
      unset($params['required']);
    } // if
    
    $not_lang = (boolean) array_var($params, 'not_lang');
    
    $text = $not_lang ? $content : lang($content);
    
    $render_text = trim($text) . $after_text;
    if($is_required) {
      $render_text = $render_text.'<em>*</em>';
    } // if
    
    return open_html_tag('label', $params) . $render_text . '</label>';
  } // smarty_block_label

?>