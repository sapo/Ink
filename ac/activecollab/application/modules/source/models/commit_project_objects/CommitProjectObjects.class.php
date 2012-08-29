<?php

  /**
   * CommitProjectObjects class
   * 
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class CommitProjectObjects extends BaseCommitProjectObjects {
    
    /**
     * Return number of commits related to a project object
     *
     * @param integer $object_id
     * @param integer $project_id
     * @return integer
     */
    function countByObject($object) {
      $object_commits_count = CommitProjectObjects::count("object_id = '".$object->getId()."' AND project_id = '".$object->getProjectId()."'");
      
      if (instance_of($object, 'Ticket')) {
        $task_ids = array();
        $tasks = db_execute_all("SELECT id FROM ".TABLE_PREFIX."project_objects WHERE parent_id = ".$object->getid()." AND `type` = 'Task'");
        if (is_foreachable($tasks)) {
          foreach ($tasks as $task) {
        	 $task_ids[] = $task['id'];
          } // foreach
        } // if
        
        if (is_foreachable($task_ids)) {
          $related_tasks = db_execute_one("SELECT COUNT(*) AS row_count FROM `".TABLE_PREFIX."commit_project_objects` WHERE object_id IN(".implode(',', $task_ids).")");
          $object_commits_count += array_var($related_tasks, 'row_count', 0);
        } // if
      } //if
      
      return $object_commits_count;
    } // countByObject
    
    
    /**
     * Get all commits related to a project object
     *
     * @param integer $object_id
     * @return array
     */
    function findCommitsByObject($object) {
      $parent_object_ids = array();
      $parent_object_ids[] = $object->getId();
      
      /**
       * Try to find commits related to children objects
       */
      $task_ids = array();
      if (instance_of($object, 'Ticket')) {
        $tasks = db_execute_all("SELECT id FROM ".TABLE_PREFIX."project_objects WHERE parent_id = ".$object->getid()." AND `type` = 'Task'");
        if (is_foreachable($tasks)) {
          foreach ($tasks as $task) {
        	 $task_ids[] = $task['id'];
          } // foreach
        } // if
      } // if
      
      $objects_ids = array_merge($parent_object_ids, $task_ids);
      
      $commit_project_objects = CommitProjectObjects::find(array(
        'conditions' => array("object_id IN(".implode(',', $objects_ids).")"),
        'order' => 'repository_id ASC, revision DESC'
      ));
      
      if (is_foreachable($commit_project_objects)) {
        $commits = array();
        $revisions = array();
        foreach ($commit_project_objects as $commit_project_object) {
          if (!in_array($commit_project_object->getRevision(), $revisions)) { // prevent commits from showing more than once
            $revisions[] = $commit_project_object->getRevision();
            $commit = Commits::findByRevision($commit_project_object->getRevision(), Repositories::findById($commit_project_object->getRepositoryId()));
            if (instance_of($commit, 'Commit')) {
              $commit->repository = Repositories::findById($commit->getProjectId());
              $commits[] = $commit;
            } // if
          } // if
        } // foreach
        
        return group_by_date($commits);
      }
      else {
        return false;
      } // if
      
    } // findCommitsByObjectId
  
  }

?>