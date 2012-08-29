<?php

  /**
  * Clean special chars
  *
  * @param string $content
  * @return string
  */
  function smarty_modifier_clean($content) {
    return clean($content);
  } // smarty_modifier_clean

?>