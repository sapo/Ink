<?php

  /**
   * Print project icons
   * 
   * Params:
   * 
   * - project - Project instance or project ID
   * - large - Boolean value, use large or small icon
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_icon($params, &$smarty) {
    $project = array_var($params, 'project');
    if($project === null) {
      return '';
    } elseif(instance_of($project, 'Project')) {
      $project_id = $project->getId();
    } else {
      $project_id = $project;
    } // if
    
    list($large_icon_url, $small_icon_url) = get_project_icon_urls($project_id);
    return array_var($params, 'large') ? $large_icon_url : $small_icon_url;
  } // smarty_function_project_icon

?>