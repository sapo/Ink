<?php

  /**
   * list_objects helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Show a list of objects
   *
   * Parameters:
   * 
   * - objects - Array of objects that need to be listed
   * - id - Table ID, if not present ID will be generated
   * - show_header - Show table header
   * - show_star - Show star
   * - show_priority - Show star
   * - show_checkboxes - Show checkboxes column (this will also init checkboxes 
   *   behavior)
   * - show_project - Show project information
   * - del_completed - DEL completed object links
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_list_objects($params, &$smarty) {
    static $counter = 1;
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $id = 'objects_list_' . $counter;
      $counter++;
    } // if
    
    $smarty->assign(array(
      '_list_objects_objects'         => array_var($params, 'objects'),
      '_list_objects_id'              => $id,
      '_list_objects_show_header'     => (boolean) array_var($params, 'show_header', true),
      '_list_objects_show_star'       => (boolean) array_var($params, 'show_star', true),
      '_list_objects_show_priority'   => (boolean) array_var($params, 'show_priority', true),
      '_list_objects_show_checkboxes' => (boolean) array_var($params, 'show_checkboxes', true),
      '_list_objects_show_project'    => (boolean) array_var($params, 'show_project', true),
      '_list_objects_del_completed'   => (boolean) array_var($params, 'del_completed', true),
    ));
    return $smarty->fetch(get_template_path('_list_objects', null, SYSTEM_MODULE));
  } // smarty_function_list_objects

?>