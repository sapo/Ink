<?php

  /**
   * Render role_permission_value widget
   * 
   * Params:
   * 
   * - permission - String - Permission name
   * - role - Role - System role
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_role_permission_value($params, &$smarty) {
    $permission = array_var($params, 'permission');
    if(empty($permission)) {
      return new InvalidParamError('permission', $permission, '$permission value is exepected to be valid system permission name', true);
    } // if
    
    $role = array_var($params, 'role');
    if(!instance_of($role, 'Role')) {
      return new InvalidParamError('role', $role, '$role is exepected to be valid Role instance', true);
    } // if
    
    $yes_for_admins = array_var($params, 'yes_for_admins', true, true);
    
    $id = 'role_permission_value_' . $permission . '_' . $role->getId();
    
    $result = open_html_tag('input', array(
      'type' => 'checkbox',
      'class' => 'auto',
      'checked' => $yes_for_admins ? $role->getPermissionValue('admin_access') || $role->getPermissionValue($permission) : $role->getPermissionValue($permission),
      'id' => $id,
      'set_permission_value_url' => $role->getSetPermissionValueUrl($permission),
      'disabled' => $yes_for_admins && $role->getPermissionValue('admin_access'),
    ));
    
    return $result . '<script type="text/javascript">App.system.RolePermissionValue.init("' . $id . '")</script>';
  } // smarty_function_role_permission_value

?>