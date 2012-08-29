<?php

  /**
   * Return lang for a given code text and parameters
   * 
   * Paramteres:
   * 
   * - clean_params - boolean - Clean params before they are inserted in string, 
   *   true by default
   * - language - Language - Force translation it his language
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_lang($params, $content, &$smarty, &$repeat) {
    $clean_params = isset($params['clean_params']) ? (boolean) $params['clean_params'] : true; // true by default
    $language = isset($params['language']) && $params['language'] ? $params['language'] : null;
    
    return lang($content, $params, $clean_params, $language);
  } // smarty_block_lang

?>