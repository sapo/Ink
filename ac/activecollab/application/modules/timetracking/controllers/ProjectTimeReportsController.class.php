<?php

  // Extend timetracking controller
  use_controller('timetracking', TIMETRACKING_MODULE);

  /**
   * Handle project time report related actions
   *
   * @package activeCollab.modules.timetracking
   * @subpackage controllers
   */
  class ProjectTimeReportsController extends TimetrackingController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'project_time_reports';
    
    /**
     * Selected report
     *
     * @var TimeReport
     */
    var $active_report;
    
    /**
     * Contruct time report controller
     *
     * @param Request $request
     * @return ProjectTimeReportsController
     */
    function __construct($request) {
    	parent::__construct($request);
    	
    	if(!$this->logged_user->isAdministrator() && !$this->logged_user->getSystemPermission('use_time_reports')) {
    	  $this->httpError(HTTP_ERR_FORBIDDEN);
    	} // if
    	
    	$this->wireframe->addBreadCrumb(lang('Reports'), timetracking_module_reports_url($this->active_project));
    	
    	$report_id = $this->request->getId('report_id');
      if($report_id) {
        $this->active_report = TimeReports::findById($report_id);
      } // if
      
      if(instance_of($this->active_report, 'TimeReport')) {
        $this->wireframe->addBreadCrumb($this->active_report->getName(), $this->active_report->getUrl());
      } else {
        $this->active_report = new TimeReport();
      } // if
      
      $this->wireframe->page_actions = array();
      if(TimeReport::canAdd($this->logged_user)) {
        $this->wireframe->addPageAction(lang('New Report'), assemble_url('global_time_report_add', array('project_id' => $this->active_project->getId())));
      } // if
      
      $this->smarty->assign('active_report', $this->active_report);
    } // __construct
    
    /**
     * Redirect to default report
     *
     * @param void
     * @return null
     */
    function index() {
    	$default_report = TimeReports::findDefault();
      if(instance_of($default_report, 'Timereport')) {
        $this->redirectToUrl($default_report->getUrl($this->active_project));
      } else {
        $this->httpError(HTTP_ERR_OPERATION_FAILED);
      } // if
    } // index
    
    /**
     * Show a single report
     *
     * @param void
     * @return null
     */
    function report() {
    	if($this->active_report->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_report->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $report_records =  TimeReports::executeReport($this->logged_user, $this->active_report, $this->active_project);
      $total_time = 0;
      if(is_foreachable($report_records)) {
        if($this->active_report->getSumByUser()) {
          foreach($report_records as $report_record) {
            $total_time += $report_record['total_time'];
          } // foreach
        } else {
          foreach($report_records as $report_record) {
            $total_time += $report_record->getValue();
          } // foreach
        } // if
      } // if
      
    	$this->smarty->assign(array(
    	  'grouped_reports' => TimeReports::findGrouped(),
    	  'report_records'  => $report_records,
    	  'total_time'      => $total_time,
    	  'show_project'    => false,
    	));
    	
    	$this->setTemplate(array(
    	  'module' => TIMETRACKING_MODULE,
    	  'controller' => 'global_timetracking',
    	  'template' => 'report',
    	));
    } // report
    
    /**
     * Export report data as CSV
     *
     * @param void
     * @return null
     */
    function report_export() {
    	if($this->active_report->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_report->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      download_contents(array_to_csv(TimeReports::executeReportForExport($this->logged_user, $this->active_report, $this->active_project)), 'text/csv', "time-report.csv", true);
    } // report_export
    
  }

?>