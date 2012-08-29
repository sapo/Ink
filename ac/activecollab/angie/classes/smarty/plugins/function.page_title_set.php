<?php

  /**
  * Set page title
  * 
  * Parameters:
  * 
  * - to - new title
  *
  * @param array $params
  * @param Smarty $smarty
  * @return void
  */
  function smarty_function_page_title_set($params, &$smarty) {
    $construction =& PageConstruction::instance();
    $construction->setPageTitle(array_var($params, 'to'));
    return '';
  } // smarty_function_page_title_set

?>