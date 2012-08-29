<?php

  /**
   * Excerpt modifier definition
   */

  /**
   * Return excerpt from string
   *
   * @param string $string
   * @param integer $lenght
   * @param string $etc
   * @param boolean $flat
   * @return string
   */
  function smarty_modifier_excerpt($string, $lenght = 100, $etc = '...', $flat = false) {
    $text = $flat ? strip_tags($string) : $string;
    return str_excerpt($text, $lenght, $etc);
  } // smarty_modifier_excerpt

?>