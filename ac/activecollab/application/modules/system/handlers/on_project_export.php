<?php

  /**
   * System module on_project_export event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle project exporting
   *
   * @param array $exportable_modules
   * @param Project $project
   * @return null
   */
  function system_handle_on_project_export(&$exportable_modules, $project) {
    $exportable_modules[] = array(
      "url" => assemble_url('project_export',array('project_id' => $project->getId())),
      "title" => lang('Exporting project data...'),
      "module" => SYSTEM_MODULE,
      "label" => SYSTEM_MODULE,
    );
  } //tickets_handle_on_project_export


?>