<?php

  /**
   * Extract excerpt from HTML text
   *
   * @param string $text
   * @return string
   */
  function smarty_modifier_html_excerpt($text) {
    $text = strip_tags($text);
    $text = str_ireplace(array('<h1>', '</h1>', '<h2>', '</h2>'), array('<p>', '</p>', '<p>', '</p>'), $text);
    
    return $text;
  } // smarty_modifier_html_excerpt

?>