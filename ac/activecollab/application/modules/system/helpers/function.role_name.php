<?php

  /**
   * Show role name
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Show role name
   * 
   * Parameters:
   * 
   * - role - Selected role
   * - user - If present, system will check if $user is people manager and only 
   *   then display role name
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_role_name($params, &$smarty) {
  	$role = array_var($params, 'role');
  	
  	if(instance_of($role, 'Role')) {
  	  $user = array_var($params, 'user');
    	if(instance_of($user, 'User')) {
    	  return $user->isPeopleManager() ? clean($role->getName()) : '';
    	} else {
    	  return clean($role->getName());
    	} // if
  	} else {
  	  return '<span style="color: red; font-weight: bold">' . lang('Error: Unknown Role') . '</span>';
  	} // if
  } // smarty_function_role_name

?>