<?php

  /**
   * Milestones module on_project_tabs event handler
   *
   * @package activeCollab.modules.milestones
   * @subpackage handlers
   */
  
  /**
   * Handle on prepare project overview event
   *
   * @param NamedList $tabs
   * @param User $logged_user
   * @param Project $project
   * @return null
   */
  function milestones_handle_on_project_tabs(&$tabs, &$logged_user, &$project) {
    if($logged_user->getProjectPermission('milestone', $project) >= PROJECT_PERMISSION_ACCESS) {
      $tabs->add('milestones', array(
        'text' => lang('Milestones'),
        'url' => milestones_module_url($project),
      ));
    } // if
  } // milestones_handle_on_project_tabs

?>