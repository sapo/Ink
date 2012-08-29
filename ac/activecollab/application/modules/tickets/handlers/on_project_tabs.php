<?php

  /**
   * Tickets module on_project_tabs event handler
   *
   * @package activeCollab.modules.tickets
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
  function tickets_handle_on_project_tabs(&$tabs, &$logged_user, &$project) {
    if($logged_user->getProjectPermission('ticket', $project) >= PROJECT_PERMISSION_ACCESS) {
      $tabs->add('tickets', array(
        'text' => lang('Tickets'),
        'url' => tickets_module_url($project)
      ));
    } // if
  } // tickets_handle_on_project_tabs

?>