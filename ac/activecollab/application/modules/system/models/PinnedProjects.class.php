<?php

  /**
   * Favorite Projects manager
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class PinnedProjects {
  
    /**
     * Mark project as pinned
     *
     * @param Project $project
     * @param User $user
     * @return boolean
     */
    function pinProject($project, $user) {
      if(PinnedProjects::isPinned($project, $user, false)) {
        return true;
      } // if
      
      PinnedProjects::dropUserCache($user);
      return db_execute('INSERT INTO ' . TABLE_PREFIX . 'pinned_projects (project_id, user_id) VALUES (?, ?)', $project->getId(), $user->getId());
    } // pinProject
    
    /**
     * Mark project as not pinned
     *
     * @param Project $project
     * @param User $user
     * @return boolean
     */
    function unpinProject($project, $user) {
      PinnedProjects::dropUserCache($user);
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'pinned_projects WHERE project_id = ? AND user_id = ?', $project->getId(), $user->getId());
    } // unpinProject
    
    /**
     * Check if $project is pinned
     *
     * @param Project $project
     * @param User $user
     * @param boolean $use_cache
     * @return boolean
     */
    function isPinned($project, $user, $use_cache = true) {
      if($use_cache) {
        $cache_value = cache_get('user_pinned_projects_' . $user->getId());
        if(is_array($cache_value)) {
          return in_array($project->getId(), $cache_value);
        } else {
          return in_array($project->getId(), PinnedProjects::rebuildUserCache($user));
        }
      } else {
        return (boolean) array_var(db_execute_one("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . 'pinned_projects WHERE project_id = ? AND user_id = ?', $project->getId(), $user->getId()), 'row_count');
      } // if
    } // isPinned
    
    /**
     * Return ID-s of projects pinned by a given user
     *
     * @param User $user
     * @return array
     */
    function findProjectIdsByUser($user, $use_cache = true) {
      if($use_cache) {
        $cache_value = cache_get('user_pinned_projects_' . $user->getId());
        return is_array($cache_value) ? $cache_value : PinnedProjects::rebuildUserCache($user);
      } else {
        $projects_table = TABLE_PREFIX . 'projects';
        $pinned_projects_table = TABLE_PREFIX . 'pinned_projects';
        
        $ids = array();
        
        $rows = db_execute_all("SELECT $projects_table.id FROM $projects_table, $pinned_projects_table WHERE $projects_table.id = $pinned_projects_table.project_id AND $pinned_projects_table.user_id = ? AND $projects_table.type = ? ORDER BY $projects_table.name", $user->getId(), PROJECT_TYPE_NORMAL);
        if(is_foreachable($rows)) {
          foreach($rows as $row) {
            $ids[] = (integer) $row['id'];
          } // foreach
        } // if
        
        return $ids;
      } // if
    } // findProjectIdsByUser
    
    /**
     * Drop all records by user
     *
     * @param User $user
     * @return boolean
     */
    function deleteByUser($user) {
      cache_remove_by_pattern('user_pinned_projects_*');
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'pinned_projects WHERE user_id = ?', $user->getId());
    } // deleteByUser
    
    /**
     * Drop records by project
     *
     * @param Project $project
     * @return boolean
     */
    function deleteByProject($project) {
      cache_remove_by_pattern('user_pinned_projects_*');
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'pinned_projects WHERE project_id = ?', $project->getId());
    } // deleteByProject
    
    /**
     * Rebuild user cache
     *
     * @param User $user
     * @return array
     */
    function rebuildUserCache($user) {
    	$value = PinnedProjects::findProjectIdsByUser($user, false);
    	if(empty($value)) {
    	  $value = array();
    	} // if
    	cache_set('user_pinned_projects_' . $user->getId(), $value);
    	return $value;
    } // rebuildUserCache
    
    /**
     * Drop user cache
     *
     * @param User $user
     * @return null
     */
    function dropUserCache($user) {
    	cache_remove('user_pinned_projects_' . $user->getId(), $value);
    } // dropUserCache
  
  } // PinnedProjects

?>