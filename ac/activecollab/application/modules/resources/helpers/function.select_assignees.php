<?php

  /**
   * select_assignees helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render select assignees box
   * 
   * Parameters:
   * 
   * - object     - Parent object
   * - project    - Show only users that have access to this project
   * - company    - SHow only users that are members of tis company
   * - exclude    - ID-s of users that need to be excluded
   * - value      - Array of selected users as first element and ID of task 
   *                owner as second
   * - name       - Base name
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_assignees($params, &$smarty) {
    static $counter = 0;
    
    $name = array_var($params, 'name');
    if($name == '') {
      return new InvalidParamError('name', $name, '$name is expected to be a valid control name', true);
    } // if
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $counter++;
      $id = 'select_assignees_' . $counter;
    } // if
    
    $exclude_ids = array_var($params, 'exclude', array());
    
    if(is_foreachable($exclude_ids) && is_foreachable($selected_user_ids)) {
      foreach($selected_user_ids as $k => $v) {
        if(in_array($v, $exclude_ids)) {
          unset($selected_user_ids[$k]);
        } // if
      } // foreach
    } // if
    
    $value = array_var($params, 'value', array(), true);
    if(count($value) == 2) {
      list($selected_user_ids, $owner_id) = $value;
    } else {
      $selected_user_ids = null;
      $owner_id = null;
    } // if
    
    if(is_foreachable($selected_user_ids)) {
      $selected_users = Users::findByIds($selected_user_ids);
    } else {
      $selected_users = null;
    } // if
    
    $company = array_var($params, 'company');
    $project = array_var($params, 'project');
    
    $company_id = 0;
    if(instance_of($company, 'Company')) {
      $company_id = $company->getId();
    } // if
    
    $project_id = 0;
    if(instance_of($project, 'Project')) {
      $project_id = $project->getId();
    } // if
    
    require_once ANGIE_PATH . '/classes/json/init.php';
    
    $smarty->assign(array(
      '_select_assignees_id'          => $id,
      '_select_assignees_name'        => $name,
      '_select_assignees_users'       => $selected_users,
      '_select_assignees_owner_id'    => $owner_id,
      '_select_assignees_company_id'  => do_json_encode($company_id),
      '_select_assignees_project_id'  => do_json_encode($project_id),
      '_select_assignees_exclude_ids' => do_json_encode($exclude_ids),
    ));
    
    return $smarty->fetch(get_template_path('_select_assignees', null, RESOURCES_MODULE));
  } // smarty_function_select_assignees

?>