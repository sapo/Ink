<?php

  /**
   * Render button
   * 
   * Parameters:
   * 
   * - common button parameter
   * - href - when button is clicked this link is opened
   * - method - if POST that this button will be send POST request. Method works 
   *   only if href parameter is present
   * - confirm - enforces confirmation dialog
   * - not_lang - if true content will not be matched agains registered language 
   *   codes
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_button($params, $content, &$smarty, &$repeat) {
    if(!isset($params['type'])) {
      $params['type'] = 'button';
    } // if
    
    $href = '';
    if(isset($params['href'])) {
      $href = $params['href'];
      if(str_starts_with($href, '?')) {
        $href = assemble_from_string($params['href']);
      } // if
      unset($params['href']);
    } // if
    
    $confirm = '';
    if(isset($params['confirm'])) {
      $confirm = trim($params['confirm']);
      unset($params['confirm']);
    } // if
    
    $post = false;
    if(isset($params['method'])) {
      $post = strtolower($params['method']) == 'post';
      unset($params['method']);
    } // if
    
    if($href) {
      $execution = $post ? 'App.postLink(' . var_export($href, true) . ')' : 'location.href = ' . var_export($href, true);
      if($confirm) {
        $params['onclick'] = "if(confirm(" . var_export($confirm, true) . ")) { $execution; } return false;";
      } else {
        $params['onclick'] = "$execution; return false;";
      } // if
    } else {
      if($confirm) {
        $params['onclick'] = "return confirm(" . var_export($confirm, true) . ")";
      } // if
    } // if
    
    $not_lang = false;
    if(isset($params['not_lang'])) {
      $not_lang = (boolean) $params['not_lang'];
      unset($params['not_lang']);
    } // if
    
    $text = $not_lang ? $content : lang($content);
    
    return open_html_tag('button', $params) . '<span><span>' . clean($text) . '</span></span></button>';
  } // smarty_block_button

?>