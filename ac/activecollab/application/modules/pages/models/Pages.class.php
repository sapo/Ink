<?php

  /**
   * Pages manager
   * 
   * @package activeCollab.modules.pages
   * @subpackage models
   */
  class Pages extends ProjectObjects {
    
    /**
     * Return pages that belong to a specific milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findByMilestone($milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' =>  array('milestone_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), 'Page', $min_state, $min_visibility),
        'order' => 'updated_on DESC'
      ));
    } // findByMilestone
    
    /**
     * Paginate pages by project
     *
     * @param Project $project
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function paginateByProject($project, $page = 1, $per_page = 10, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::paginate(array(
        'conditions' =>  array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Page', $min_state, $min_visibility),
        'order' => 'updated_on DESC'
      ), $page, $per_page);
    } // paginateByProject
    
    /**
     * Load pages by $category
     *
     * @param Category $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findByCategory($category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' =>  array('parent_id = ? AND type = ? AND state >= ? AND visibility >= ?', $category->getId(), 'Page', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position'
      ));
    } // findByCategory
    
    /**
     * Return subpages
     *
     * @param Page $page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findSubpages($page, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Pages::find(array(
        'conditions' => array('parent_id = ? AND type = ? AND state >= ? AND visibility >= ?', $page->getId(), 'Page', $min_state, $min_visibility, false),
        'order' => 'ISNULL(position) ASC, position'
      ));
    } // findSubpages
  
  } // Pages

?>