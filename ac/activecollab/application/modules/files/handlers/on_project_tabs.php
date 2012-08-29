<?php

  /**
   * Files module on_project_tabs event handler
   *
   * @package activeCollab.modules.files
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
  function files_handle_on_project_tabs(&$tabs, &$logged_user, &$project) {
    if($logged_user->getProjectPermission('file', $project) >= PROJECT_PERMISSION_ACCESS) {
      $tabs->add('files', array(
        'text' => lang('Files'),
        'url' => files_module_url($project),
      ));
    } // if
  } // files_handle_on_project_tabs

?>