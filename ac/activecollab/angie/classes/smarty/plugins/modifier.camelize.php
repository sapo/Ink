<?php

  /**
  * Return camelized string
  *
  * @param string $string
  * @return string
  */
  function smarty_modifier_camelize($string) {
    return Inflector::camelize($string);
  } // smarty_modifier_camelize

?>