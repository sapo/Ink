<?php

  /**
   * Render select project user permissions widget
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select user permissions widget
   * 
   * Params:
   * 
   * - name
   * - id
   * - permissions
   * - role_id
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_user_project_permissions($params, &$smarty) {
    static $counter = 1;
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $id = 'select_user_project_permissions_' . $counter;
      $counter++;
    } // if
    
    $role_id = array_var($params, 'role_id', 0);
    $permissions = array_var($params, 'permissions', array());
    
    $smarty->assign(array(
      '_select_user_project_permissions_name'              => array_var($params, 'name'),
      '_select_user_project_permissions_role_id_field'     => array_var($params, 'role_id_field', 'role_id'),
      '_select_user_project_permissions_permissions_field' => array_var($params, 'permissions_field', 'permissions'),
      '_select_user_project_permissions_name'              => array_var($params, 'name'),
      '_select_user_project_permissions_id'                => $id,
      '_select_user_project_permissions_role_id'           => $role_id,
      '_select_user_project_permissions_permissions'       => $permissions,
      '_select_user_project_permissions_roles'             => Roles::findProjectRoles(),
    ));
    
  	return $smarty->fetch(get_template_path('_user_project_permissions', null, SYSTEM_MODULE));
  } // smarty_function_select_user_project_permissions

?>