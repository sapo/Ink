<?php

  /**
   * Discussions module on_project_tabs event handler
   *
   * @package activeCollab.modules.discussions
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
  function discussions_handle_on_project_tabs(&$tabs, &$logged_user, &$project) {
    if($logged_user->getProjectPermission('discussion', $project) >= PROJECT_PERMISSION_ACCESS) {
      $tabs->add('discussions', array(
        'text' => lang('Discussions'),
        'url' => discussions_module_url($project)
      ));
    } // if
  } // discussions_handle_on_project_tabs

?>