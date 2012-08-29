<?php

  /**
   * Milestones manager class
   *
   * @package activeCollab.modules.milestones
   * @subpackage models
   */
  class Milestones extends ProjectObjects {
    
    /**
     * Return milestones by a given list of ID-s
     *
     * @param array $ids
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findByIds($ids, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('id IN (?) AND type = ? AND state >= ? AND visibility >= ?', $ids, 'Milestone', $min_state, $min_visibility),
        'order' => 'due_on',
      ));
    } // findByIds
    
    /**
     * Return all visible milestone by a project
     *
     * @param Project $project
     * @param integer $min_visibility
     * @return array
     */
    function findAllByProject($project, $min_visibility = VISIBILITY_NORMAL) {
    	return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Milestone', STATE_VISIBLE, $min_visibility),
        'order' => 'due_on',
      ));
    } // findAllByProject
  
    /**
     * Return all milestones for a given project
     *
     * @param Project $project
     * @param User $user
     * @return array
     */
    function findByProject($project, $user) {
      if($user->getProjectPermission('milestone', $project) >= PROJECT_PERMISSION_ACCESS) {
        return ProjectObjects::find(array(
          'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Milestone', STATE_VISIBLE, $user->getVisibility()),
          'order' => 'due_on',
        ));
      } // if
      return null;
    } // findByProject
    
    /**
     * Return all active milestones in a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findActiveByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $project->getId(), 'Milestone', $min_state, $min_visibility),
        'order'      => 'due_on'
      ));
    } // findActiveByProject
    
    /**
     * Return completed milestones by project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findCompletedByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NOT NULL', $project->getId(), 'Milestone', $min_state, $min_visibility),
        'order'      => 'due_on',
      ));
    } // findCompletedByProject
    
    /**
     * Find successive milestones by a given milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findSuccessiveByMilestone($milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Milestones::find(array(
        'conditions' => array('project_id = ? AND type = ? AND due_on >= ? AND state >= ? AND visibility >= ? AND id != ?', $milestone->getProjectId(), 'Milestone', $milestone->getDueOn(), $min_state, $min_visibility, $milestone->getId()),
        'order' => 'due_on',
      ));
    } // findSuccessiveByMilestone
    
    // ---------------------------------------------------
    //  Portal methods
    // ---------------------------------------------------
    
    /**
     * Return all milestones for a given portal project
     *
     * @param Portal $portal
     * @param Project $project
     * @return array
     */
    function findByPortalProject($portal, $project) {
    	if($portal->getProjectPermissionValue('milestone') >= PROJECT_PERMISSION_ACCESS) {
    		return ProjectObjects::find(array(
    			'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Milestone', STATE_VISIBLE, VISIBILITY_NORMAL),
    			'order'      => 'due_on'
    		));
    	} // if
    	return null;
    } // findByPortalProject
  
  } // Milestones

?>