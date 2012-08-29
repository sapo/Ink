<?php

  /**
   * mobile_access_display_categories
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */

  /**
   * Render categories
   * 
   * Parameters:
   * 
   * - categories - list of categories
   * - active_category - active category
   * - action - form action
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_mobile_access_display_filter_list($params, &$smarty) {
    $active_category = array_var($params, 'active_object', new ProjectObject());
    $variable_name = array_var($params, 'variable_name', 'category_id');
    
    $smarty->assign(array(
      "_mobile_access_display_categories_objects"  => array_var($params, 'objects', null),
      "_mobile_access_display_categories_active_object"  => $active_category,
      "_mobile_access_display_categories_variable_name" => $variable_name,
      "_mobile_access_display_categories_action"  => array_var($params, 'action', '#'),
    ));
    
    if (array_var($params, 'enable_categories', false)) {
      return $smarty->fetch(get_template_path('_display_filter_list', null, MOBILE_ACCESS_MODULE));
    } else {
      return null;
    } // if
  } // smarty_function_mobile_access_display_filter_list

?>