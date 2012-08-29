<?php

  /**
  * This function will extend already generated URL with a set of parameters
  * 
  * Used when we have existing URL that need more parameters attached to it. 
  * Every block parameter is attached at the end of URL, in query string
  *
  * @param array $params
  * @param string $content
  * @param Smarty $smarty
  * @param boolean $repeat
  * @return string
  */
  function smarty_block_extend_url($params, $content, &$smarty, &$repeat) {
    $append = '';
    if(count($params)) {
      if(strrpos($content, '?') === false) {
        $append .= '?';
      } // if
      
      $append .= http_build_query($params);
    } // if
    
    return $content . $append;
  } // smarty_block_extend_url

?>