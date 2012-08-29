<?php

  /**
  * Display page title
  *
  * Parameters:
  * 
  * - default - default page title, if no page title is present. If there is no 
  *   default value 'Index' is used
  * 
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_page_title($params, &$smarty) {
    $default = array_var($params, 'default', 'Index');
    $page_title = PageConstruction::getPageTitle();
    
    return empty($page_title) ? clean($default) : clean($page_title);
  } // smarty_function_page_title

?>