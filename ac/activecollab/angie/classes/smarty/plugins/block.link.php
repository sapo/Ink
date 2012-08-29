<?php

  /**
   * Render button
   * 
   * Parameters:
   * 
   * - common anchor parameter
   * 
   * - method - if POST that this button will be send POST request. Method works 
   *   only if href parameter is present
   * - confirm - enforces confirmation dialog
   *   codes
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_link($params, $content, &$smarty, &$repeat) {
    $href = '';
    if(isset($params['href'])) {
      $href = $params['href'];
      if(str_starts_with($href, '?')) {
        $href = assemble_from_string($params['href']);
      } // if
    } // if
    $params['href'] = $href;
    
    $confirm = '';
    if(array_key_exists('confirm', $params)) {
      $confirm = lang(trim($params['confirm']));
      unset($params['confirm']);
    } // if
    
    $post = false;
    if(array_key_exists('method', $params)) {
      if(strtolower($params['method']) == 'post') {
        $post = true;
      } // if
      unset($params['method']);
    } // if
    
    if($post || $confirm) {
      $execution = $post ? 'App.postLink(' . var_export($href, true) . ')' : 'location.href = ' . var_export($href, true);
      if($confirm) {
        $params['onclick'] = "if(confirm(" . var_export($confirm, true) . ")) { $execution; } return false;";
      } else {
        $params['onclick'] = "$execution; return false;";
      } // if
    } // if
    
    $not_lang = false;
    if(isset($params['not_lang'])) {
      $not_lang = (boolean) $params['not_lang'];
      unset($params['not_lang']);
    } // if
    
    if (array_key_exists('id', $params) && strlen($params['id']) == 0) {
      unset($params['id']);
    } // if
    
    if(array_key_exists('title', $params)) {
      $params['title'] = lang($params['title']);
    } // if
    
    $text = $not_lang ? $content : lang($content);
    
    return open_html_tag('a', $params) . $text . '</a>';
  } // smarty_block_link

?>