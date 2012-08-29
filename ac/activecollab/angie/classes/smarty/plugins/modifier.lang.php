<?php

  /**
   * Lang modifier
   *
   * @param string $content
   * @return string
   */
  function smarty_modifier_lang($content) {
    return lang($content);
  } // smarty_modifier_lang

?>