<?php

  /**
   * Tasks manager class
   *
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class Tasks extends ProjectObjects {
  
    /**
     * Return all tasks that belong to a given object
     *
     * @param ProjectObject $object
     * @param integer $min_state
     * @return array
     */
    function findByObject($object, $min_state = STATE_VISIBLE) {
      return ProjectObjects::find(array(
        'conditions' => array('parent_id = ? AND type = ? AND state >= ?', $object->getId(), 'Task', $min_state),
        'order' => 'priority DESC, created_on'
      ));
    } // findByObject
    
    /**
     * Return number of tasks in a given object
     *
     * @param ProjectObject $object
     * @param integer $min_state
     * @return integer
     */
    function countByObject($object, $min_state = STATE_VISIBLE) {
      return ProjectObjects::count(array('parent_id = ? AND type = ? AND state >= ? ', $object->getId(), 'Task', $min_state));
    } // countByObject
    
    /**
     * Return open tasks that belong to a given object
     *
     * @param ProjectObject $object
     * @param integer $min_state
     * @return array
     */
    function findOpenByObject($object, $min_state = STATE_VISIBLE) {
      return ProjectObjects::find(array(
        'conditions' => array('parent_id = ? AND type = ? AND state >= ? AND completed_on IS NULL', $object->getId(), 'Task', $min_state),
        'order' => 'ISNULL(position) ASC, position, priority DESC, created_on'
      ));
    } // findOpenByObject
    
    /**
     * Return number of open tasks in a given object
     *
     * @param ProjectObject $object
     * @param integer $min_state
     * @return integer
     */
    function countOpenByObject($object, $min_state = STATE_VISIBLE) {
      return ProjectObjects::count(array('parent_id = ? AND type = ? AND state >= ? AND completed_on IS NULL', $object->getId(), 'Task', $min_state));
    } // countOpenByObject
    
    /**
     * Return only completed tasks that belong to a specific object
     *
     * @param ProjectObject $object
     * @param integer $limit
     * @param integer $min_state
     * @return array
     */
    function findCompletedByObject($object, $limit=NULL, $min_state = STATE_VISIBLE) {
      $conditions = array(
        'conditions' => array('parent_id = ? AND type = ? AND state >= ? AND completed_on IS NOT NULL', $object->getId(), 'Task', $min_state),
        'order' => 'completed_on DESC'
      );
      
      if ($limit !== null) {
       $conditions['limit'] = $limit;
      } // if
      return ProjectObjects::find($conditions);
    } // findCompletedByObject
    
    /**
     * Return number of completed tasks in a given object
     *
     * @param ProjectObject $object
     * @param integer $min_state
     * @return integer
     */
    function countCompletedByObject($object, $min_state = STATE_VISIBLE) {
      return ProjectObjects::count(array('parent_id = ? AND type = ? AND state >= ? AND completed_on IS NOT NULL', $object->getId(), 'Task', $min_state));
    } // countCompletedByObject
  
  } // Tasks

?>