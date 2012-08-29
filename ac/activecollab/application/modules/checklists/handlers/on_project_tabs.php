<?php

  /**
   * Checklists module on_project_tabs event handler
   *
   * @package activeCollab.modules.checklists
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
  function checklists_handle_on_project_tabs(&$tabs, &$logged_user, &$project) {
    if($logged_user->getProjectPermission('checklist', $project) >= PROJECT_PERMISSION_ACCESS) {
      $tabs->add('checklists', array(
        'text' => lang('Checklists'),
        'url' => checklists_module_url($project)
      ));
    } // if
  } // checklists_handle_on_project_tabs

?>