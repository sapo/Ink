<?php

  /**
   * TimeReport class
   * 
   * @package activeCollab.mobules.timetracking
   * @subpackage models
   */
  class TimeReport extends BaseTimeReport {
    
    /**
     * Prepare conditions based on report settings
     *
     * @param User $user
     * @param Project $project
     * @return string
     */
    function prepareConditions($user, $project = null) {
    	$project_objects_table = TABLE_PREFIX . 'project_objects';
      
      $conditions = array();
      
      // Project and type
      if(instance_of($project, 'Project')) {
        $conditions[] = db_prepare_string('project_id = ? AND type = ?', array($project->getId(), 'timerecord'));
      } else {
        $conditions[] = db_prepare_string('type = ?', array('timerecord'));
      } // if
      
      // User filter
      switch($this->getUserFilter()) {
        
        // Anyone - This filter used to filter only time tracked from all 
        // visible users, but that did not include deleted accounts. Fixed now
        //
        //case USER_FILTER_ANYBODY:
        //  $visible_user_ids = $user->visibleUserIds();
        //  if(is_foreachable($visible_user_ids)) {
        //    $conditions[] = "($project_objects_table.integer_field_1 IN (" . db_escape($visible_user_ids) . '))';
        //  } else {
        //    return false; // not visible users
        //  } // if
        //  break;
        
        // Logged user
        case USER_FILTER_LOGGED_USER:
          $user_id = $user->getId();
          $conditions[] = "($project_objects_table.integer_field_1 = $user_id)";
          break;
          
        // All members of a specific company
        case USER_FILTER_COMPANY:
          $visible_user_ids = $user->visibleUserIds();
          if(!is_foreachable($visible_user_ids)) {
            return false;
          } // if
          
          $company_id = $this->getUserFilterData();
          if($company_id) {
            $company = Companies::findById($company_id);
            if(instance_of($company, 'Company')) {
              $user_ids = Users::findUserIdsByCompany($company);
              if(is_foreachable($user_ids)) {
                foreach($user_ids as $k => $v) {
                  if(!in_array($v, $visible_user_ids)) {
                    unset($user_ids[$k]);
                  } // if
                } // if
                
                if(count($user_ids) > 0) {
                  $imploded = implode(', ', $user_ids);
                  $conditions[] = "($project_objects_table.integer_field_1 IN ($imploded))";
                } else {
                  return false;
                } // if
              } // if
            } // if
          } // if
          break;
          
        // Selected users
        case USER_FILTER_SELECTED:
          $visible_user_ids = $user->visibleUserIds();
          if(!is_foreachable($visible_user_ids)) {
            return false;
          } // if
          
          $user_ids = $this->getUserFilterData();
          if(is_foreachable($user_ids)) {
            foreach($user_ids as $k => $v) {
              if(!in_array($v, $visible_user_ids)) {
                unset($user_ids[$k]);
              } // if
            } // foreach
            
            if(count($user_ids) > 0) {
              $imploded = implode(', ', $user_ids);
              $conditions[] = "($project_objects_table.integer_field_1 IN ($imploded))";
            } else {
              return false;
            } // if
          } // if
          break;
      } // switch
      
      $today = new DateValue(time() + get_user_gmt_offset($user)); // Calculate user timezone when determining today
      switch($this->getDateFilter()) {
          
        // List time records posted for today
        case DATE_FILTER_TODAY:
          $today_str = db_escape($today->toMySQL());
          $conditions[] = "($project_objects_table.date_field_1 = $today_str)";
          break;
          
        // List next week records
        case DATE_FILTER_LAST_WEEK:
          $first_day_sunday = UserConfigOptions::getValue('time_first_week_day', $user) == 0;
          
          $last_week = $today->advance(-604800, false);
          
          $week_start = $last_week->beginningOfWeek($first_day_sunday);
          $week_end = $last_week->endOfWeek($first_day_sunday);
          
          $week_start_str = db_escape($week_start->toMySQL());
          $week_end_str = db_escape($week_end->toMySQL());
          
          $conditions[] = "($project_objects_table.date_field_1 >= $week_start_str AND $project_objects_table.date_field_1 <= $week_end_str)";
          break;
          
        // List this week records
        case DATE_FILTER_THIS_WEEK:
          $first_day_sunday = UserConfigOptions::getValue('time_first_week_day', $user) == 0;
          
          $week_start = $today->beginningOfWeek($first_day_sunday);
          $week_end = $today->endOfWeek($first_day_sunday);
          
          $week_start_str = db_escape($week_start->toMySQL());
          $week_end_str = db_escape($week_end->toMySQL());
          
          $conditions[] = "($project_objects_table.date_field_1 >= $week_start_str AND $project_objects_table.date_field_1 <= $week_end_str)";
          break;
          
        // List this month time records
        case DATE_FILTER_LAST_MONTH:
          $month = $today->getMonth() - 1;
          $year = $today->getYear();
          
          if($month == 0) {
            $month = 12;
            $year -= 1;
          } // if
          
          $month_start = DateTimeValue::beginningOfMonth($month, $year);
          $month_end = DateTimeValue::endOfMonth($month, $year);
          
          $month_start_str = db_escape($month_start->toMySQL());
          $month_end_str = db_escape($month_end->toMySQL());
          
          $conditions[] = "($project_objects_table.date_field_1 >= $month_start_str AND $project_objects_table.date_field_1 <= $month_end_str)";
          break;
          
        // Last month
        case DATE_FILTER_THIS_MONTH:
          $month_start = DateTimeValue::beginningOfMonth($today->getMonth(), $today->getYear());
          $month_end = DateTimeValue::endOfMonth($today->getMonth(), $today->getYear());
          
          $month_start_str = db_escape($month_start->toMySQL());
          $month_end_str = db_escape($month_end->toMySQL());
          
          $conditions[] = "($project_objects_table.date_field_1 >= $month_start_str AND $project_objects_table.date_field_1 <= $month_end_str)";
          break;
          
        // Specific date
        case DATE_FILTER_SELECTED_DATE:
          $date_from = $this->getDateFrom();
          if(instance_of($date_from, 'DateValue')) {
            $date_from_str = db_escape($date_from->toMySql());
            $conditions[] = "($project_objects_table.date_field_1 = $date_from_str)";
          } // if
          break;
          
        // Specific range
        case DATE_FILTER_SELECTED_RANGE:
          $date_from = $this->getDateFrom();
          $date_to = $this->getDateTo();
          
          if(instance_of($date_from, 'DateValue') && instance_of($date_to, 'DateValue')) {
            $date_from_str = db_escape($date_from->toMySQL());
            $date_to_str = db_escape($date_to->toMySQL());
            
            $conditions[] = "($project_objects_table.date_field_1 >= $date_from_str AND $project_objects_table.date_field_1 <= $date_to_str)";
          } // if
          break;
      } // switch
      
      // Billable filter
      switch($this->getBillableFilter()) {
        case BILLABLE_FILTER_BILLABLE:
          $conditions[] = "($project_objects_table.integer_field_2 = '" . BILLABLE_STATUS_BILLABLE . "')";
          break;
        case BILLABLE_FILTER_NOT_BILLABLE:
          $conditions[] = "($project_objects_table.integer_field_2 = '" . BILLABLE_STATUS_NOT_BILLABLE . "' OR $project_objects_table.integer_field_2 IS NULL)";
          break;
        case BILLABLE_FILTER_BILLABLE_BILLED:
          $conditions[] = "($project_objects_table.integer_field_2 >= '" . BILLABLE_STATUS_BILLED . "')";
          break;
        case BILLABLE_FILTER_PENDING_PAYMENT:
          $conditions[] = "($project_objects_table.integer_field_2 = '" . BILLABLE_STATUS_PENDING_PAYMENT . "')";
          break;
        case BILLABLE_FILTER_BILLABLE_NOT_BILLED:
          $conditions[] = "($project_objects_table.integer_field_2 IN ('" . BILLABLE_STATUS_BILLABLE . "', '" . BILLABLE_STATUS_PENDING_PAYMENT . "'))";
          break;
      } // switch
      
      // Additional filters
      $state = STATE_VISIBLE;
      $visibility = $user->getVisibility();
      
      $conditions[] = "($project_objects_table.state >= $state AND $project_objects_table.visibility >= $visibility)";
      
      return implode(' AND ', $conditions);
    } // prepareConditions
    
    /**
     * Cached verbose user filter data
     *
     * @var string
     */
    var $verbose_user_filter_data = false;
    
    /**
     * Return verbose user filter data
     *
     * @param void
     * @return string
     */
    function getVerboseUserFilterData() {
      if($this->verbose_user_filter_data === false) {
        switch($this->getUserFilter()) {
          case USER_FILTER_COMPANY:
            $company = Companies::findById($this->getUserFilterData());
      	    if(instance_of($company, 'Company')) {
      	      $this->verbose_user_filter_data = $company->getName();
      	    } // if
            
            break;
            
          case USER_FILTER_SELECTED:
            $user_ids = $this->getUserFilterData();
            
        	  if(is_foreachable($user_ids)) {
        	    $users = Users::findByIds($user_ids);
        	    if(is_foreachable($users)) {
        	      $user_names = array();
        	      foreach($users as $user) {
        	        $user_names[] = $user->getDisplayName();
        	      } // foreach
        	      $this->verbose_user_filter_data = implode(', ', $user_names);
        	    } // if
        	  } // if
            
            break;
            
          case USER_FILTER_LOGGED_USER:
            $this->verbose_user_filter_data = lang('person using this report');
            break;
            
          default:
            $this->verbose_user_filter_data = lang('anyone');
            break;
        } // switch
      } // if
      
      return $this->verbose_user_filter_data;
    } // getVerboseUserFilterData
    
    /**
     * Report options
     *
     * @var array
     */
    var $footer_options = false;
    
    /**
     * Return array of report options
     *
     * @param Project $project
     * @param User $user
     * @return array
     */
    function getFooterOptions($project, $user) {
      if($this->footer_options === false) {
        $this->footer_options = array(
          array(
            'url' => $this->getExportUrl($project),
            'text' => lang('Export CSV'),
            'icon' => get_image_url('csv.gif'),
          ),
        );
        
        event_trigger('on_time_report_footer_options', array(&$this, &$this->footer_options, &$project, &$user));
      } // if
      
      return $this->footer_options;
    } // getFooterOptions
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can see this report
     *
     * @param User $user
     * @return boolean
     */
    function canView($user) {
    	return $user->isAdministrator() || $user->getSystemPermission('use_time_reports');
    } // canView
    
    /**
     * Returns true if $user can cerate new time report
     *
     * @param User $user
     * @return boolean
     */
    function canAdd($user) {
    	return $user->isAdministrator() || ($user->getSystemPermission('use_time_reports') && $user->getSystemPermission('manage_time_reports'));
    } // canAdd
    
    /**
     * Returns true if $user can update existing time report
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
    	return $user->isAdministrator() || ($user->getSystemPermission('use_time_reports') && $user->getSystemPermission('manage_time_reports'));
    } // canEdit
    
    /**
     * Returns true if $user can delete this time report
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      if($this->getIsDefault()) {
        return false; // Default time report cannot be deleted
      } // if
    	return $user->isAdministrator() || ($user->getSystemPermission('use_time_reports') && $user->getSystemPermission('manage_time_reports'));
    } // canDelete
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return user filter value
     *
     * @param void
     * @return string
     */
    function getUserFilterData() {
      $raw = parent::getUserFilterData();
    	return empty($raw) ? null : unserialize($raw);
    } // getUserFilterData
    
    /**
     * Set user filter data
     *
     * @param mixed $value
     * @return mixed
     */
    function setUserFilterData($value) {
    	return parent::setUserFilterData(serialize($value));
    } // setUserFilterData
  
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return report URL
     *
     * @param Project $project
     * @return string
     */
    function getUrl($project = null) {
      if($project === null) {
        return assemble_url('global_time_report', array(
      	  'report_id' => $this->getId(),
      	));
      } else {
        return assemble_url('project_time_report', array(
          'project_id' => $project->getId(),
      	  'report_id' => $this->getId(),
      	));
      } // if
    } // getUrl
    
    /**
     * Return export URL
     *
     * @param Project $project
     * @return string
     */
    function getExportUrl($project = null) {
      if($project === null) {
        return assemble_url('global_time_report_export', array(
      	  'report_id' => $this->getId(),
      	));
      } else {
        return assemble_url('project_time_report_export', array(
          'project_id' => $project->getId(),
      	  'report_id' => $this->getId(),
      	));
      } // if
    } // getExportUrl
    
    /**
     * Return edit report URL
     *
     * @param Project $project
     * @return string
     */
    function getEditUrl($project = null) {
      $params = array('report_id' => $this->getId());
      if($project !== null) {
        $params['project_id'] = $project->getId();
      } // if
      
    	return assemble_url('global_time_report_edit', $params);
    } // getEditUrl
    
    /**
     * Return delete report URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
    	return assemble_url('global_time_report_delete', array(
    	  'report_id' => $this->getId(),
    	));
    } // getDeleteUrl
  
  }

?>