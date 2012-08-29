<?php

  /**
   * select_milestone helper
   *
   * @package activeCollab.modules.milestones
   * @subpackage helpers
   */
  
  /**
   * Render select milestone control
   * 
   * Params:
   * 
   * - project - Project instance that need to be used
   * - active_only - Return only active milestones, true by default
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_milestone($params, &$smarty) {
    $project = array_var($params, 'project');
    if(!instance_of($project, 'Project')) {
      return new InvalidParamError('project', $project, '$project value is expected to be an instance of Project class', true);
    } // if
    
    unset($params['project']);
    
    $active_only = false;
    if(isset($params['active_only'])) {
      $active_only = (boolean) $params['active_only'];
      unset($params['active_only']);
    } // if
    
    $value = null;
    if(isset($params['value'])) {
      $value = $params['value'];
      unset($params['value']);
    } // if
    
    $optional = true;
    if(isset($params['optional'])) {
      $optional = (boolean) $params['optional'];
      unset($params['optional']);
    } // if
    
    $options = array();
    if($optional) {
      $options[] = option_tag(lang('-- None --'), '');
      $options[] = option_tag('', '');
    } // if
    
    $logged_user = $smarty->get_template_vars('logged_user');
    $milestones = $active_only ? Milestones::findActiveByProject($project, STATE_VISIBLE, $logged_user->getVisibility()) : Milestones::findByProject($project, $logged_user);
      
    if(is_foreachable($milestones)) {
      $completed_options = array();
      
      foreach($milestones as $milestone) {
        if($milestone->isCompleted()) {
          $option_attributes = $milestone->getId() == $value ? array('selected' => true) : null;
          $completed_options[] = option_tag($milestone->getName(), $milestone->getId(), $option_attributes);
        } else {
          $option_attributes = $milestone->getId() == $value ? array('selected' => true) : null;
          $options[] = option_tag($milestone->getName(), $milestone->getId(), $option_attributes);
        } // if
      } // foreach
      
      if(is_foreachable($completed_options)) {
        $options[] = option_tag('', '');
        $options[] = option_group_tag(lang('Completed'), $completed_options);
      } // if
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_milestone

?>