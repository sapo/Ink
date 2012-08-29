<?php

  /**
   * Pages module on_project_export event handler
   *
   * @package activeCollab.modules.pages
   * @subpackage handlers
   */

  /**
   * Handle project exporting
   *
   * @param array $exportable_modules
   * @param Project $project
   * @return null
   */
  function pages_handle_on_project_export(&$exportable_modules, $project) {
    $exportable_modules[] = array(
      "url" => assemble_url('project_pages_export',array('project_id' => $project->getId())),
      "title" => lang('Exporting pages data...'),
      "module" => PAGES_MODULE,
      "label" => PAGES_MODULE,
    );
  } //pages_handle_on_project_export


?>