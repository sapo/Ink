<?php

  /**
   * mobile_access_get_add_comment_url
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */

  /**
   * returns Add Comment url for provided parent
   * 
   * - parent - parent object of comment
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_mobile_access_get_add_comment_url($params, &$smarty) {
    return mobile_access_module_get_add_comment_url(array_var($params, 'parent', null));
  } // smarty_fuction_mobile_access_get_add_comment_url

?>