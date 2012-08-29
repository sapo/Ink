<?php

  /**
   * select_parent_object helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select parent object for provided project
   * 
   * Supported paramteres:
   * 
   * - types - type of of parent objects to be listed
   * - project - Instance of selected project (required)
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project_object($params, &$smarty) {
    $project = array_var($params, 'project');
    if (!instance_of($project, 'Project')) {
      return new InvalidParamError('project', $project, '$project is expected to be an instance of Project class', true);
    } // if
    
    $value = array_var($params, 'value');
    
    unset($params['project']);
    
    $types = array_var($params, 'types', null);
    if (!$types || !is_foreachable($types = explode(',' , $types))) {
      $types = array(
        'ticket',
        'file',
        'discussion',
        'page',
      );
    } // if
    
    $id_name_map = ProjectObjects::getIdNameMapForProject($project, $types);
    
    if (!is_foreachable($id_name_map)) {
      return false;
    } // if
    
    $sorted = array();
    foreach ($id_name_map as $object) {
      $option_attributes = ($value == $object['id']) ? array('selected' => true) : null;
    	$sorted[strtolower($object['type'])][] = option_tag($object['name'], $object['id'], $option_attributes);
    } // foreach
    
    if (is_foreachable($sorted)) {
      foreach ($sorted as $sorted_key => $sorted_values) {
      	$options[] = option_group_tag($sorted_key, $sorted_values);
      } // foreach
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_project_object

?>