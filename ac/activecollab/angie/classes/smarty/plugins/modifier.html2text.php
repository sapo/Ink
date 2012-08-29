<?php

  /**
   * HTML 2 Text modifier
   *
   * @package angie.library.Smarty
   */

  /**
   * Convert HTML to text
   *
   * @param string $content
   * @return string
   */
  function smarty_modifier_html2text($content) {
    return html_to_text($content);
  } // smarty_modifier_html2text

?>