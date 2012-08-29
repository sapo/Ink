<?php

  /**
   * Render select system permissions widget
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_system_permissions($params, &$smarty) {
  	static $counter = 1;
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $id = 'select_system_permissions_' . $counter;
      $counter++;
    } // if
    
    $name = array_var($params, 'name');
  	$permissions = array_var($params, 'permissions');
  	$value = array_var($params, 'value', array());
  	
  	if(is_foreachable($permissions)) {
  	  require_once SMARTY_PATH . '/plugins/function.yes_no.php';
  	  
  	  $result = '<table class="select_system_permissions" id="' . clean($id) . '">
  	    <tr>
  	      <th>' . lang('Permission') . '</th>
  	      <th colspan="4">' . lang('Value') . '</th>
  	    </tr>';
  	  $counter = 1;
  	  foreach($permissions as $permission_name) {
  	    $permission_value = array_var($value, $permission_name);
  	    if($permission_value === null) {
  	      $permission_value = false;
  	    } // if
  	    
  	    $result .= '<tr class="' . ($counter % 2 ? 'odd' : 'even') . '"><td class="permission_name"><span>' . clean($permission_name) . '</span></td>';
  	    $result .= '<td>' . smarty_function_yes_no(array(
  	      'name' => 'role[permissions][' . $permission_name . ']',
  	      'value' => $permission_value,
  	      'disabled' => array_var($params, 'protect_admin_role') && ($permission_name == 'admin_access' || $permission_name == 'system_access')
  	    )) . '</td>';
  	    
  	    //$result .= '</tr><script type="text/javascript">App.widgets.SelectSystemPermissions.init("' . clean($id) . '")</script>';
  	    $result .= '</tr>';
  	    
  	    $counter++;
  	  } // foreach
  	  return $result . '</table>';
  	} // if
  	
  	return '';
  } // smarty_function_select_system_permissions

?>