<?php

  /**
   * Select role helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select role helper
   * 
   * Params:
   * 
   * - value - ID of selected role
   * - optional - Wether value is optional or not
   * - active_user - Set if we are changing role of existing user so we can 
   *   handle situations when administrator role is displayed or changed
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_role($params, &$smarty) {
    $value = array_var($params, 'value', null, true);
    $optional = array_var($params, 'optional', false, true);
    $active_user = array_var($params, 'active_user', false, true);
    
    $logged_user = get_logged_user();
    if(!instance_of($logged_user, 'User')) {
      return new InvalidParamError('logged_user', $logged_user, '$logged_user is expected to be an instance of user class');
    } // if
    
    if($optional) {
      $options = array(
        option_tag(lang('-- None --'), ''), 
        option_tag('', '')
      );
    } else {
      $options = array();
    } // if
    
    $roles = Roles::findSystemRoles();
    if(is_foreachable($roles)) {
      foreach($roles as $role) {
        $show_role = true;
        $disabled = false;
        
        if($role->getPermissionValue('admin_access') && !$logged_user->isAdministrator() && !$active_user->isAdministrator()) {
          $show_role = false; // don't show administration role to non-admins and for non-admins
        } // if
        
        if ($show_role) {
          $option_attributes = $value == $role->getId() ? array('selected' => true, 'disabled' => $disabled) : null;
          $options[] = option_tag($role->getName(), $role->getId(), $option_attributes);
        } // if
      } // foreach
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_role

?>