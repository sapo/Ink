<?php

  /**
   * project_progress helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render project progress bar
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_progress($params, &$smarty) {
    $project = array_var($params, 'project');
    if(!instance_of($project, 'Project')) {
      return new InvalidParamError('project', $project, '$project is expected to be an instance of Project class', true);
    } // if
    
    $smarty->assign(array(
      '_project_progress' => $project,
      '_project_progress_info' => (boolean) array_var($params, 'info', true),
    ));
    return $smarty->fetch(get_template_path('_projects_progress', 'project', SYSTEM_MODULE));
  } // smarty_function_project_progress

?>