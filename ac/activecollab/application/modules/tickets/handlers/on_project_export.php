<?php

  /**
   * Tickets module on_project_export event handler
   *
   * @package activeCollab.modules.tickets
   * @subpackage handlers
   */

  /**
   * Handle project exporting
   *
   * @param array $exportable_modules
   * @param Project $project
   * @return null
   */
  function tickets_handle_on_project_export(&$exportable_modules, $project) {
    $exportable_modules[] = array(
      "url" => assemble_url('project_tickets_export',array('project_id' => $project->getId())),
      "title" => lang('Exporting ticket data...'),
      "module" => TICKETS_MODULE,
      "label" => TICKETS_MODULE,
    );
  } //tickets_handle_on_project_export


?>