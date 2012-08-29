<?php

  /**
   * activities helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render activities block
   * 
   * Parameters:
   * 
   * - activities - array of groupped activities
   * - project_column - show project column
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_activities($params, &$smarty) {
    $activities = array_var($params, 'activities');
    if(!is_foreachable($activities)) {
      return '';
    } // if
    
    $smarty->assign(array(
      '_activities' => $activities,
      '_activities_project_column' => (boolean) array_var($params, 'project_column', true),
    ));
    return $smarty->fetch(get_template_path('_activities', null, SYSTEM_MODULE));
  } // smarty_function_activities

?>