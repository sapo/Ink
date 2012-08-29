<?php

  /**
   * mobile_access_list_objects
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */

  /**
   * Render list of objects
   * 
   * Parameters:
   * 
   * - objects - list of objects
   * - show_object_type - to display object type
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mobile_access_list_objects($params, &$smarty) {
    $objects = array_var($params, 'objects', null);

    $smarty->assign(array(
      "_mobile_access_list_objects_objects" => $objects,
      "_mobile_access_show_object_type" => array_var($params, 'show_object_type', false)
    ));
    
    return $smarty->fetch(get_template_path('_list_objects', null, MOBILE_ACCESS_MODULE));
  } // smarty_function_mobile_access_list_objects

?>