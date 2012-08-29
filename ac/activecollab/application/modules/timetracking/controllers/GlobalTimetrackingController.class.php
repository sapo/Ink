<?php

  /**
   * Global timetracking controller definition
   *
   * @package activeCollab.modules.timetracking
   * @subpackage controllers
   */
  class GlobalTimetrackingController extends ApplicationController {
    
    /**
     * PHP4 friendly controller name
     *
     * @var string
     */
    var $controller_name = 'global_timetracking';
    
    /**
     * Active time report
     *
     * @var TimeReport
     */
    var $active_report;
    
    /**
     * Constructor
     *
     * @param Request $request
     * @return PeopleController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if(!$this->logged_user->isAdministrator() && !$this->logged_user->getSystemPermission('use_time_reports')) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->wireframe->addBreadCrumb(lang('Time'), assemble_url('global_time'));
      if(TimeReport::canAdd($this->logged_user)) {
        $this->wireframe->addPageAction(lang('New Report'), assemble_url('global_time_report_add'));
      } // if
      
      $report_id = $this->request->getId('report_id');
      if($report_id) {
        $this->active_report = TimeReports::findById($report_id);
      } // if
      
      if(instance_of($this->active_report, 'TimeReport')) {
        $this->wireframe->addBreadCrumb($this->active_report->getName(), $this->active_report->getUrl());
      } else {
        $this->active_report = new TimeReport();
      } // if
      
      $this->wireframe->current_menu_item = 'time';
      
      $this->smarty->assign('active_report', $this->active_report);
    } // __construct
    
    /**
     * Show global timetracking summaries
     *
     * @param void
     * @return null
     */
    function index() {
      $default_report = TimeReports::findDefault();
      if(instance_of($default_report, 'Timereport')) {
        $this->redirectToUrl($default_report->getUrl());
      } else {
        $this->httpError(HTTP_ERR_OPERATION_FAILED);
      } // if
    } // index
    
    /**
     * Show single report
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
      
      $report_records =  TimeReports::executeReport($this->logged_user, $this->active_report);
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
    	  'show_project'    => true,
    	));
    } // report
    
    /**
     * Export report as CSV
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
      
      download_contents(array_to_csv(TimeReports::executeReportForExport($this->logged_user, $this->active_report)), 'text/csv', "time-report.csv", true);
    } // report_export
    
    /**
     * Create new report
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      if(!TimeReport::canAdd($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $project = null;
    	$add_report_url = null;
    	
    	$project_id = (integer) $this->request->get('project_id');
    	if($project_id) {
    	  $project = Projects::findById($project_id);
    	  if(instance_of($project, 'Project')) {
    	    $add_report_url = assemble_url('global_time_report_add', array('project_id' => $project_id));
    	  } // if
    	} // if
    	
    	if($add_report_url === null) {
    	  $add_report_url = assemble_url('global_time_report_add');
    	} // if
      
    	$report_data = $this->request->post('report');
    	if(empty($report_data)) {
    	  $report_data = array(
    	    'user_filter' => USER_FILTER_LOGGED_USER,
    	  );
    	} // if
    	
    	$this->smarty->assign(array(
    	  'report_data' => $report_data,
    	  'add_report_url' => $add_report_url,
    	));
    	
    	if($this->request->isSubmitted()) {
    	  $this->active_report = new TimeReport();
    	  $this->active_report->setAttributes($report_data);
    	  $this->active_report->setUserFilterData(array_var($report_data, 'user_filter_data'));
    	  
    	  $save = $this->active_report->save();
    	  if($save && !is_error($save)) {
    	    flash_success("Report ':name' has been created", array('name' => $this->active_report->getName()));
    	    $this->redirectToUrl($this->active_report->getUrl($project));
    	  } else {
    	    $this->smarty->assign('errors', $save);
    	  }
    	} // if
    } // add
    
    /**
     * Update existing report
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->active_report->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_report->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $active_project = null;
      $project_id = $this->request->getId('project_id');
      if($project_id) {
        $active_project = Projects::findById($project_id);
      } // if
      
    	$report_data = $this->request->post('report');
    	if(empty($report_data)) {
    	  $report_data = array(
    	    'name' => $this->active_report->getName(),
    	    'group_name' => $this->active_report->getGroupName(),
    	    'user_filter' => $this->active_report->getUserFilter(),
    	    'user_filter_data' => $this->active_report->getUserFilterData(),
    	    'billable_filter' => $this->active_report->getBillableFilter(),
    	    'date_filter' => $this->active_report->getDateFilter(),
    	    'date_from' => $this->active_report->getDateFrom(),
    	    'date_to' => $this->active_report->getDateTo(),
    	    'sum_by_user' => $this->active_report->getSumByUser(),
    	  );
    	} // if
    	
    	$this->smarty->assign(array(
    	  'report_data' => $report_data,
    	  'active_project' => $active_project,
    	));
    	
    	if($this->request->isSubmitted()) {
    	  $old_name = $this->active_report->getName();
    	  
    	  $this->active_report->setAttributes($report_data);
    	  $this->active_report->setUserFilterData(array_var($report_data, 'user_filter_data'));
    	  
    	  $save = $this->active_report->save();
    	  if($save && !is_error($save)) {
    	    flash_success("Report ':name' has been updated", array('name' => $old_name));
    	    $this->redirectToUrl($this->active_report->getUrl($active_project));
    	  } else {
    	    $this->smarty->assign('errors', $save);
    	  }
    	} // if
    } // edit
    
    /**
     * Delete existing report
     *
     * @param void
     * @return null
     */
    function delete() {
    	if($this->active_report->isNew()) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_report->canDelete($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
    	
    	if($this->request->isSubmitted()) {
    	  $delete = $this->active_report->delete();
    	  if($delete && !is_error($delete)) {
    	    flash_success("Report ':name' has been deleted", array('name' => $this->active_report->getName()));
    	  } else {
    	    flash_error("Failed to delete ':name' report", array('name' => $this->active_report->getName()));
    	  } // if
    	  $this->redirectTo('global_time');
    	} // if
    } // delete
    
    /**
     * Render form partials
     *
     * @param void
     * @return null
     */
    function partial_generator() {
      $select_box = $this->request->get('select_box');
    	
    	// remove report[...] around the value we need
    	$select_box = substr($select_box, 7, strlen($select_box) - 8);
    	$option_value = $this->request->get('option_value');
    	
    	switch($select_box) {
    	  case 'user_filter':
    	    if($option_value == 'company') {
    	      require_once SYSTEM_MODULE_PATH . '/helpers/function.select_company.php';
    	      print smarty_function_select_company(array('name' => 'report[user_filter_data]'), $this->smarty);
    	    } elseif($option_value == USER_FILTER_SELECTED) {
    	      require_once SYSTEM_MODULE_PATH . '/helpers/function.select_users.php';
    	      print smarty_function_select_users(array('name' => 'report[user_filter_data]'), $this->smarty);
    	    } // if
    	    break;
    	  case 'date_filter':
    	    require_once SMARTY_PATH . '/plugins/function.select_date.php';
    	    if($option_value == 'selected_date') {
    	      print smarty_function_select_date(array('name' => 'report[date_from]'), $this->smarty);
    	    } elseif($option_value == 'selected_range') {
    	      print '<table>
    	        <tr>
    	          <td>' . smarty_function_select_date(array('name' => 'report[date_from]'), $this->smarty) . '</td>
    	          <td style="width: 10px; text-align: center;">-</td>
    	          <td>' . smarty_function_select_date(array('name' => 'report[date_to]'), $this->smarty) . '</td>
    	        </tr>
    	      </table>';
    	    } // if
    	    break;
    	} // switch
      die();
    } // partial_generator
    
  }

?>