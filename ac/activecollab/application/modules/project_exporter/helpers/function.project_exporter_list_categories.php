<?php

  /**
   * project_exporter_list_categories helper
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */
  
  /**
   * Show a list of categories
   *
   * Parameters:
   * 
   * - categories - Array of categories that need to be listed
   * - current_category - Currently displayed category
   * - title - title for first element
   * - url_prefix - prefix for generated links
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_list_categories($params, &$smarty) {
    static $counter = 1;
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $id = 'objects_list_' . $counter;
      $counter++;
    } // if
    
    $smarty->assign(array(
      '_list_objects_categories'      => array_var($params, 'categories'),
      '_list_objects_current_category'=> array_var($params, 'current_category'),
    ));

    return $smarty->fetch(get_template_path('_project_exporter_list_categories', null, PROJECT_EXPORTER_MODULE));
  } // smarty_function_project_exporter_list_categories

?>