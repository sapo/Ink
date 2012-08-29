<?php

  /**
   * select_default_assignment_filter helper implementation
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */

  /**
   * Render select_default_assignment_filter control
   * 
   * Parameters:
   * 
   * - user - User - User using the page
   * - value - integer - ID of selected filter
   * - optional - boolean - Value is optional?
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_default_assignment_filter($params, &$smarty) {
    $user = array_var($params, 'user', null, true);
    $value = array_var($params, 'value', null, true);
    $optional = array_var($params, 'optional', true, true);
    
    $default_filter_id = (integer) ConfigOptions::getValue('default_assignments_filter');
    $default_filter = $default_filter_id ? AssignmentFilters::findById($default_filter_id) : null;
    
    $options = array();
    if(instance_of($default_filter, 'AssignmentFilter') && $optional) {
      $options[] = option_tag(lang('-- System Default (:filter) --', array('filter' => $default_filter->getName())), '');
    } // if
    
    $grouped_filters = AssignmentFilters::findGrouped($user, true);
    if(is_foreachable($grouped_filters)) {
      foreach($grouped_filters as $group_name => $filters) {
        $group_options = array();
        foreach($filters as $filter) {
          $group_options[] = option_tag($filter->getName(), $filter->getId(), array('selected' => ($value == $filter->getId())));
        } // foreach
        
        if(count($options) > 0) {
          $options[] = option_tag('', '');
        } // if
        
        $options[] = option_group_tag($group_name, $group_options);
      } // foreach
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_default_assignment_filter

?>