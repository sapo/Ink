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
   * - comments - Array of objects that need to be listed
   * - id - div ID, if not present ID will be generated
   * - attachments_url_prefix - prefix for generated links for attachments
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_comments($params, &$smarty) {
    static $counter = 1;
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $id = 'objects_list_' . $counter;
      $counter++;
    } // if
    
    $smarty->assign(array(
      '_list_objects_comments'         => array_var($params, 'comments'),
      '_list_objects_id'              => $id,
      '_list_objects_attachments_url_prefix'      => array_var($params, 'attachments_url_prefix'),
    ));
    return $smarty->fetch(get_template_path('_project_exporter_comments', null, PROJECT_EXPORTER_MODULE));
  } // smarty_function_project_exporter_list_objects

?>