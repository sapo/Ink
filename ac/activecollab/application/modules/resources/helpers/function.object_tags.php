<?php

  /**
   * object_tags helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render object tags
   *
   * Parameters:
   * 
   * - object - Selected object
   * - project - Selected project, if not present we'll get it from 
   *   $object->getProject()
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_tags($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    $project = array_var($params, 'project');
    if(!instance_of($project, 'Project')) {
      $project = $object->getProject();
    } // if
    
    if(!instance_of($project, 'Project')) {
      return new InvalidParamError('project', $project, '$project is expected to be an instance of Project class', true);
    } // if
    
    $tags = $object->getTags();
    if(is_foreachable($tags)) {
      $prepared = array();
      foreach($tags as $tag) {
        if(trim($tag)) {
          $prepared[] = '<a href="' . Tags::getTagUrl($tag, $project) . '">' . clean($tag) . '</a>';
        } // if
      } // if
      return implode(', ', $prepared);
    } else {
      return '<span class="no_tags">' . lang('-- No tags --') . '</span>';
    } // if
  } // smarty_function_object_tags

?>