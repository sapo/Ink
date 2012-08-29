<?php

  /**
   * Init timetracking module
   *
   * @package activeCollab.modules.timetracking
   */
  
  define('TIMETRACKING_MODULE', 'timetracking');
  define('TIMETRACKING_MODULE_PATH', APPLICATION_PATH . '/modules/timetracking');
  
  define('BILLABLE_FILTER_ALL', 'all');
  define('BILLABLE_FILTER_BILLABLE', 'billable');
  define('BILLABLE_FILTER_NOT_BILLABLE', 'not_billable');
  define('BILLABLE_FILTER_BILLABLE_BILLED', 'billable_billed');
  define('BILLABLE_FILTER_BILLABLE_NOT_BILLED', 'billable_not_billed');
  define('BILLABLE_FILTER_PENDING_PAYMENT', 'pending_payment');
  
  define('BILLABLE_STATUS_NOT_BILLABLE', 0);
  define('BILLABLE_STATUS_BILLABLE', 1);
  define('BILLABLE_STATUS_PENDING_PAYMENT', 2);
  define('BILLABLE_STATUS_BILLED', 3);
  
  require TIMETRACKING_MODULE_PATH . '/functions.php';
  
  use_model('time_reports', TIMETRACKING_MODULE);
  set_for_autoload(array(
    'TimeRecord' => TIMETRACKING_MODULE_PATH . '/models/timetracking/TimeRecord.class.php', 
    'TimeRecords' => TIMETRACKING_MODULE_PATH . '/models/timetracking/TimeRecords.class.php', 
    'TimeAddedActivityLog' => TIMETRACKING_MODULE_PATH . '/models/activity_logs/TimeAddedActivityLog.class.php', 
  ));
	
	/**
   * Return section URL
   *
   * @param Project $project
   * @param ProjectObject $object
   * @return string
   */
  function timetracking_module_url($project, $object = null) {
    $params = array('project_id' => $project->getId());
    if(instance_of($object, 'ProjectObject')) {
      $params['for'] = $object->getId();
    } // if
    
    return assemble_url('project_time', $params);
  } // timetracking_module_url
  
  /**
   * Return add discussion URL
   *
   * @param Project $project
   * @param array $additional
   * @return string
   */
  function timetracking_module_add_record_url($project, $additional = null) {
    $params = array('project_id' => $project->getId());
    if(is_foreachable($additional)) {
      $params = array_merge($params, $additional);
    } // if
    
    return assemble_url('project_time_add', $params);
  } // timetracking_module_add_record_url
  
  /**
   * Return Reports URL
   *
   * @param Project $project
   * @return string
   */
  function timetracking_module_reports_url($project) {
  	return assemble_url('project_time_reports', array('project_id' => $project->getId()));
  } // timetracking_module_reports_url
  
  /**
   * Returns true if $user can track time for $object
   *
   * @param User $user
   * @param ProjectObject $object
   * @return boolean
   */
  function timetracking_can_add_for($user, $object) {
  	return TimeRecord::canAddFor($user, $object);
  } // timetracking_can_add_for

?>