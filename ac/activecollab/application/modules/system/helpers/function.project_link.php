<?php

  /**
   * project_link helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render Smarty link
   * 
   * Parameters:
   * 
   * - project - Project instance that need to be linked
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_link($params, &$smarty) {
    static $cache = array();
    
    $project = array_var($params, 'project');
    if(!instance_of($project, 'Project')) {
      if(array_var($params, 'optional', false)) {
        return '--';
      } else {
        return new InvalidParamError('project', $project, '$project is expected to be an instance of Project class', true);
      } // if
    } // if
    
    if(!isset($cache[$project->getId()])) {
      $cache[$project->getId()] = '<a href="' . clean($project->getOverviewUrl()) . '" class="project_link">' . clean($project->getName()) .'</a>';
    } // if
    
    return $cache[$project->getId()];
  } // smarty_function_project_link

?>