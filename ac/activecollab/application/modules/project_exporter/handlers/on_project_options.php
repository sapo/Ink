<?php

  /**
   * Project Exporter module on_project_options event handler
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage handlers
   */
  
  /**
   * Handle on project options event
   *
   * @param NamedList $options
   * @param Project $project
   * @param User $user
   * @return null
   */
  function project_exporter_handle_on_project_options(&$options, $project, $user) {
    if($user->isAdministrator() || $user->isProjectLeader($project) || $user->isProjectManager()) {
      $options->add('export_project', array(
        'url' => assemble_url('project_exporter', array('project_id' => $project->getId())),
        'text' => lang('Export Project'),
      ));
    } //
  } // project_exporter_handle_on_project_options

?>