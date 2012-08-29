<?php

  /**
   * ProjectGroups class
   */
  class ProjectGroups extends BaseProjectGroups {
  
    /**
     * Return all groups ordered by name
     * 
     * This function will return only groups visible to given user
     * 
     * If $return_all is set to true all groups will be loaded and returned. 
     * This is used in situations where we need all of them regardels of user 
     * previous assignments (like select project group helper)
     *
     * @param User $user
     * @param boolean $return_all
     * @return array
     */
    function findAll($user, $return_all = false) {
      if($return_all || $user->isAdministrator() || $user->isProjectManager()) {
        return ProjectGroups::find(array(
          'order' => 'name',
        ));
      } // if
      
      $project_ids = Projects::findProjectIdsByUser($user);
      if(is_foreachable($project_ids)) {
        $projects_table = TABLE_PREFIX . 'projects';
        $project_groups_table = TABLE_PREFIX . 'project_groups';
        
        return ProjectGroups::findBySQL("SELECT DISTINCT $project_groups_table.* FROM $projects_table, $project_groups_table WHERE $project_groups_table.id = $projects_table.group_id AND $projects_table.id IN (?) ORDER BY $project_groups_table.name", array($project_ids));
      } else {
        return null;
      } // if
    } // findAll
  
  }

?>