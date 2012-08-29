<?php

  /**
   * Pages module on_project_tabs event handler
   *
   * @package activeCollab.modules.pages
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
  function pages_handle_on_project_tabs(&$tabs, &$logged_user, &$project) {
    if($logged_user->getProjectPermission('page', $project) >= PROJECT_PERMISSION_ACCESS) {
      $tabs->add('pages', array(
        'text' => lang('Pages'),
        'url' => pages_module_url($project)
      ));
    } // if
  } // pages_handle_on_project_tabs

?>