<?php

  /**
   * select_project helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select project helper
   * 
   * Parametars:
   * 
   *  - value - Id of selected project
   *  - user - Limit only to projects that can be viewed by User
   *  - optional
   * 
   * @param void
   * @return null
   */
  function smarty_function_select_project($params, &$smarty) {
    $user = array_var($params, 'user', null, true);
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('user', $user, '$user is expected to be an instance of User class', true);
    } // if
    
    $show_all = array_var($params, 'show_all', false) && $user->isProjectManager();
    $value = array_var($params, 'value', null, true);
    
    $projects_table = TABLE_PREFIX . 'projects';
    $project_users_table = TABLE_PREFIX . 'project_users';
    
    if($show_all) {
      $projects = db_execute_all("SELECT $projects_table.id, $projects_table.name, $projects_table.status FROM $projects_table WHERE $projects_table.type = ? ORDER BY $projects_table.name",  PROJECT_TYPE_NORMAL);
    } else {
      $projects = db_execute_all("SELECT $projects_table.id, $projects_table.name, $projects_table.status FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.type = ? ORDER BY $projects_table.name", $user->getId(), PROJECT_TYPE_NORMAL);
    } // if
    
    $exclude = (array) array_var($params, 'exclude', array(), true);
    
    $active_options = array();
    $archived_options = array();
    
    if(is_foreachable($projects)) {
      foreach($projects as $k => $project) {
        if(in_array($project['id'], $exclude)) {
          continue;
        } // if
        
        $option_attributes = $project['id'] == $value ? array('selected' => true) : null;
        
        if($project['status'] == PROJECT_STATUS_ACTIVE) {
          $active_options[] = option_tag($project['name'], $project['id'], $option_attributes);
        } else {
          $archived_options[] = option_tag($project['name'], $project['id'], $option_attributes);
        } // if
      } // if
    } // if
    
    $optional = array_var($params, 'optional', false, true);
    
    $options = array();
    if($optional) {
      $options[] = option_tag(lang(array_var($params, 'optional_caption', '-- Select Project --')), '');
      $options[] = option_tag('', '');
    } // if
    
    if(is_foreachable($active_options)) {
      $options[] = option_group_tag(lang('Active'), $active_options);
    } // if
    
    if(is_foreachable($active_options) && is_foreachable($archived_options)) {
      $options[] = option_tag('', '');
    } // if
    
    if(is_foreachable($archived_options)) {
      $options[] = option_group_tag(lang('Archive'), $archived_options);
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_project

?>