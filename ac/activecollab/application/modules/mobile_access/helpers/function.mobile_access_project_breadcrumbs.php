<?php

  /**
   * mobile_access_project_breadcrumbs
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */

  /**
   * Render project breadcrumbs block
   * 
   * Parameters:
   * 
   * - breadcrumbs - array of breadcrumbs
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mobile_access_project_breadcrumbs($params, &$smarty) {
    if (!is_foreachable($breadcrumbs = array_var($params, 'breadcrumbs', null))) {
      return null;
    }
    
    $smarty->assign(array(
      "mobile_access_project_breadcrumbs_breadcrumbs"  => $breadcrumbs,
    ));
    
    return $smarty->fetch(get_template_path('_project_breadcrumbs', null, MOBILE_ACCESS_MODULE));
  } // smarty_function_mobile_access_project_breadcrumbs

?>