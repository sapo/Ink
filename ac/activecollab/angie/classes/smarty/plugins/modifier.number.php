<?php

  /**
   * Format number (number_format function interface)
   * 
   * @param float $number
   * @param integer $num_decimal_places
   * @param string $dec_separator
   * @param string $thousands_separator
   * @return string
   */
  function smarty_modifier_number($number, $num_decimal_places = 2, $dec_separator = NUMBER_FORMAT_DEC_SEPARATOR, $thousands_separator = NUMBER_FORMAT_THOUSANDS_SEPARATOR) {
    return number_format($number, $num_decimal_places, $dec_separator, $thousands_separator);
  } // smarty_modifier_number

?>