<?php

  /**
   * recent_activities helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render block of the recent activities for selected user
   *
   * - recent_activities - array of groupped recent activities
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_recent_activities($params, &$smarty) {
    $recents = array_var($params, 'recent_activities');
    if(!is_foreachable($recents)) {
    	return '';
    } // if
    
    $smarty->assign(array(
      '_recents' => $recents
    ));
    
    return $smarty->fetch(get_template_path('_recent_activities_for_selected_user', null, SYSTEM_MODULE));
  } // smarty_function_recent_activities

?>