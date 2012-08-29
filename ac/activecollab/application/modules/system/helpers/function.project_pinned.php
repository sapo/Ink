<?php

  /**
   * project_pinned helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render pin/unpin icon
   * 
   * Parameters:
   * 
   * - project - Selected project
   * - user - Check pinned state agains this user
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_pinned($params, &$smarty) {
    $project = array_var($params, 'project');
    if(!instance_of($project, 'Project')) {
      return new InvalidParamError('project', $project, '$project is expected to be an instance of Project class', true);
    } // if
    
    $user = array_var($params, 'user');
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('user', $user, '$user is expected to be an instance of User class', true);
    } // if
    
    require_once SMARTY_PATH . '/plugins/block.link.php';
    $repeat = false;
    
    if(PinnedProjects::isPinned($project, $user)) {
      return smarty_block_link(array('href' => $project->getUnpinUrl(), 'title' => lang('Unpin'), 'class' => lang('unpin'), 'method' => 'post'), '<img src="' . get_image_url('icons/pinned.16x16.gif') . '" alt="" />', $smarty, $repeat);
    } else {
      return smarty_block_link(array('href' => $project->getPinUrl(), 'title' => lang('Pin to Top'), 'class' => lang('pin_to_top'), 'method' => 'post'), '<img src="' . get_image_url('icons/not-pinned.16x16.gif') . '" alt="" />', $smarty, $repeat);
    } // if
  } // smarty_function_project_pinned

?>