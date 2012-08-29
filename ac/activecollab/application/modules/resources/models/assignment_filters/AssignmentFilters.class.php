<?php

  /**
   * AssignmentFilters class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class AssignmentFilters extends BaseAssignmentFilters {
  
    /**
     * Return all filters grouped by group name
     *
     * @param User $user
     * @param boolean $skip_private
     * @return array
     */
    function findGrouped($user = null, $skip_private = false) {
      $result = null;
      
      $conditions = null;
      if(instance_of($user, 'User')) {
        $conditions = array('is_private = ? OR (is_private = ? AND created_by_id = ?)', false, true, $user->getId());
      } elseif($skip_private) {
        $conditions = array('is_private = ?', false);
      } // if
      
      $all = AssignmentFilters::find(array(
        'conditions' => $conditions,
        'order' => 'group_name, name'
      ));
      
      if(is_foreachable($all)) {
        $result = array();
        $other_filters = array();
        
        foreach($all as $filter) {
          if($group_name = $filter->getGroupName()) {
            if(!isset($result[$group_name])) {
              $result[$group_name] = array();
            } // if
            $result[$group_name][] = $filter;
          } else {
            $other_filters[] = $filter;
          } // if
        } // foreach
        
        if(count($other_filters)) {
          $result[lang('Other')] = $other_filters;
        }
      } // if
      
      return $result;
    } // findGrouped
    
    /**
     * Execute specific filter
     * 
     * Possible $paginate values:
     * 
     * - NULL - base pagination on filter settings
     * - true - force pagination with given params ($page and $per_page)
     * - false - don't paginate
     *
     * @param User $user
     * @param AssignmentFilter $filter
     * @param mixed $paginate
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function executeFilter($user, $filter, $paginate = null, $page = null, $per_page = null) {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $assignments_table = TABLE_PREFIX . 'assignments';
      
      $conditions = $filter->prepareConditions($user);
      if(empty($conditions)) {
        if($paginate === null) {
          return $filter->getObjectsPerPage() ? array(null, new Pager(1, 0, $filter->getObjectsPerPage())) : null;
        } elseif($paginate) {
          return array(null, new Pager(1, 0, $filter->getObjectsPerPage()));
        } else {
          return null;
        } // if
      } // if
      
      $order_by = '';
      if($filter->orderedByDueDate()) {
        $order_by = $filter->orderDescending() ? 'ORDER BY ISNULL(due_on), due_on DESC, priority DESC' : 'ORDER BY ISNULL(due_on), due_on, priority DESC';
      } elseif($filter->getOrderBy()) {
        $order_by = 'ORDER BY ' . $filter->getOrderBy();
      } // if
      
      $total = 0;
      $object_ids = array();
      
      // Lets get object ID-s
      $rows = db_execute_all("SELECT DISTINCT $project_objects_table.id FROM $project_objects_table LEFT JOIN $assignments_table ON $project_objects_table.id = $assignments_table.object_id WHERE $conditions");
  	  if(is_foreachable($rows)) {
  	    $total = count($rows);
  	    foreach($rows as $row) {
  	      $object_ids[] = (integer) $row['id'];
  	    } // foreach
  	  } // if
      
      // Use filter pagination settings
    	if($paginate === null || $paginate === true) {
    	  if($total) {
    	    $per_page = $paginate === null ? (integer) $filter->getObjectsPerPage() : (integer) $per_page;
    	    if($per_page < 1) {
    	      $per_page = 30;
    	    } // if
    	    
    	    $page = (integer) $page;
    	    if($page < 1) {
    	      $page = 1;
    	    } // if
    	    
    	    $offset = ($page - 1) * $per_page;
    	    $limit = "LIMIT $offset, $per_page"; 
    	    
    	    return array(
    	      ProjectObjects::findBySQL("SELECT * FROM $project_objects_table WHERE id IN (?) $order_by $limit", array($object_ids)), 
    	      new Pager($page, $total, $per_page)
    	    );
    	  } else {
    	    return array(null, new Pager(1, 0, $filter->getObjectsPerPage()));
    	  } // if
    	  
    	// Don't paginate
    	} elseif($paginate === false) {
    	  if($total) {
    	    return ProjectObjects::findBySQL("SELECT $project_objects_table.* FROM $project_objects_table WHERE id IN (?) $order_by", array($object_ids));
    	  } else {
    	    return null;
    	  } // if
    	} // if
    } // executeFilter
    
    /**
     * Return default filter
     *
     * @param void
     * @return AssignmentFilter
     */
    function findDefault() {
      
    } // findDefault
    
    /**
     * Drop all private filtes for a given user
     *
     * @param $user User
     * @return boolean
     */
    function cleanByUser($user) {
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'assignment_filters WHERE created_by_id = ? AND is_private = ?', $user->getId(), true);
    } // cleanByUser
  
  }

?>