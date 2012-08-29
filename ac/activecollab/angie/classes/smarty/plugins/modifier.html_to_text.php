<?php

  /**
   * Convet HTML content into plain text
   *
   * @param string $content
   * @return string
   */
  function smarty_modifier_html_to_text($content) {
    return html_to_text($content);
  } // smarty_modifier_html_to_text

?>