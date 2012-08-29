<?php

  /**
   * mobile_access_object_properties helper
   *
   * @package activeCollab.modules.mobile_access
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
   * - show_tags - To display object tags
   * - show_body - To display object description
   * - show_category - To display object category
   * - show_file_details - To display file details, if object is file
   * - show_name - To display object name
   * - show_priority - To display object priority
   * - show_milestone_day_info - To display milestone due on info
   * - show_assignees - To show assignees
   * - only_show_body - To display only body
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mobile_access_object_properties($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
        
    $smarty->assign(array(
      '_mobile_access_object_properties_object'                 => $object,
      '_mobile_access_object_properties_show_completed_status'  => (boolean) array_var($params, 'show_completed_status', false),
      '_mobile_access_object_properties_show_milestone'         => (boolean) array_var($params, 'show_milestone', false),
      '_mobile_access_object_properties_show_tags'              => (boolean) array_var($params, 'show_tags', false),
      '_mobile_access_object_properties_show_body'              => (boolean) array_var($params, 'show_body', false),
      '_mobile_access_object_properties_show_category'          => (boolean) array_var($params, 'show_category', false),
      '_mobile_access_object_properties_show_file_details'      => (boolean) array_var($params, 'show_file_details', false),
      '_mobile_access_object_properties_show_name'              => (boolean) array_var($params, 'show_name', false),
      '_mobile_access_object_properties_show_assignees'         => (boolean) array_var($params, 'show_assignees', false),
      '_mobile_access_object_properties_show_priority'          => (boolean) array_var($params, 'show_priority', false),
      '_mobile_access_object_properties_show_milestone_day_info'=> (boolean) array_var($params, 'show_milestone_day_info', false),
      '_mobile_access_object_properties_show_total_time'        => (boolean) array_var($params, 'show_total_time', false),
      '_mobile_access_object_properties_only_show_body'         => (boolean) array_var($params, 'only_show_body', false),
    ));
    
    if (module_loaded(TIMETRACKING_MODULE)) {
      $smarty->assign(array(
        '_mobile_access_object_properties_total_time'           => float_format(TimeRecords::sumObjectTime($object), 2),
      ));
    }
    
    return $smarty->fetch(get_template_path('_object_properties', null, MOBILE_ACCESS_MODULE));
  } // smarty_function_mobile_access_object_properties

?>