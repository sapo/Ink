<?php

  /**
   * Ticket manager class
   *
   * @package activeCollab.modules.tickets
   * @subpackage models
   */
  class Tickets extends ProjectObjects {
    
    /**
     * Return ticket by ticket ID
     *
     * @param Project $project
     * @param integer $id
     * @return Ticket
     */
    function findByTicketId($project, $id) {
      return Tickets::find(array(
        'conditions' => array('project_id = ? AND integer_field_1 = ? AND type = ?', $project->getId(), $id, 'Ticket'),
        'one' => true,
      ));
    } // findByTicketId
    
    /**
     * Return open tickets by project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findOpenByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $project->getId(), 'Ticket', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position, priority DESC',
      ));
    } // findOpenByProject
    
    /**
     * Return completed tickets by project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findCompletedByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Tickets::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NOT NULL', $project->getId(), 'Ticket', $min_state, $min_visibility),
        'order' => 'completed_on DESC'
      ));
    } // findCompletedByProject
    
    /**
     * Return open tickets by category
     *
     * @param Category $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findOpenByCategory($category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('parent_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $category->getId(), 'Ticket', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position, priority DESC',
      ));
    } // findOpenByCategory
    
    /**
     * Return all tickets by a given milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findByMilestone($milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('milestone_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), 'Ticket', $min_state, $min_visibility),
        'order' => 'priority DESC',
      ));
    } // findByMilestone
    
    /**
     * Return ID for next ticket
     * 
     * $project can be an instance of Project class or project_id
     *
     * @param Project $project
     * @return integer
     */
    function findNextTicketIdByProject($project) {
      $project_id = instance_of($project, 'Project') ? $project->getId() : (integer) $project;
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      $row = db_execute_one("SELECT MAX(integer_field_1) AS 'max_id' FROM $project_objects_table WHERE project_id = ? AND type = ?", $project_id, 'Ticket');
      if(is_array($row)) {
        return $row['max_id'] + 1;
      } else {
        return 1;
      } // if
    } // findNextTicketIdByProject
    
    /**
     * Paginate complete tickets by project
     *
     * @param Project $project
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return null
     */
    function paginateCompletedByProject($project, $page = 1, $per_page = 10, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Tickets::paginate(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NOT NULL', $project->getId(), 'Ticket', $min_state, $min_visibility),
        'order' => 'completed_on DESC'
      ), $page, $per_page);
    } // paginateCompletedByProject
    
    /**
     * Paginate complete tickets by category
     *
     * @param Category $category
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return null
     */
    function paginateCompletedByCategory($category, $page = 1, $per_page = 10, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Tickets::paginate(array(
        'conditions' => array('parent_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NOT NULL', $category->getId(), 'Ticket', $min_state, $min_visibility),
        'order' => 'completed_on DESC'
      ), $page, $per_page);
    } // paginateCompletedByCategory
    
    
    /**
     * return all tickets in current project. If category is specified it returns all tickets by category
     * 
     * @param Project $project
     * @param Category $category
     * @param integer $min_state
     * @param integer $min_visibility
     * 
     */
    function findByProject($project, $category = null, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      if ($category) {
        return Tickets::find(array(
          "conditions" => array('project_id = ? AND type = ? AND parent_id =? AND state >= ? AND visibility >= ?', $project->getId(), 'Ticket', $category->getId(), $min_state, $min_visibility),
          "order" => "priority DESC"
        ));
      } else {
        return Tickets::find(array(
          "conditions" => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Ticket', $min_state, $min_visibility),
          "order" => "priority DESC"
        ));
      }
    } // findByProject
    
  } // Tickets

?>