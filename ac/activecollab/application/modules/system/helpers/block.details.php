<?php

  /**
   * details helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Set page details for current page
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_details($params, $content, &$smarty, &$repeat) {
    $wireframe =& Wireframe::instance();
    $wireframe->details = $content;
    return '';
  } // smarty_block_details

?>