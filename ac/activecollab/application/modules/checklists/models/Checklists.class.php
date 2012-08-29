<?php

  /**
   * Checklists manager
   *
   * @package activeCollab.modules.checklists
   * @subpackage models
   */
  class Checklists extends ProjectObjects {
  
    /**
     * Return all checklist for a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Checklist', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position, created_on',
      ));
    } // findByProject
    
    /**
     * Return all active checklists in a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findActiveByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $project->getId(), 'Checklist', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position, created_on',
      ));
    } // findActiveByProject
    
    /**
     * Find all checklists by milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findByMilestone($milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('milestone_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), 'Checklist', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position, created_on',
      ));
    } // findByMilestone
    
    /**
     * Return completed checklists by project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findCompletedByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NOT NULL', $project->getId(), 'Checklist', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position, created_on',
      ));
    } // findCompletedByProject
  
  } // Checklists

?>