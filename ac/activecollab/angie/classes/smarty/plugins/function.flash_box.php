<?php

  /**
   * Render flash box (success or error message)
   */

  /**
   * Render smarty flash box
   * 
   * No parameters expected
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_flash_box($params, &$smarty) {
    if($message = flash_get('success')) {
      $id = 'success';
    } elseif($message = flash_get('error')) {
      $id = 'error';
    } else {
      return '';
    } // if
    
    return '<p id="' . $id . '" class="flash"><span class="flash_inner">' . $message . '</span></p>';
  } // smarty_function_flash_box

?>