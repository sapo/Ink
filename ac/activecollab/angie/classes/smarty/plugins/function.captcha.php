<?php

  /**
  * Render captcha field
  * 
  * Parameters:
  * 
  * - name - field name
  * - value - initial value
  * - array of additional attributes
  * - captcha_url - URL of captcha script
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_captcha($params, &$smarty) {
    $output = "<span class='captcha'>";
    $output.= "  <img src='".array_var($params, 'captcha_url')."' class='captcha' alt='verification' />";
    $output.= smarty_function_text_field($params, $smarty);
    $output.= "</span>";
    
    return $output;
  } // smarty_function_captcha

?>