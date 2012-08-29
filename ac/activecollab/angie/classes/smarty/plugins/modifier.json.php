<?php

  /**
   * Encode data to JSON
   *
   * @param mixed $data
   * @return string
   */
  function smarty_modifier_json($data) {
    require_once ANGIE_PATH . '/classes/json/init.php';
    return do_json_encode($data);
  } // smarty_modifier_json

?>