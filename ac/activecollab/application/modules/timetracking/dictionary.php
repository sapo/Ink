<?php

  /**
   * Timetracking module language index
   *
   * @package activeCollab.modules.timetracking
   */

  $lang_index = require dirname(__FILE__) . '/lang_index.php';
  $lang_index_js = require dirname(__FILE__) . '/lang_index_js.php';

  return array_unique(array_merge($lang_index, $lang_index_js));

?>