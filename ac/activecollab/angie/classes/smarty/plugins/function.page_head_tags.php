<?php

  /**
  * Render head tags collected form PageConstruction
  * 
  * No parameters expected
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_page_head_tags($params, &$smarty) {
    $instance =& PageConstruction::instance();
    $tags = $instance->getAllHeadTags();
    
    return is_foreachable($tags) ? implode("\n", $tags) : '';
  } // smarty_function_page_head_tags

?>