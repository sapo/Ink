<?php

  /**
  * Foramt filesize
  *
  * @param string $value
  * @return string
  */
  function smarty_modifier_filesize($value) {
    return format_file_size($value);
  } // smarty_modifier_filesize

?>