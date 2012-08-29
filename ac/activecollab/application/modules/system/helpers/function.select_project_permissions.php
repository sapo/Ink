<?php

  /**
   * Render select project permissions widget
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render widgert
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project_permissions($params, &$smarty) {
    static $counter = 1;
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $id = 'select_project_permissions_' . $counter;
      $counter++;
    } // if
    
    $name = array_var($params, 'name');
  	$value = array_var($params, 'value', array());
  	$permissions = Permissions::findProject();
  	
  	if(is_foreachable($permissions)) {
  	  $levels = array(
  	    PROJECT_PERMISSION_NONE => lang('No Access'),
   	    PROJECT_PERMISSION_ACCESS => lang('Has Access'),
   	    PROJECT_PERMISSION_CREATE => lang('and Can Create'),
   	    PROJECT_PERMISSION_MANAGE => lang('and Can Manage'),
  	  );
  	  
  	  $result = '<table class="select_project_permissions" id="' . clean($id) . '">
  	    <tr>
  	      <th>' . lang('Object') . '</th>
  	      <th colspan="4">' . lang('Permissions Level') . '</th>
  	    </tr>';
  	  $counter = 1;
  	  foreach($permissions as $permission => $permission_name) {
  	    $permission_value = array_var($value, $permission);
  	    if($permission_value === null) {
  	      $permission_value = PROJECT_PERMISSION_NONE;
  	    } // if
  	    
  	    $result .= '<tr class="' . ($counter % 2 ? 'odd' : 'even') . ' hoverable"><td class="permission_name"><span>' . $permission_name . '</span></td>';
  	    
  	    foreach($levels as $level_value => $level_label) {
  	      $input_id = 'select_permission_' . $permission . '_' . $level_value;
  	      $input_attributes = array(
  	        'name' => $name . '[' . $permission . ']',
  	        'value' => $level_value,
  	        'type' => 'radio',
  	        'class' => 'inline',
  	        'id' => $input_id,
  	      );
  	      
  	      if($level_value == $permission_value) {
  	        $input_attributes['checked'] = 'checked';
  	      } // if
  	      
  	      $label_attributes = array(
  	        'for' => $input_id,
  	        'class' => 'inline',
  	      );
  	      
  	      $cell_class = $level_value == PROJECT_PERMISSION_NONE ? 'no_access' : 'has_access';
  	      
  	      $result .= '<td class="permission_value ' . $cell_class . '">' . open_html_tag('input', $input_attributes, true) . ' ' . open_html_tag('label', $label_attributes) . clean($level_label) . '</label></td>';
  	    } // if
  	    
  	    $result .= '</tr>';
  	    
  	    $counter++;
  	  } // foreach
  	  return $result . '</table><script type="text/javascript">App.widgets.SelectProjectPermissions.init("' . clean($id) . '")</script>';
  	} // if
  } // smarty_function_select_project_permissions

?>