<?php

  /**
   * AssignmentFilter class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class AssignmentFilter extends BaseAssignmentFilter {
    
    /**
     * Prepare conditions based on filter settings
     *
     * @param User $user
     * @return string
     */
    function prepareConditions($user) {
    	$project_objects_table = TABLE_PREFIX . 'project_objects';
      $assignments_table = TABLE_PREFIX . 'assignments';
      
      $completable_types = get_completable_project_object_types();
      
      $conditions = array();
      
      // Selected projects filter
      if($this->getProjectFilter() == PROJECT_FILTER_SELECTED) {
        $project_ids = $this->getProjectFilterData();
        if($project_ids) {
          $conditions[] = db_prepare_string("($project_objects_table.project_id IN (?))", array($project_ids));
          
          $types_filter = ProjectUsers::getVisibleTypesFilter($user, null, $completable_types);
          if($types_filter) {
            $conditions[] = $types_filter;
          } else {
            return false; // No access to any of the projects?
          } // if
        } // if
      } // if
      
      // All projects
      if(count($conditions) == 0) {
        $types_filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE), $completable_types);
        if($types_filter) {
          $conditions[] = $types_filter;
        } else {
          return false; // No access to any of the projects?
        } // if
      } // if
      
      // User filter
      switch($this->getUserFilter()) {
        
        // Not assigned to anyone
        case USER_FILTER_NOT_ASSIGNED:
          $user_id = $user->getId();
          $conditions[] = "($assignments_table.user_id IS NULL)";
          break;
        
        // Logged user
        case USER_FILTER_LOGGED_USER:
          $user_id = $user->getId();
          $conditions[] = "($assignments_table.user_id = $user_id)";
          break;
          
        // Logged user is responsible
        case USER_FILTER_LOGGED_USER_RESPONSIBLE:
          $user_id = $user->getId();
          $conditions[] = "($assignments_table.user_id = $user_id) AND ($assignments_table.is_owner = 1)";
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
                  $conditions[] = "($assignments_table.user_id IN ($imploded))";
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
              $conditions[] = "($assignments_table.user_id IN ($imploded))";
            } else {
              return false;
            } // if
          } // if
          break;
      } // switch
      
      $today = new DateValue(time() + get_user_gmt_offset($user)); // Calculate user timezone when determining today
      switch($this->getDateFilter()) {
        
        // List late assignments
        case DATE_FILTER_LATE:
          $today_str = db_escape($today->toMySQL());
          $conditions[] = "($project_objects_table.due_on < $today_str)";
          break;
          
        // List today assignments
        case DATE_FILTER_TODAY:
          $today_str = db_escape($today->toMySQL());
          $conditions[] = "($project_objects_table.due_on = $today_str)";
          break;
          
        // List tomorrow assignments
        case DATE_FILTER_TOMORROW:
          $tomorrow = $today->advance(86400, false);
          $tomorrow_str = db_escape($tomorrow->toMySQL());
          $conditions[] = "($project_objects_table.due_on = $tomorrow_str)";
          break;
          
        // List this week assignments
        case DATE_FILTER_THIS_WEEK:
          $first_day_sunday = UserConfigOptions::getValue('time_first_week_day', $user) == 0;
          
          $week_start = $today->beginningOfWeek($first_day_sunday);
          $week_end = $today->endOfWeek($first_day_sunday);
          
          $week_start_str = db_escape($week_start->toMySQL());
          $week_end_str = db_escape($week_end->toMySQL());
          
          $conditions[] = "($project_objects_table.due_on >= $week_start_str AND $project_objects_table.due_on <= $week_end_str)";
          break;
          
        // List next week assignments
        case DATE_FILTER_NEXT_WEEK:
          $first_day_sunday = UserConfigOptions::getValue('time_first_week_day', $user) == 0;
          
          $next_week = $today->advance(604800, false);
          
          $week_start = $next_week->beginningOfWeek($first_day_sunday);
          $week_end = $next_week->endOfWeek($first_day_sunday);
          
          $week_start_str = db_escape($week_start->toMySQL());
          $week_end_str = db_escape($week_end->toMySQL());
          
          $conditions[] = "($project_objects_table.due_on >= $week_start_str AND $project_objects_table.due_on <= $week_end_str)";
          break;
          
        // List this month assignments
        case DATE_FILTER_THIS_MONTH:
          $month_start = DateTimeValue::beginningOfMonth($today->getMonth(), $today->getYear());
          $month_end = DateTimeValue::endOfMonth($today->getMonth(), $today->getYear());
          
          $month_start_str = db_escape($month_start->toMySQL());
          $month_end_str = db_escape($month_end->toMySQL());
          
          $conditions[] = "($project_objects_table.due_on >= $month_start_str AND $project_objects_table.due_on <= $month_end_str)";
          break;
          
        // List next month assignments
        case DATE_FILTER_NEXT_MONTH:
          $month = $today->getMonth() + 1;
          $year = $today->getYear();
          
          if($month == 13) {
            $month = 1;
            $year += 1;
          } // if
          
          $month_start = DateTimeValue::beginningOfMonth($month, $year);
          $month_end = DateTimeValue::endOfMonth($month, $year);
          
          $month_start_str = db_escape($month_start->toMySQL());
          $month_end_str = db_escape($month_end->toMySQL());
          
          $conditions[] = "($project_objects_table.due_on >= $month_start_str AND $project_objects_table.due_on <= $month_end_str)";
          break;
          
        // Specific date
        case DATE_FILTER_SELECTED_DATE:
          $date_from = $this->getDateFrom();
          if(instance_of($date_from, 'DateTimeValue')) {
            $date_from_str = db_escape($date_from->toMySql());
            $conditions[] = "($project_objects_table.due_on = $date_from_str)";
          } // if
          break;
          
        // Specific range
        case DATE_FILTER_SELECTED_RANGE:
          $date_from = $this->getDateFrom();
          $date_to = $this->getDateTo();
          
          if(instance_of($date_from, 'DateValue') && instance_of($date_to, 'DateValue')) {
            $date_from_str = db_escape($date_from->toMySQL());
            $date_to_str = db_escape($date_to->toMySQL());
            
            $conditions[] = "($project_objects_table.due_on >= $date_from_str AND $project_objects_table.due_on <= $date_to_str)";
          } // if
          break;
      } // switch
      
      // Status filter
      switch($this->getStatusFilter()) {
        case STATUS_FILTER_ACTIVE:
          $conditions[] = "($project_objects_table.completed_on IS NULL)";
          break;
        case STATUS_FILTER_COMPLETED:
          $conditions[] = "($project_objects_table.completed_on IS NOT NULL)";
          break;
      } // if
      
      // Additional filters
      $state = STATE_VISIBLE;
      $visibility = $user->getVisibility();
      
      $conditions[] = "($project_objects_table.state >= $state AND $project_objects_table.visibility >= $visibility)";
      
      return implode(' AND ', $conditions);
    } // prepareConditions
    
    /**
     * Check if this filter is default assignment filter
     *
     * @param User $user
     * @return boolean
     */
    function isDefault() {
      return $this->getId() == ConfigOptions::getValue('default_assignments_filter');
    } // isDefault
    
    /**
     * Returns true if there are users who use this filter as default filter
     * 
     * Person who created this filter is not calculated when users are counted!
     *
     * @return boolean
     */
    function hasUsers() {
    	return UserConfigOptions::countByValue('default_assignments_filter', $this->getId(), $this->getCreatedById());
    } // hasUsers
    
    /**
     * Return number of objects displayed per page
     *
     * @param void
     * @return integer
     */
    function getObjectsPerPage() {
      $value = parent::getObjectsPerPage();
      return $value > 0 ? $value : 30;
    } // getObjectsPerPage
    
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
        } // switch
      } // if
      
      return $this->verbose_user_filter_data;
    } // getVerboseUserFilterData
    
    /**
     * Return verbose order by
     *
     * @param void
     * @return string
     */
    function getVerboseOrderBy() {
      if($this->orderedByPriority()) {
        return $this->orderDescending() ? lang('Priority, Highest First') : lang('Priority, Lowest First');
      } elseif($this->orderedByDueDate()) {
        return $this->orderDescending() ? lang('Due date, Late First') : lang('Due date, Late at the End');
      } else {
        return $this->orderDescending() ? lang('Creation Time, Newest First') : lang('Creation Time, Oldest First');
      } // if
    } // getVerboseOrderBy
    
    /**
     * Cached verbose project filter data
     *
     * @var string
     */
    var $verbose_project_filter_data = false;
    
    /**
     * Return verbose project filter data
     *
     * @param void
     * @return string
     */
    function getVerboseProjectFilterData() {
    	if($this->verbose_project_filter_data === false) {
    	  if($this->getProjectFilter() == PROJECT_FILTER_SELECTED) {
    	    $project_ids = $this->getProjectFilterData();
    	    
    	    $rows = db_execute_all('SELECT DISTINCT id, name FROM ' . TABLE_PREFIX . 'projects WHERE id IN (?)', $project_ids);
    	    if(is_foreachable($rows)) {
    	      $names = array();
    	      foreach($rows as $row) {
    	        $names[] = $row['name'];
    	      } // foreach
    	      require_once SMARTY_PATH . '/plugins/function.join.php';
    	      $this->verbose_project_filter_data = smarty_function_join(array('items' => $names));
    	    } // if
    	  } // if
    	  
    	  if(empty($this->verbose_project_filter_data)) {
    	    $this->verbose_project_filter_data = null;
    	  } // if
    	} // if
    	return $this->verbose_project_filter_data;
    } // getVerboseProjectFilterData
    
    /**
     * Returns true if this filter result is ordered descending
     *
     * @param void
     * @return boolean
     */
    function orderDescending() {
    	return strpos($this->getOrderBy(), 'DESC') !== false;
    } // orderDescending
    
    /**
     * Returns true if result is ordered by priority
     *
     * @param void
     * @return boolean
     */
    function orderedByPriority() {
    	return str_starts_with($this->getOrderBy(), 'priority');
    } // orderedByPriority
    
    /**
     * Returns true if result is ordered by due_date
     *
     * @param void
     * @return boolean
     */
    function orderedByDueDate() {
    	return str_starts_with($this->getOrderBy(), 'due_on');
    } // orderedByDueDate
    
    /**
     * Returns true if result is ordered by created_on
     *
     * @param void
     * @return boolean
     */
    function orderedByCreatedOn() {
    	return str_starts_with($this->getOrderBy(), 'created_on');
    } // orderedByCreatedOn
    
    /**
     * Set object attributes
     *
     * @param array $attributes
     * @return null
     */
    function setAttributes($attributes) {
      if(array_key_exists('user_filter_data', $attributes)) {
        $this->setUserFilterData(array_var($attributes, 'user_filter_data', null, true));
      } // if
      
      if(array_key_exists('project_filter_data', $attributes)) {
        $this->setProjectFilterData(array_var($attributes, 'project_filter_data', null, true));
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can use this filter
     *
     * @param User $user
     * @return boolean
     */
    function canUse($user) {
    	if($this->getIsPrivate()) {
    	  return $this->getCreatedById() == $user->getId();
    	} // if
    	return true;
    } // canUse
    
    /**
     * Returns true if $user can create new filters
     *
     * @param User $user
     * @return boolean
     */
    function canAdd($user) {
      return $user->getSystemPermission('manage_assignment_filters');
    } // canAdd
    
    /**
     * Check if given user can edit this filter
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
      if($user->getSystemPermission('manage_assignment_filters')) {
        if($this->getIsPrivate()) {
          return $user->isAdministrator() || ($this->getCreatedById() == $user->getId());
        } // if
      	return true;
      } // if
      
      return false;
    } // canEdit
    
    /**
     * Returns true if this filter can be marked as private
     *
     * @return boolean
     */
    function canBeMarkedAsPrivate() {
      return !$this->isDefault() && !$this->hasUsers();
    } // canBeMarkedAsPrivate
    
    /**
     * Check if user can delete this filter
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      if($user->getSystemPermission('manage_assignment_filters')) {
        if(ConfigOptions::getValue('default_assignments_filter') == $this->getId()) {
          return false; // default filter cannot be deleted
        } // if
        
        if($this->getIsPrivate()) {
          return $this->getCreatedById() == $user->getId();
        } // if
      	return $user->isAdministrator();
      } else {
        return false;
      } // if
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return filter URL
     *
     * @param integer $page
     * @return string
     */
    function getUrl($page = null) {
      $params = array('filter_id' => $this->getId());
      if($page) {
        $params['page'] = $page;
      } // if
      
      return assemble_url('assignments_filter', $params);
    } // getUrl
    
    /**
     * View filter RSS URL
     *
     * @param User $user
     * @return string
     */
    function getRssUrl($user) {
      return assemble_url('assignments_filter_rss', array(
        'token' => $user->getToken(true),
        'filter_id' => $this->getId()
      ));
    } // getRssUrl
    
    /**
     * View edit filter URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('assignments_filter_edit', array('filter_id' => $this->getId()));
    } // getEditUrl
    
    /**
     * View delete filter URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('assignments_filter_delete', array('filter_id' => $this->getId()));
    } // getDeleteUrl
    
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
    
    /**
     * Return project filter data
     *
     * @param void
     * @return mixed
     */
    function getProjectFilterData() {
    	$raw = parent::getProjectFilterData();
    	if($raw) {
    	  $data = (array) unserialize($raw);
    	  foreach($data as $k => $v) {
    	    if(empty($v)) {
    	      unset($data[$k]);
    	    } // if
    	  } // foreach
    	  
    	  return count($data) ? $data : null;
    	} // if
    	
    	return null;
    } // getProjectFilterData
    
    /**
     * Set project filter data
     *
     * @param mixed $value
     * @return mixed
     */
    function setProjectFilterData($value) {
    	return parent::setProjectFilterData(serialize($value));
    } // setProjectFilterData
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name', 'is_private')) {
          $errors->addError(lang('Filter name needs to be unique'), 'name');
        } // if
      } else {
        $errors->addError(lang('Field name is required'), 'name');
      } // if
      
      if($this->getIsPrivate()) {
        if($this->isDefault()) {
          $errors->addError(lang("Default filter can't be marked as private"), 'is_private'); // Globally default?
        } elseif($this->hasUsers()) {
          $errors->addError(lang("Filter already used as default by other users can't be marked as private"), 'is_private'); // Not global default, but default for somone
        } // if
      } // if
    } // validate
  
  }

?>