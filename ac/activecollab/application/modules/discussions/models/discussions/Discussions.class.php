<?php

  /**
   * Discussions manager class
   *
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class Discussions extends ProjectObjects {
    
    /**
     * Return discussions posted in a specific category
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    function findByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL, $offset = null, $limit = null) {
      return Discussions::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Discussion', $min_state, $min_visibility),
        'order'      => 'boolean_field_1 DESC, datetime_field_1 DESC',
        'offset'     => $offset,
        'limit'      => $limit,
      ));
    } // findByProject
    
    /**
     * Return discussions posted in a specific category in projects
     *
     * @param array $project_ids
     * @param integer $min_state
     * @param integer $min_visibility
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    function findByProjectIds($project_ids, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL, $offset = null, $limit = null) {
      return Discussions::find(array(
        'conditions' => array('project_id IN (?) AND type = ? AND state >= ? AND visibility >= ?', $project_ids, 'Discussion', $min_state, $min_visibility),
        'order'      => 'boolean_field_1, datetime_field_1 DESC',
        'offset'     => $offset,
        'limit'      => $limit,
      ));
    } // findByProjectIds
    
    /**
     * Return discussions by milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findByMilestone($milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Discussions::find(array(
        'conditions' => array('milestone_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), 'Discussion', $min_state, $min_visibility),
        'order'      => 'boolean_field_1 DESC, datetime_field_1 DESC',
      ));
    } // findOpenByMilestone
    
    /**
     * Return paginated discussions by project
     * 
     * Discussions are ordered by IS_PINNED flag and time of last reply
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
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Discussion', $min_state, $min_visibility),
        'order' => 'boolean_field_1 DESC, datetime_field_1 DESC',
      ), $page, $per_page);
    } // paginateByProject
    
    /**
     * Return paginated discussions by project ids
     * 
     * Discussions are ordered by IS_PINNED flag and time of last reply
     *
     * @param array $project_ids
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function paginateByProjectIds($project_ids, $page = 1, $per_page = 30, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::paginate(array(
        'conditions' => array('project_id IN (?) AND type = ? AND state >= ? AND visibility >= ?', $project_ids, 'Discussion', $min_state, $min_visibility),
        'order' => 'boolean_field_1, datetime_field_1 DESC',
      ), $page, $per_page);
    } // paginateByProjectIds
    
    /**
     * Return paginated discussions by Category
     *
     * @param Category $category
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function paginateByCategory($category, $page = 1, $per_page = 30, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::paginate(array(
        'conditions' => array('parent_id = ? AND type = ? AND state >= ? AND visibility >= ?', $category->getId(), 'Discussion', $min_state, $min_visibility),
        'order' => 'boolean_field_1 DESC, datetime_field_1 DESC',
      ), $page, $per_page);
    } // paginateByCategory
    
  } // Discussions

?>