<?php

  /**
   * Time Tracking module on_project_export event handler
   *
   * @package activeCollab.modules.timetracking
   * @subpackage handlers
   */

  /**
   * Handle project exporting
   *
   * @param array $exportable_modules
   * @param Project $project
   * @return null
   */
  function timetracking_handle_on_project_export(&$exportable_modules, $project) {
    $exportable_modules[] = array(
      "url" => assemble_url('project_time_export',array('project_id' => $project->getId())),
      "title" => lang('Exporting time tracking data...'),
      "module" => TIMETRACKING_MODULE,
      "label" => "Time",
    );
  } //timetracking_handle_on_project_export


?>