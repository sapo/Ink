<?php

  /**
   * project_exporter_object_properties helper
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */
  
  /**
   * Show a object properties
   *
   * Parameters:
   * 
   * - object - Object of which properties are shown
   * - show_completed_status - To display object completed status
   * - show_milestone - To display object milestone
   * - milestone_url_prefix - URL prefix for milestone link
   * - show_tags - To display object tags
   * - show_body - To display object description
   * - show_category - To display object category
   * - category_url_prefix - URL prefix for category link
   * - show_file_details - To display file details, if object is file
   * - show_name - To display object name
   * - show_priority - To display object priority
   * - show_milestone_day_info - To display milestone due on info
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_object_properties($params, &$smarty) {
    $smarty->assign(array(
      '_object_properties_object'                 => array_var($params, 'object'),
      '_object_properties_show_completed_status'  => (boolean) array_var($params, 'show_completed_status', false),
      '_object_properties_show_milestone'         => (boolean) array_var($params, 'show_milestone', false),
      '_object_properties_milestone_url_prefix'   => array_var($params, 'milestone_url_prefix'),
      '_object_properties_show_tags'              => (boolean) array_var($params, 'show_tags', false),
      '_object_properties_show_body'              => (boolean) array_var($params, 'show_body', false),
      '_object_properties_show_category'          => (boolean) array_var($params, 'show_category', false),
      '_object_properties_category_url_prefix'    => array_var($params, 'category_url_prefix'),
      '_object_properties_show_file_details'      => (boolean) array_var($params, 'show_file_details', false),
      '_object_properties_show_name'              => (boolean) array_var($params, 'show_name', false),
      '_object_properties_show_priority'          => (boolean) array_var($params, 'show_priority', false),
      '_object_properties_show_milestone_day_info'=> (boolean) array_var($params, 'show_milestone_day_info', false),
      '_object_properties_show_milestone_link'    => (boolean) array_var($params, 'show_milestone_link', false),
      '_object_properties_show_created_by'        => (boolean) array_var($params, 'show_created_by', true),
      '_object_properties_show_created_on'        => (boolean) array_var($params, 'show_created_on', true),
      '_object_properties_attachments_url_prefix'      => array_var($params, 'attachments_url_prefix'),
    ));
    return $smarty->fetch(get_template_path('_project_exporter_object_properties', null, PROJECT_EXPORTER_MODULE));
  } // smarty_function_project_exporter_object_properties

?>