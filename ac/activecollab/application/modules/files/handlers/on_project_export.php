<?php

  /**
   * Files module on_project_export event handler
   *
   * @package activeCollab.modules.files
   * @subpackage handlers
   */

  /**
   * Handle project exporting
   *
   * @param array $exportable_modules
   * @param Project $project
   * @return null
   */
  function files_handle_on_project_export(&$exportable_modules, $project) {
    $exportable_modules[] = array(
      "url" => assemble_url('project_files_export',array('project_id' => $project->getId())),
      "title" => lang('Exporting files data...'),
      "module" => FILES_MODULE,
      "label" => FILES_MODULE,
    );
  } //files_handle_on_project_export


?>