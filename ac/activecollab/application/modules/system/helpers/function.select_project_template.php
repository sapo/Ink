<?php

  /**
   * Render select project template widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project_template($params, &$smarty) {
    $options = array(
      option_tag(lang('-- Create a Blank Project --'), ''),
      option_tag('', '')
    );
    
    $value = array_var($params, 'value', null, true);
    
    $projects_loaded = false;
    
    $group_id = ConfigOptions::getValue('project_templates_group');
    if($group_id) {
      $group = ProjectGroups::findById($group_id);
      if(instance_of($group, 'ProjectGroup')) {
        $projects = Projects::findByGroup($group);
        $projects_loaded = true;
        
        if(is_foreachable($projects)) {
          foreach($projects as $project) {
            $option_attributes = $project->getId() == $value ? array('selected' => true) : null;
            $options[] = option_tag($project->getName(), $project->getId(), $option_attributes);
          } // if
        } // if
      } // if
    } // if
    
    if(!$projects_loaded) {
      $projects = Projects::findNamesByUser($smarty->get_template_vars('logged_user'));
      if(is_foreachable($projects)) {
        foreach($projects as $k => $v) {
          $option_attributes = $k == $value ? array('selected' => true) : null;
          $options[] = option_tag($v, $k, $option_attributes);
        } // foreach
      } // if
    } // if
    
  	return select_box($options, $params);
  } // smarty_function_select_project_template

?>