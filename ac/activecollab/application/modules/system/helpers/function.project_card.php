<?php

  /**
   * project_card helpers
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Show project card
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_card($params, &$smarty) {
    $project = array_var($params, 'project');
    if(!instance_of($project, 'Project')) {
      return new InvalidParamError('project', $project, '$project is expected to be an instance of Project class', true);
    } // if
    
    $smarty->assign(array(
      '_card_project' => $project,
      '_card_project_company' => $project->getCompany(),
    ));
    return $smarty->fetch(get_template_path('_card', 'project', SYSTEM_MODULE));
  } // smarty_function_project_card

?>