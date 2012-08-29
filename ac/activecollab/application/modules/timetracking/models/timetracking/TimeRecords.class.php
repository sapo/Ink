<?php

  /**
   * Time records manager class
   * 
   * @package activeCollab.modules.timetracking
   * @subpackage models
   */
  class TimeRecords extends ProjectObjects {
    
    /**
     * Return time records from a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'TimeRecord', $min_state, $min_visibility),
        'order' => 'date_field_1 DESC, id DESC',
      ));
    } // findByProject
    
    /**
     * Return paginated TimeRecords by project
     *
     * @param Project $project
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function paginateByProject($project, $page = 1, $per_page = 30, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::paginate(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'TimeRecord', $min_state, $min_visibility),
        'order' => 'date_field_1 DESC, id DESC',
      ), $page, $per_page);
    } // paginateByProject
    
    /**
     * Return TimeRecords by parent
     *
     * @param ProjectObject $parent
     * @param array $billable_status
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findByParent($parent, $billable_status = null, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      if (is_foreachable($billable_status)) {
        $conditions = array('parent_id = ? AND type = ? AND state >= ? AND visibility >= ? and integer_field_2 IN (?)', $parent->getId(), 'TimeRecord', $min_state, $min_visibility, $billable_status);        
      } else {
        $conditions = array('parent_id = ? AND type = ? AND state >= ? AND visibility >= ?', $parent->getId(), 'TimeRecord', $min_state, $min_visibility);
      } // if
      return ProjectObjects::find(array(
        'conditions' => $conditions,
        'order' => 'date_field_1 DESC, id DESC',
      ));
    } // findByParent
    
    /**
     * Return paginated TimeRecords by object
     *
     * @param ProjectObject $object
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function paginateByObject($object, $page = 1, $per_page = 30, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::paginate(array(
        'conditions' => array('parent_id = ? AND type = ? AND state >= ? AND visibility >= ?', $object->getId(), 'TimeRecord', $min_state, $min_visibility),
        'order' => 'date_field_1 DESC, id DESC',
      ), $page, $per_page);
    } // paginateByObject
    
    /**
     * Return full time for a given object
     *
     * @param ProjectObject $object
     * @return float
     */
    function sumObjectTime($object) {
      return (float) array_var(db_execute_one("SELECT SUM(float_field_1) AS 'time_sum' FROM " . TABLE_PREFIX . "project_objects WHERE parent_id = ? AND state >= ?", $object->getId(), STATE_VISIBLE), 'time_sum');
    } // sumObjectTime
    
    /**
     * Return number of hours tracked for tasks attached to $object
     *
     * @param ProjectObject $object
     * @return float
     */
    function sumTasksTime($object) {
      $rows = db_execute_all('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE parent_id = ? AND type = ?', $object->getId(), 'Task');
      if(is_foreachable($rows)) {
        $task_ids = array();
        foreach($rows as $row) {
          $task_ids[] = (integer) $row['id'];
        } // foreach
        return (float) array_var(db_execute_one("SELECT SUM(float_field_1) AS 'time_sum' FROM " . TABLE_PREFIX . "project_objects WHERE parent_id IN (?) AND state >= ?", $task_ids, STATE_VISIBLE), 'time_sum');
      } else {
        return 0;
      } // if
    } // sumTasksTime
    
    /**
     * Calculate time for time records
     * 
     * @param TimeRecords   $timerecords
     * @return float
     */    
    function calculateTime($timerecords) {
      if (!is_array($timerecords)) {
      	return null;
      } // if
      
      $total_time = 0;
  		foreach ($timerecords as $timerecord) {
   			$total_time += $timerecord->getValue();
    	} // if
    	
    	return $total_time;
    } // calculateTime
    
    /**
     * Find # of a timerecord in a project
     *
     * @param Timerecord $timerecord
     * @param integer $min_state
     * @param integer $min_visiblity
     * @return integer
     */
    function findTimerecordNum($timerecord, $min_state = STATE_VISIBLE, $min_visiblity = VISIBILITY_NORMAL) {
      return ProjectObjects::count(array("type = 'Timerecord' AND date_field_1 > ? AND project_id = ? AND state >= ? AND visibility >= ?", $timerecord->getRecordDate(), $timerecord->getProjectId(), $min_state, $min_visiblity)) + 1;
    } // findCommentNum
    
    /**
     * Get total time on project for user
     * @param Project $project
     * @param User $user
     * @return float
     */
    function getTotalUserTimeOnProject($project, $user) {
      return (float) array_var(db_execute_one("SELECT SUM(float_field_1) AS 'time_sum' FROM ".TABLE_PREFIX."project_objects WHERE project_id = ? AND integer_field_1 = ?", $project->getId(), $user->getId()), 'time_sum');
    } // getTotalUserTimeOnProject
    
  }

?>