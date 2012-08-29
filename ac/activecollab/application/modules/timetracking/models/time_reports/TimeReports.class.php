<?php

  /**
   * TimeReports class
   * 
   * @package activeCollab.modules.timetracking
   * @subpackage models
   */
  class TimeReports extends BaseTimeReports {
  
    /**
     * Return all reports grouped by group name
     *
     * @param void
     * @return array
     */
    function findGrouped() {
      $result = null;
      
      $all = TimeReports::find(array(
        'order' => 'group_name, name'
      ));
      
      if(is_foreachable($all)) {
        $result = array();
        $other_reports = array();
        
        foreach($all as $report) {
          if($group_name = $report->getGroupName()) {
            if(!isset($result[$group_name])) {
              $result[$group_name] = array();
            } // if
            $result[$group_name][] = $report;
          } else {
            $other_reports[] = $report;
          } // if
        } // foreach
        
        if(count($other_reports)) {
          $result[lang('Other')] = $other_reports;
        } // if
      } // if
      
      return $result;
    } // findGrouped
    
    /**
     * Return default time report
     *
     * @param void
     * @return TimeReport
     */
    function findDefault() {
    	return TimeReports::find(array(
    	  'conditions' => array('is_default = ?', true),
    	  'one' => true
    	));
    } // findDefault
    
    /**
     * Execute report
     *
     * @param User $user
     * @param TimeReport $report
     * @param Project $project
     * @return array
     */
    function executeReport($user, $report, $project = null) {
      $conditions = $report->prepareConditions($user, $project);
      if(empty($conditions)) {
        return null;
      } // if
      
    	if($report->getSumByUser()) {
    	  $rows = db_execute_all('SELECT SUM(float_field_1) AS total_time, integer_field_1 AS user_id FROM ' . TABLE_PREFIX . 'project_objects WHERE ' . $conditions . ' GROUP BY integer_field_1');
    	  if(is_foreachable($rows)) {
    	    $result = array();
    	    foreach($rows as $row) {
    	      $user = Users::findById($row['user_id']);
    	      if(instance_of($user, 'User')) {
    	        $result[] = array(
    	          'user' => $user,
    	          'total_time' => float_format($row['total_time'], 2),
    	        );
    	      } // if
    	    } // foreach
    	    return $result;
    	  } else {
    	    return null;
    	  } // if
    	} else {
    	  return TimeRecords::findBySQL('SELECT * FROM ' . TABLE_PREFIX . 'project_objects WHERE ' . $conditions . ' ORDER BY date_field_1');
    	} // if
    } // executeReport
    
    /**
     * Execute report and prepare it for export
     *
     * @param User $user
     * @param TimeReport $report
     * @param Project $project
     * @return array
     */
    function executeReportForExport($user, $report, $project = null) {
      $report_records = TimeReports::executeReport($user, $report, $project);
      
    	$csv_content = array();
      if($report->getSumByUser()) {
        $csv_content[] = array('Person', 'Hours');
        
        if(is_foreachable($report_records)) {
          foreach($report_records as $report_record) {
            $csv_content[] = array($report_record['user']->getDisplayName(), (string) $report_record['total_time']);
          } // foreach
        } // if
      } else {
        $csv_content[] = array('Date', 'Person', 'Project ID', 'Project Name', 'Hours', 'Summary', 'Parent Summary', 'Billable', 'Billed', 'Billed Status');
        
        if(is_foreachable($report_records)) {
          foreach($report_records as $time_record) {
            if($time_record->isBillable()) {
              $billable = 'Yes';
              $billed = $time_record->isBilled() ? 'Yes' : 'No';
         	  }else {
         	  	$billable = 'No';
         	  	$billed = 'No';
         	  } // if
         	  
         	  $date = $time_record->getRecordDate();
         	  $person = $time_record->getUser();
         	  $project = $time_record->getProject();
         	  $parent = $time_record->getParent();
        	  
        	  $csv_content[] = array(
         	    $date->toMySql(),
         	    $person->getDisplayName(),
         	    $time_record->getProjectId(),
         	    $project->getName(),
         	    (string) $time_record->getValue(),
         	    $time_record->getBody(),
         	    instance_of($parent, 'ProjectObject') ? $parent->getName() : '',
         	    $billable,
         	    $billed,
         	    (integer) $time_record->getBillableStatus(),
        	  );
          } // foreach
        } // if
      } // if
      
      return $csv_content;
    } // executeReportForExport
  
  }

?>