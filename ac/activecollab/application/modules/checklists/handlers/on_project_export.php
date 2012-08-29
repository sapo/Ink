<?php

  /**
   * Checklist module on_project_export event handler
   *
   * @package activeCollab.modules.checklists
   * @subpackage handlers
   */

  /**
   * Handle project exporting
   *
   * @param array $exportable_modules
   * @param Project $project
   * @return null
   */
  function checklists_handle_on_project_export(&$exportable_modules, $project) {
    $exportable_modules[] = array(
      "url" => assemble_url('project_checklists_export',array('project_id' => $project->getId())),
      "title" => lang('Exporting checklists data...'),
      "module" => CHECKLISTS_MODULE,
      "label" => CHECKLISTS_MODULE,
    );
  } //checklists_handle_on_project_export


?>