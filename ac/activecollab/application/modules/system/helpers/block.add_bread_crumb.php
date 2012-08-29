<?php

  /**
   * add_bread_crumb helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Add bread crumb
   * 
   * Parameters:
   * 
   * - url - crumb URL, optional
   * - not_lang - use raw value, optional
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_add_bread_crumb($params, $content, &$smarty, &$repeat) {
    static $instance;
    
    $url = array_var($params, 'url');
    $not_lang = (boolean) array_var($params, 'not_lang');
    $text = $not_lang ? $content : lang($content);
    
    if(trim($text) == '') {
      return new InvalidParamError('text', $text, "Bread crumb text is required in add_bread_crumb helper");
    } // if
    
    if($instance === null) {
      $instance =& Wireframe::instance();
    } // if
    
    $instance->addBreadCrumb($text, $url);
    return '';
  } // smarty_block_add_bread_crumb

?>