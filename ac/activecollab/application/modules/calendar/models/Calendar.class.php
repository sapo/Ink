<?php

  /**
   * Calendar manager class
   * 
   * @package activeCollab.modules.calendar
   * @subpackage calendar
   */
  class Calendar {
  
    // ---------------------------------------------------
    //  URL Sections
    // ---------------------------------------------------
    
    /**
     * Return dashboard calendar URL
     *
     * @param void
     * @return string
     */
    function getDashboardCalendarUrl() {
      return assemble_url('dashboard_calendar');
    } // getDashboardCalendarUrl
    
    /**
     * Return dashboard month URL
     *
     * @param integer $year
     * @param integer $month
     * @return string
     */
    function getDashboardMonthUrl($year, $month) {
      return assemble_url('dashboard_calendar_month', array(
    	  'year' => $year,
        'month' => $month
    	));
    } // getDashboardMonthUrl
    
    /**
     * Return dashboard calendar day URL
     *
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @return string
     */
    function getDashboardDayUrl($year, $month, $day) {
    	return assemble_url('dashboard_calendar_day', array(
    	  'year' => $year,
        'month' => $month,
        'day' => $day
    	));
    } // getDashboardDayUrl
    
    /**
     * Get project section URL
     *
     * @param ProjectObject $project
     * @return string
     */
    function getProjectCalendarUrl($project) {
    	return assemble_url('project_calendar', array('project_id' => $project->getId()));
    } // getProjectCalendarUrl
    
    /**
     * Return project month URL
     *
     * @param Project $project
     * @param integer $year
     * @param integer $month
     * @return string
     */
    function getProjectMonthUrl($project, $year, $month) {
      return assemble_url('project_calendar_month', array(
        'project_id' => $project->getId(),
        'year' => $year,
        'month' => $month,
      ));
    } // getProjectMonthUrl
    
    /**
     * Return project day URL
     *
     * @param Project $project
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @return string
     */
    function getProjectDayUrl($project, $year, $month, $day) {
      return assemble_url('project_calendar_day', array(
        'project_id' => $project->getId(),
        'year' => $year,
        'month' => $month,
        'day' => $day
      ));
    } // getProjectDayUrl
    
    /**
     * Return user profile calendar URL
     *
     * @param User $user
     * @return string
     */
    function getProfileCalendarUrl($user) {
      return assemble_url('profile_calendar', array(
        'user_id' => $user->getId(),
        'company_id' => $user->getCompanyId(),
      ));
    } // getProfileCalendarUrl
    
    /**
     * Return user profile month URL
     *
     * @param User $user
     * @param integer $year
     * @param integer $month
     * @return string
     */
    function getProfileMonthUrl($user, $year, $month) {
      return assemble_url('profile_calendar_month', array(
        'user_id' => $user->getId(),
        'company_id' => $user->getCompanyId(),
        'year' => $year,
        'month' => $month,
      ));
    } // getProfileMonthUrl
    
    /**
     * Return user profile day URL
     *
     * @param User $user
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @return string
     */
    function getProfileDayUrl($user, $year, $month, $day) {
      return assemble_url('profile_calendar_day', array(
        'user_id' => $user->getId(),
        'company_id' => $user->getCompanyId(),
        'year' => $year,
        'month' => $month,
        'day' => $day,
      ));
    } // getProfileDayUrl
    
    // ---------------------------------------------------
    //  Extractors
    // ---------------------------------------------------
    
    /**
     * Return data for active projects
     *
     * @param User $user
     * @param integer $month
     * @param integer $year
     * @return array
     */
    function getActiveProjectsData($user, $month, $year) {
    	$types = get_completable_project_object_types();
      
    	$filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE), $types);
    	
    	if($filter) {
    	  $filter .= db_prepare_string(' AND (state >= ? AND visibility >= ?)', array(STATE_VISIBLE, $user->getVisibility()));
    	  return Calendar::getMonthData($month, $year, $filter);
    	} // if
    	
    	return null;
    } // getActiveProjectsData
    
    /**
     * Return day data for active projects
     *
     * @param User $user
     * @param DateValue $day
     * @return array
     */
    function getActiveProjectsDayData($user, $day) {
    	$types = get_completable_project_object_types();
      
    	$filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE), $types);
    	
    	if($filter) {
    	  $filter .= db_prepare_string(' AND (state >= ? AND visibility >= ?)', array(STATE_VISIBLE, $user->getVisibility()));
    	  return Calendar::getDayData($day, $filter);
    	} // if
    	
    	return null;
    } // getActiveProjectsData
    
    /**
     * Return project data ready for rendering
     *
     * @param User $user
     * @param Project $project
     * @param integer $month
     * @param integer $year
     * @return array
     */
    function getProjectData($user, $project, $month, $year) {
      $types = get_completable_project_object_types();
      
    	$filter = ProjectUsers::getVisibleTypesFilterByproject($user, $project, $types);
    	
    	if($filter) {
    	  $filter .= db_prepare_string(' AND (state >= ? AND visibility >= ?)', array(STATE_VISIBLE, $user->getVisibility()));
    	  return Calendar::getMonthData($month, $year, $filter);
    	} else {
    	  return null;
    	} // if
    } // getProjectData
    
    /**
     * Return project day data
     *
     * @param User $user
     * @param Project $project
     * @param DateValue $day
     * @return array
     */
    function getProjectDayData($user, $project, $day) {
    	$types = get_completable_project_object_types();
      
    	$filter = ProjectUsers::getVisibleTypesFilterByproject($user, $project, $types);
    	
    	if($filter) {
    	  $filter .= db_prepare_string(' AND (state >= ? AND visibility >= ?)', array(STATE_VISIBLE, $user->getVisibility()));
    	  return Calendar::getDayData($day, $filter);
    	} else {
    	  return null;
    	} // if
    } // getProjectDayData
    
    /**
     * Return user for a given user
     * 
     * This can be viewed only by project manager so type filter is not 
     * necessery
     *
     * @param User $user
     * @param integer $month
     * @param integer $year
     * @returnay
     */
    function getUserData($user, $month, $year) {
    	$objects_table = TABLE_PREFIX . 'project_objects';
      $assignments_table = TABLE_PREFIX . 'assignments';
      
      return Calendar::getMonthData($month, $year, db_prepare_string("$objects_table.id = $assignments_table.object_id AND $assignments_table.user_id = ?", array($user->getId())), true);
    } // getUserData
    
    /**
     * Return user day data
     *
     * @param User $user
     * @param DateValue $day
     * @return array
     */
    function getUserDayData($user, $day) {
    	$objects_table = TABLE_PREFIX . 'project_objects';
      $assignments_table = TABLE_PREFIX . 'assignments';
      
      return Calendar::getDayData($day, db_prepare_string("$objects_table.id = $assignments_table.object_id AND $assignments_table.user_id = ?", array($user->getId())), true);
    } // getUserData
    
    /**
     * Prepare project data for a given month
     *
     * @param integer $month
     * @param integer $year
     * @param mixed $additional_conditions
     * @param boolean $include_assignments_table
     * @return array
     */
    function getMonthData($month, $year, $additional_conditions, $include_assignments_table = false) {
      $first_day = DateTimeValue::beginningOfMonth($month, $year);
      $last_day = DateTimeValue::endOfMonth($month, $year);

      // Than we define empty result...
      $result = array();
      for($i = 1; $i <= $last_day->getDay(); $i++) {
        $result["$year-$month-$i"] = array();
      } // for
      
      $objects_table = TABLE_PREFIX . 'project_objects';
      $assignments_table = TABLE_PREFIX . 'assignments';
      
      $conditions = db_prepare_string("$objects_table.due_on BETWEEN ? AND ?", array($first_day, $last_day));
      if($additional_conditions) {
        $conditions .= " AND $additional_conditions";
      } // if
      
      // If we don't have user ID-s filter we can exclude assignments table
      $tables = $include_assignments_table ? "$objects_table, $assignments_table" : $objects_table;
      
      $objects = ProjectObjects::findBySQL("SELECT DISTINCT $objects_table.* FROM $tables WHERE $conditions ORDER BY type, due_on");
      if(is_foreachable($objects)) {
        foreach($objects as $object) {
          $due_on = $object->getDueOn();
          $result[$due_on->getYear() . '-' . $due_on->getMonth() . '-' . $due_on->getDay()][] = $object;
        } // foreach
      } // if
      
      return $result;
    } // getMonthData
    
    /**
     * Return data for a given day
     *
     * @param DateValue $day
     * @param string $additional_conditions
     * @param boolean $include_assignments_table
     * @return array
     */
    function getDayData($day, $additional_conditions, $include_assignments_table = false) {
      $objects_table = TABLE_PREFIX . 'project_objects';
      $assignments_table = TABLE_PREFIX . 'assignments';
      
      $conditions = db_prepare_string("$objects_table.due_on = ?", array($day));
      if($additional_conditions) {
        $conditions .= " AND $additional_conditions";
      } // if
      
      // If we don't have user ID-s filter we can exclude assignments table
      $tables = $include_assignments_table ? "$objects_table, $assignments_table" : $objects_table;
      
      return ProjectObjects::findBySQL("SELECT DISTINCT $objects_table.* FROM $tables WHERE $conditions ORDER BY priority DESC");
    } // getDayData
    
  } // Calendar

?>