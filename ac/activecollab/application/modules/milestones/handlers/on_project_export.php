<?php

  /**
   * Milestones module on_project_export event handler
   *
   * @package activeCollab.modules.milestones
   * @subpackage handlers
   */

  /**
   * Handle project exporting
   *
   * @param array $exportable_modules
   * @param Project $project
   * @return null
   */
  function milestones_handle_on_project_export(&$exportable_modules, $project) {
    $exportable_modules[] = array(
      "url" => assemble_url('project_milestones_export',array('project_id' => $project->getId())),
      "title" => lang('Exporting milestones data...'),
      "module" => MILESTONES_MODULE,
      "label" => MILESTONES_MODULE,
    );
  } // milestones_handle_on_project_export


?>