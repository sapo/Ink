<?php

  /**
   * Discussions module on_project_export event handler
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Handle project exporting
   *
   * @param array $exportable_modules
   * @param Project $project
   * @return null
   */
  function discussions_handle_on_project_export(&$exportable_modules, $project) {
    $exportable_modules[] = array(
      "url" => assemble_url('project_discussions_export',array('project_id' => $project->getId())),
      "title" => lang('Exporting discussions data...'),
      "module" => DISCUSSIONS_MODULE,
      "label" => DISCUSSIONS_MODULE,
    );
  } //tickets_handle_on_project_export


?>