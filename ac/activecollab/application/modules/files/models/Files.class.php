<?php

  /**
   * File manager class
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class Files extends ProjectObjects {
    
    /**
     * Find file revisions
     *
     * @param File $file
     * @return array
     */
    function findRevisions($file) {
      return Attachments::find(array(
        'conditions' => array('parent_id = ? AND parent_type = ?', $file->getId(), 'File'),
        'order' => 'created_on DESC'
      ));
    } // findRevisions
    
    /**
     * Return file revisions
     *
     * @param File $file
     * @return integer
     */
    function countRevisions($file) {
      return Attachments::count(array('parent_id = ? AND parent_type = ?', $file->getId(), 'File'));
    } // countRevisions
    
    /**
    * Return files by $project
    *
    * @param Project $project
    * @param integer $min_state
    * @param integer $min_visibility
    * @return array
    */
    function findByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Files::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'File', $min_state, $min_visibility),
        'order' => 'created_on DESC',
      ));
    } // findByProject
    
    /**
    * Paginate project files
    *
    * @param Project $project
    * @param integer $page
    * @param integer $per_page
    * @param integer $min_state
    * @param integer $min_visibility
    * @return array
    */
    function paginateByProject($project, $page = 1, $per_page = 30, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Files::paginate(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'File', $min_state, $min_visibility),
        'order' => 'created_on DESC'
      ), $page, $per_page);
    } // paginateByProject
    
    /**
    * Paginate projects files
    *
    * @param array   $project_ids
    * @param integer $page
    * @param integer $per_page
    * @param integer $min_state
    * @param integer $min_visibility
    * @return array
    */
    function paginateByProjectIds($project_ids, $page = 1, $per_page = 30, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Files::paginate(array(
        'conditions' => array('project_id IN (?) AND type = ? AND state >= ? AND visibility >= ?', $project_ids, 'File', $min_state, $min_visibility),
        'order' => 'created_on DESC'
      ), $page, $per_page);
    } // paginateByProjectIds
    
    /**
     * Return Files by a given milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findByMilestone($milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('milestone_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), 'File', $min_state, $min_visibility),
        'order' => 'created_on, name',
      ));
    } // findByMilestone
    
    /**
     * Return paginatede files by cateogry
     *
     * @param Category $category
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function paginateByCategory($category, $page = 1, $per_page = 10, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Files::paginate(array(
        'conditions' => array('parent_id = ? AND type = ? AND state >= ? AND visibility >= ?', $category->getId(), 'File', $min_state, $min_visibility),
        'order' => 'created_on DESC'
      ), $page, $per_page);
    } // paginateByCategory
  
  } // Files

?>