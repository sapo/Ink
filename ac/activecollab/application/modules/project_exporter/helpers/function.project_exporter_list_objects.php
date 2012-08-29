<?php

  /**
   * project_exporter_list_objects helper
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */
  
  /**
   * Show a list of objects
   *
   * Parameters:
   * 
   * - objects - Array of objects that need to be listed
   * - id - Table ID, if not present ID will be generated
   * - show_priority - Show priority
   * - url_prefix - prefix for generated links
   * - show_created_on - show created on field
   * - show_start_on - show start on field
   * - show_due_on - show due on field
   * - skip_table_tag - to skip <table> tag when outputing template
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_list_objects($params, &$smarty) {
    static $counter = 1;
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $id = 'objects_list_' . $counter;
      $counter++;
    } // if
        
    $smarty->assign(array(
      '_list_objects_objects'         => array_var($params, 'objects'),
      '_list_objects_id'              => $id,
      '_list_objects_show_priority'   => (boolean) array_var($params, 'show_priority', false),
      '_list_objects_url_prefix'      => array_var($params, 'url_prefix'),
      '_list_objects_show_created_on' => array_var($params, 'show_created_on', true),
      '_list_objects_show_start_on'   => array_var($params, 'show_start_on', false),
      '_list_objects_show_due_on'     => array_var($params, 'show_due_on', false),
      '_list_objects_skip_table_tag'  => array_var($params, 'skip_table_tag', false),
    ));
    return $smarty->fetch(get_template_path('_project_exporter_list_objects', null, PROJECT_EXPORTER_MODULE));
  } // smarty_function_project_exporter_list_objects

?>