<?php

  /**
   * Select projects helper definition
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select projects widget
   * 
   * Parameters:
   * 
   * - user - Instance of user accesing the page, required
   * - exclude - Single project or array of projects that need to be excluded
   * - value - Array of selected projects
   * - active_only - List only active projects
   * - show_all - If true and user is project manager / administrator, all 
   *   projects will be listed
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_projects($params, &$smarty) {
    static $ids = array();
    
    $name = array_var($params, 'name');
    if($name == '') {
      return new InvalidParamError('name', $name, '$name is expected to be a valid control name', true);
    } // if
    
  	$user = array_var($params, 'user', null, true);
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('user', $user, '$user is expected to be an instance of User class', true);
    } // if
    
    $id = array_var($params, 'id', null, true);
    if(empty($id)) {
      $counter = 1;
      do {
        $id = "select_projects_$counter";
      } while(in_array($id, $ids));
    } // if
    $ids[] = $id;
    
    $show_all = array_var($params, 'show_all', false) && $user->isProjectManager();
    
    $exclude = array_var($params, 'exclude', array(), true);
    if(!is_array($exclude)) {
      $exclude = array($exclude);
    } // if
    
    $value = array_var($params, 'value', null, true);
    if(is_foreachable($value) && count($exclude)) {
      foreach($value as $k => $v) {
        if(in_array($v, $exclude)) {
          unset($value[$k]);
        } // if
      } // foreach
    } // if
    
    $selected_projects = is_foreachable($value) ? Projects::findByIds($value) : null;
    
    require_once ANGIE_PATH . '/classes/json/init.php';
    $smarty->assign(array(
      '_select_projects_id' => $id,
      '_select_projects_name' => array_var($params, 'name'),
      '_select_projects_user' => $user,
      '_select_projects_projects' => $selected_projects,
      '_select_projects_exclude_ids' => do_json_encode($exclude),
      '_select_projects_active_only' => array_var($params, 'active_only', true),
      '_select_projects_show_all' => $show_all,
    ));
    
    return $smarty->fetch(get_template_path('_select_projects', null, SYSTEM_MODULE));
    
    // ---------------------------------------------------
    //  Old!
    // ---------------------------------------------------
    
    $projects_table = TABLE_PREFIX . 'projects';
    $project_users_table = TABLE_PREFIX . 'project_users';
    
    if($show_all) {
      $projects = db_execute_all("SELECT $projects_table.id, $projects_table.name, $projects_table.status FROM $projects_table WHERE $projects_table.type = ? ORDER BY $projects_table.name",  PROJECT_TYPE_NORMAL);
    } else {
      $projects = db_execute_all("SELECT $projects_table.id, $projects_table.name, $projects_table.status FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.type = ? ORDER BY $projects_table.name", $user->getId(), PROJECT_TYPE_NORMAL);
    } // if
    
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
  } // smarty_function_select_projects

?>