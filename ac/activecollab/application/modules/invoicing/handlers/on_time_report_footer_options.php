<?php

  /**
   * on_time_report_footer_options event handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */
  
  /**
   * on_time_report_footer_options event handler implementation
   *
   * @param TimeReport $report
   * @param array $options
   * @param Project $project
   * @param User $user
   * @return null
   */
  function invoicing_handle_on_time_report_footer_options(&$report, &$options, &$project, &$user) {
    if(Invoice::canAdd($user)) {
      $options[] = array(
        'url' => instance_of($project, 'Project') ? assemble_url('invoices_add', array('time_report_id' => $report->getId(), 'project_id' => $project->getId())) : assemble_url('invoices_add', array('time_report_id' => $report->getId())),
        'text' => lang('New Invoice'),
        'icon' => get_image_url('create-invoice.gif', INVOICING_MODULE),
      );
    } // if
  } // invoicing_handle_on_time_report_footer_options

?>