<?php

  /**
   * mobile_access_progressbar
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */

  /**
   * Render progressbar block
   * 
   * Parameters:
   * 
   * - value - progressbar percent
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mobile_access_progressbar($params, &$smarty) {
    $smarty->assign(array(
      "_mobile_access_progressbar_value"  => array_var($params,'value',0),
    ));
    return $smarty->fetch(get_template_path('_progressbar', null, MOBILE_ACCESS_MODULE));
  } // smarty_function_mobile_access_progressbar

?>