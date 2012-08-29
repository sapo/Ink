<?php

  /**
  * Print wrapper div around buttons
  *
  * @param array $params
  * @param string $content
  * @param Smarty $smarty
  * @param boolean $repeat
  * @return string
  */
  function smarty_block_wrap_buttons($params, $content, &$smarty, &$repeat) {
    return '<div class="buttonHolder">' . "\n$content\n" . '</div>';
  } // smarty_block_wrap_buttons

?>