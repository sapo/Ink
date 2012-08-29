<?php

  /**
   * select_project_group helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Select project group helper
   *
   * Params:
   * 
   * - value - ID of selected group
   * - optional - boolean
   * - can_create_new - Should this select box offer option to create a new 
   *   company from within the list
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project_group($params, &$smarty) {
    static $ids = array();
    
    $optional = array_var($params, 'optional', true, true);
    $value = array_var($params, 'value', null, true);
    $can_create_new = array_var($params, 'can_create_new', true, true);
    
    $id = array_var($params, 'id', null, true);
    if(empty($id)) {
      $counter = 1;
      do {
        $id = "select_project_group_dropdown_$counter";
        $counter++;
      } while(in_array($id, $ids));
    } // if
    $ids[] = $id;
    $params['id'] = $id;
    
    $groups = ProjectGroups::findAll($smarty->get_template_vars('logged_user'), true);
    
    if($optional) {
      $options = array(option_tag(lang('-- None --'), ''), option_tag('', ''));
    } else {
      $options = array();
    } // if
    
    if(is_foreachable($groups)) {
      foreach($groups as $group) {
        $option_attributes = array('class' => 'object_option');
        if($value == $group->getId()) {
          $option_attributes['selected'] = true;
        } // if
        
        $options[] = option_tag($group->getName(), $group->getId(), $option_attributes);
      } // foreach
    } // if
    
    if($can_create_new) {
      $params['add_object_url'] = assemble_url('project_groups_quick_add');
      $params['object_name'] = 'project_group';
      $params['add_object_message'] = lang('Please insert new project group name');
      
      $logged_user = get_logged_user();
      if(instance_of($logged_user, 'User') && ProjectGroup::canAdd($logged_user)) {
        $options[] = option_tag('', '');
        $options[] = option_tag(lang('New Project Group...'), '', array('class' => 'new_object_option'));
      } // if
    } // if
    
    return select_box($options, $params) . '<script type="text/javascript">$("#' . $id . '").new_object_from_select();</script>';
  } // smarty_function_select_project_group

?>