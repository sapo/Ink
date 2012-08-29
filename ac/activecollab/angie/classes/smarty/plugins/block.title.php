<?php

  /**
   * Set page title to $content value
   * 
   * Parameters:
   * 
   * - not_lang - Use raw value...
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_title($params, $content, &$smarty, &$repeat) {
    if(!isset($params['not_lang'])) {
      $content = lang($content, $params, false); // Params will be cleaned by page construction
    } // if
    
    $construction =& PageConstruction::instance();
    $construction->setPageTitle($content);
    return '';
  } // smarty_block_title

?>