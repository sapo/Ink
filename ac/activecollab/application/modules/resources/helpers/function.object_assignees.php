<?php

  /**
   * object_assignees helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render object assignees list
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_assignees($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    $language = array_var($params, 'language', $smarty->get_template_vars('current_language')); // maybe we need to print this in a specific language?
    
    if(instance_of($language, 'Language')) {
      $cache_id = 'object_assignments_' . $object->getId() . '_rendered_' . $language->getId();
      $cached_value = cache_get($cache_id);
    } else {
      $cache_id = null;
      $cached_value = null;
    } // if
    
    if($cached_value) {
      return $cached_value;
    } else {
      $users_table = TABLE_PREFIX . 'users';
      $assignments_table = TABLE_PREFIX . 'assignments';
      
      $rows = db_execute_all("SELECT $assignments_table.is_owner AS is_assignment_owner, $users_table.id AS user_id, $users_table.company_id, $users_table.first_name, $users_table.last_name, $users_table.email FROM $users_table, $assignments_table WHERE $users_table.id = $assignments_table.user_id AND $assignments_table.object_id = ? ORDER BY $assignments_table.is_owner DESC", $object->getId());
      if(is_foreachable($rows)) {
        $owner = null;
        $other_assignees = array();
        
        foreach($rows as $row) {
          if(empty($row['first_name']) && empty($row['last_name'])) {
            $user_link = '<a href="' . assemble_url('people_company', array('company_id' => $row['company_id'])) . '#user' . $row['user_id'] . '">' . clean($row['email'])  . '</a>';
          } else {
            $user_link = '<a href="' . assemble_url('people_company', array('company_id' => $row['company_id'])) . '#user' . $row['user_id'] . '">' . clean($row['first_name'] . ' ' . $row['last_name'])  . '</a>';
          } // if
          
          if($row['is_assignment_owner']) {
            $owner = $user_link;
          } else {
            $other_assignees[] = $user_link;
          } // if
        } // foreach
        
        if($owner) {
          if(count($other_assignees) > 0) {
            $cached_value = $owner . ' ' . lang('is responsible', null, true, $language) . '. ' . lang('Other assignees', null, true, $language) . ': ' . implode(', ', $other_assignees) . '.';
          } else {
            $cached_value = $owner . ' ' . lang('is responsible', null, true, $language) . '.';
          } // if
        } // if
      } // if
      
      if(empty($cached_value)) {
        $cached_value = lang('Anyone can pick and complete this task', null, true, $language);
      } // if
      
      if(instance_of($language, 'Language') && $cache_id) {
        cache_set($cache_id, $cached_value); // cache if we don't have language parameter set
      } // if
      
      return $cached_value;
    } // if
  } // smarty_function_object_assignees

?>