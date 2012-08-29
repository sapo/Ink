<?php

  /**
   * ActivityLogs class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ActivityLogs extends BaseActivityLogs {
    
    /**
     * Return recent project activities by user
     *
     * @param Project $project
     * @param User $user
     * @param integer $count
     * @return array
     */
    function findProjectActivitiesByUser($project, $user, $count = 30) {
      $type_filter = ProjectUsers::getVisibleTypesfilterByProject($user, $project);
      if($type_filter) {
        $objects_table = TABLE_PREFIX . 'project_objects';
        $logs_table = TABLE_PREFIX . 'activity_logs';
        
        $count = (integer) $count;
        if($count < 1) {
          $count = 30;
        } // if
        
        return ActivityLogs::findBySQL("SELECT $logs_table.* FROM $logs_table, $objects_table WHERE $logs_table.object_id = $objects_table.id AND $type_filter AND $objects_table.state >= ? AND $objects_table.visibility >= ? ORDER BY $logs_table.created_on DESC LIMIT 0, $count", array(STATE_DELETED, $user->getVisibility()));
      } else {
        return null;
      } // if
    } // findProjectActivitiesByUser
    
    /**
     * Return activities of all active projects by user
     *
     * @param User $user
     * @param integer $count
     * @return array
     */
    function findActiveProjectsActivitiesByUser($user, $count = 30) {
    	$type_filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE));
    	if($type_filter) {
    	  $objects_table = TABLE_PREFIX . 'project_objects';
        $logs_table = TABLE_PREFIX . 'activity_logs';
        
        $count = (integer) $count;
        if($count < 1) {
          $count = 30;
        } // if
        
        return ActivityLogs::findBySQL("SELECT $logs_table.* FROM $logs_table, $objects_table WHERE $logs_table.object_id = $objects_table.id AND $type_filter AND $objects_table.state >= ? AND $objects_table.visibility >= ? ORDER BY $logs_table.created_on DESC LIMIT 0, $count", array(STATE_DELETED, $user->getVisibility()));
    	} else {
    	  return null;
    	} // if
    } // findActiveProjectsActivitiesByUser
    
    /**
     * Return recent activities for selected user of all active projects
     *
     * @param User $user
     * @param integer $count
     * @return array
     */
    function paginateActivitiesByUser($user, $page = 1, $per_page = 30) {
  	  $objects_table = TABLE_PREFIX . 'project_objects';
      $logs_table = TABLE_PREFIX . 'activity_logs';
      
      $total_activities = array_var(db_execute_one("SELECT COUNT($logs_table.id) AS 'row_count' FROM $logs_table, $objects_table WHERE $logs_table.object_id = $objects_table.id AND $logs_table.created_by_id = ?", $user->getId()), 'row_count');
      if($total_activities) {
      	$offset = ($page - 1) * $per_page;
      	
      	$activities = ActivityLogs::findBySQL("SELECT $logs_table.* FROM $logs_table, $objects_table WHERE $logs_table.object_id = $objects_table.id AND $logs_table.created_by_id = ? ORDER BY $logs_table.created_on DESC LIMIT $offset, $per_page", array($user->getId()));
      } else {
      	$activities = null;
      } // if
      
      return array($activities, new Pager($page, $total_activities, $per_page));
    } // paginateActivitiesByUser
    
    /**
     * Delete activity log entries by object
     *
     * @param ProjectObject $object
     * @return boolean
     */
    function deleteByObject($object) {
      return ActivityLogs::delete(array('object_id = ?', $object->getId()));
    } // deleteByObject
    
    /**
     * Clean activity log by project
     *
     * @param Project $project
     * @return boolean
     */
    function deleteByProject($project) {
    	return ActivityLogs::delete(array('project_id = ?', $project->getId()));
    } // deleteByProject
    
    /**
     * Delete activity log entries by object ID-s
     *
     * @param array $ids
     * @return boolean
     */
    function deleteByObjectIds($ids) {
      return ActivityLogs::delete(array('object_id IN (?)', $ids));
    } // deleteByObjectIds
    
    /**
     * Update cached project ID value
     *
     * @param ProjectObject $object
     * @return boolean
     */
    function updateProjectIdCache($object) {
      return db_execute('UPDATE ' . TABLE_PREFIX . 'activity_logs SET project_id = ? WHERE object_id = ?', $object->getProjectId(), $object->getId());
    } // updateProjectIdCache
  
    /**
     * Write Activity Log
     *
     * @param ProjectObject $object
     * @param User $user
     * @param string $action
     * @param string $comment
     * @return null
     */
    function write($object, $user, $action, $comment = null) {
      if(!instance_of($user, 'User') && !instance_of($user, 'AnonymousUser')) {
        $user =& get_logged_user();
        if(!instance_of($user, 'User')) {
          return false;
        } // if
      } // if
      
    	$activity_log = new ActivityLog();
    	$activity_log->setAttributes(array(
    	  'object_id'  => $object->getId(),
    	  'project_id' => $object->getProjectId(),
    	  'action'     => $action,
    	  'comment'    => $comment
    	));
    	$activity_log->setCreatedBy($user);
      
    	return $activity_log->save();    	
    } // write
    
    // ---------------------------------------------------
    //  Override BaseActivityLogs methods
    // ---------------------------------------------------
    
    /**
     * Do a SELECT query over database with specified arguments
     * 
     * This function can return single instance or array of instances that match 
     * requirements provided in $arguments associative array
     *
     * @param array $arguments
     * @return mixed
     * @throws DBQueryError
     */
    function find($arguments = null) {
      return ActivityLogs::findBySQL(DataManager::prepareSelectFromArguments($arguments, TABLE_PREFIX . 'activity_logs'), null, array_var($arguments, 'one'));
    } // find
    
    /**
     * Return paginated set of activity logs
     *
     * @param array $arguments
     * @param itneger $page
     * @param integer $per_page
     * @return array
     */
    function paginate($arguments = null, $page = 1, $per_page = 10) {
      if(!is_array($arguments)) {
        $arguments = array();
      } // if
      
      $arguments['limit'] = $per_page;
      $arguments['offset'] = ($page - 1) * $per_page;
      
      $items = ActivityLogs::findBySQL(DataManager::prepareSelectFromArguments($arguments, TABLE_PREFIX . 'activity_logs'), null, array_var($arguments, 'one'));
      $total_items = ActivityLogs::count(array_var($arguments, 'conditions'));
      
      return array(
        $items,
        new Pager($page, $total_items, $per_page)
      );
    } // paginate
    
    /**
     * Return object of a specific class by SQL
     *
     * @param string $sql
     * @param array $arguments
     * @param boolean $one
     * @param string $table_name
     * @return array
     */
    function findBySQL($sql, $arguments = null, $one = false) {
      if($arguments !== null) {
        $sql = db_prepare_string($sql, $arguments);
      } // if
      
      $rows = db_execute_all($sql);
      
      if(is_error($rows)) {
        return $rows;
      } // if
      
      if(!is_foreachable($rows)) {
        return null;
      } // if
      
      if($one) {
        $row = $rows[0];
        $log_class = array_var($row, 'type');
        
        $log = new $log_class();
        $log->loadFromRow($row);
        return $log;
      } else {
        $logs = array();
        
        foreach($rows as $row) {
          $log_class = array_var($row, 'type');
          
          $log = new $log_class();
          $log->loadFromRow($row);
          $logs[] = $log;
        } // foreach
        
        return count($logs) ? $logs : null;
      } // if
    } // findBySQL
  
    /**
     * Find and return a specific activity log by ID
     *
     * @param mixed $id
     * @return ProjectObject
     */
    function findById($id) {
      if(empty($id)) {
        return null;
      } // if
      
      $cache_id = TABLE_PREFIX . 'activity_logs_id_' . $id;
      $row = cache_get($cache_id);
      
      if($row) {
        $log_class = $row['type'];
        
        $log = new $log_class();
        $log->loadFromRow($row);
        
        return $log;
      } else {
        $row = db_execute_one("SELECT * FROM " . TABLE_PREFIX . "activity_logs WHERE id = ? LIMIT 0, 1", $id);
      
        if(is_array($row)) {
          $log_class = $row['type'];
          
          $log = new $log_class();
          $log->loadFromRow($row, true);
          
          return $log;
        } // if
      } // if
      
      return null;
    } // findById
    
    // ---------------------------------------------------
    //  Portal public methods
    // ---------------------------------------------------
    
    /**
     * Return portal project recent activities
     *
     * @param Portal $portal
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginatePortalProjectRecentActivities($portal, $page = 1, $per_page = 30) {
    	$type_filter = Portal::getVisibleTypesFilterByPortalProject($portal);
    	if($type_filter) {
	    	$objects_table = TABLE_PREFIX . 'project_objects';
	    	$logs_table = TABLE_PREFIX . 'activity_logs';
	    	
	    	$count = array_var(db_execute_one("SELECT COUNT($logs_table.id) AS 'row_count' FROM $logs_table, $objects_table WHERE $logs_table.object_id = $objects_table.id AND $objects_table.project_id = ? AND $type_filter AND $objects_table.state >= ? AND $objects_table.visibility >= ?", $portal->getProjectId(), STATE_DELETED, VISIBILITY_NORMAL), 'row_count');
	    	if($count) {
	    		$offset = ($page - 1) * $per_page;
	    		$recent_activities = ActivityLogs::findBySQL("SELECT $logs_table.* FROM $logs_table, $objects_table WHERE $logs_table.object_id = $objects_table.id AND $objects_table.project_id = ? AND $type_filter AND $objects_table.state >= ? AND $objects_table.visibility >= ? ORDER BY $logs_table.created_on DESC LIMIT $offset, $per_page", array($portal->getProjectId(), STATE_DELETED, VISIBILITY_NORMAL));
	    	} else {
	    		$recent_activities = null;
	    	} // if
	    	
	    	return array($recent_activities, new Pager($page, $count, $per_page));
    	} else {
    		return null;
    	} // if
    } // paginatePortalProjectRecentActivities
    
    /**
     * Return portal project recent activities
     *
     * @param Portal $portal
     * @param integer $count
     * @return array
     */
    function findPortalProjectRecentActivities($portal, $count = 30) {
    	$type_filter = Portal::getVisibleTypesFilterByPortalProject($portal);
    	if($type_filter) {
    		$objects_table = TABLE_PREFIX . 'project_objects';
    		$logs_table = TABLE_PREFIX . 'activity_logs';
    		
    		$count = (integer) $count;
    		if($count < 1) {
    			$count = 30;
    		} // if
    		
    		$recent_activities = ActivityLogs::findBySQL("SELECT $logs_table.* FROM $logs_table, $objects_table WHERE $logs_table.object_id = $objects_table.id AND $type_filter AND $objects_table.state >= ? AND $objects_table.visibility >= ? ORDER BY $logs_table.created_on DESC LIMIT 0, $count", array(STATE_DELETED, VISIBILITY_NORMAL));
    		
    		return $recent_activities;
    	} else {
    		return null;
    	} // if
    } // findPortalProjectRecentActivities
  
  }

?>