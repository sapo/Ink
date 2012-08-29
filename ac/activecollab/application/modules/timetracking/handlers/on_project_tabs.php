<?php

  /**
   * Timetracking module on_project_tabs event handler
   *
   * @package activeCollab.modules.timetracking
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
  function timetracking_handle_on_project_tabs(&$tabs, &$logged_user, &$project) {
    if($logged_user->getProjectPermission('timerecord', $project) >= PROJECT_PERMISSION_ACCESS) {
      $tabs->add('time', array(
        'text' => lang('Time'),
        'url' => timetracking_module_url($project),
      ));
    } // if
  } // timetracking_handle_on_project_tabs

?>