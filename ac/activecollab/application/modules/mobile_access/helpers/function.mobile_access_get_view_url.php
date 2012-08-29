<?php

  /**
   * mobile_access_get_view_url
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */

  /**
   * returns View Url for provided object
   * 
   * - object - object
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mobile_access_get_view_url($params, &$smarty) {
    return mobile_access_module_get_view_url(array_var($params, 'object', null));
  } // smarty_function_mobile_access_get_view_url

?>