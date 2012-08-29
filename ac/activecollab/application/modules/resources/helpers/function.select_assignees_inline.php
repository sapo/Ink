<?php

  /**
   * select_assignees_inline helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render inline select assignees
   * 
   * Parameters:
   * 
   * - object     - Parent object
   * - project    - Show only users that have access to this project
   * - company    - SHow only users that are members of this company
   * - exclude    - ID-s of users that need to be excluded
   * - value      - Array of selected users as first element and ID of task 
   *                owner as second
   * - name       - Base name
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_assignees_inline($params, &$smarty) {
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
    
    $company = array_var($params, 'company', null);
    $project = array_var($params, 'project', null);
    $choose_responsible = array_var($params, 'choose_responsible', false);
    $responsible_name = '';
    if ($choose_responsible) {
      $responsible_name = $name.'[1]';
      $name.='[0]';
    } // if
    
    $widget_users = Users::findForSelect($company, $project);
    
    $value = array_var($params, 'value', array(), true);
    $selected_assignees = array_var($value, '0', array());
    $responsible_person = array_var($value, '1', null);
    
    if (!is_foreachable($widget_users)) {
      return false;
    } // if
    
    $smarty->assign(array(
      '_select_assignees_id'                  => $id,
      '_select_assignees_name'                => $name,
      '_select_assignees_responsible_name'    => $responsible_name,
      '_select_assignees_users'               => $widget_users,
      '_select_assignees_responsible'         => $responsible_person,
      '_select_assignees_assigned'            => $selected_assignees,
      '_select_assignees_choose_responsible'  => $choose_responsible,
      '_select_assignees_users_per_row'       => array_var($params, 'users_per_row', 4)
    ));
    
    return $smarty->fetch(get_template_path('_select_assignees_inline', null, RESOURCES_MODULE));
  } // smarty_function_select_assignees

?>