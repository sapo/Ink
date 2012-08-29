<?php

  /**
   * Source control module on_project_tabs event handler
   *
   * @package activeCollab.modules.source
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
  function source_handle_on_project_tabs(&$tabs, &$logged_user, &$project) {
    if($logged_user->getProjectPermission('repository', $project) >= PROJECT_PERMISSION_ACCESS) {
      $tabs->add('source', array(
        'text' => lang('Source'),
        'url' => source_module_url($project)
      ));
    } // if
  } // source_handle_on_project_tabs

?>