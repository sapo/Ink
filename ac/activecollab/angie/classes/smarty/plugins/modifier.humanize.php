<?php

  /**
  * Return humanized string
  *
  * @param string $string
  * @return string
  */
  function smarty_modifier_humanize($string) {
    return Inflector::humanize($string);
  } // smarty_modifier_humanize

?>