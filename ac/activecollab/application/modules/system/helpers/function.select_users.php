<?php

  /**
   * select_users helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select users from all clients box
   * 
   * Supported paramteres:
   *
   * - value     - array of select user ID-s 
   * - users     - users can be preloaded, if missing users will be loaded by 
   *               project or all at once if project value is missing
   * - project   - Project list only users that are pare of the project
   * - company   - List only users of this company
   * - exclude   - array of user ID-s that need to be excluded
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_users($params, &$smarty){
    static $counter = 0;
    
    $name = array_var($params, 'name');
    if($name == '') {
      return new InvalidParamError('name', $name, '$name is expected to be a valid control name', true);
    } // if
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $counter++;
      $id = 'select_users_' . $counter;
    } // if
    
    $exclude_ids = array_var($params, 'exclude', array());
    $selected_user_ids = array_var($params, 'value', array(), true);
    
    if(is_foreachable($exclude_ids) && is_foreachable($selected_user_ids)) {
      foreach($selected_user_ids as $k => $v) {
        if(in_array($v, $exclude_ids)) {
          unset($selected_user_ids[$k]);
        } // if
      } // foreach
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
      '_select_users_id' => $id,
      '_select_users_name' => $name,
      '_select_users_users' => $selected_users,
      '_select_users_company_id' => $company_id,
      '_select_users_project_id' => $project_id,
      '_select_users_exclude_ids' => do_json_encode($exclude_ids),
    ));
    
    return $smarty->fetch(get_template_path('_select_users', null, SYSTEM_MODULE));
  } // smarty_function_select_users

?>