<?php

  require_once SYSTEM_MODULE_PATH . '/models/search_engines/SearchEngine.class.php';
  
  /**
   * MySQL search engine implementation
   * 
   * This search engine stores data into MyISAM table with FULLTEXT key on 
   * content field. This is default search engine and should be used only for 
   * low load websites
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class MysqlSearchEngine extends SearchEngine  {
    
    /**
     * Mysql Search
     *
     * @param string $search_for
     * @param string $type
     * @param User $user
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function search($search_for, $type, $user, $page = 1, $per_page = 30) {
      $page = (integer) $page;
      $per_page = (integer) $per_page;
      
      $search_index_table = TABLE_PREFIX . 'search_index';
      
      $offset = ($page - 1) * $per_page;
      
      // Search in projects
      if($type == 'ProjectObject') {
        $type_filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE, PROJECT_STATUS_PAUSED, PROJECT_STATUS_COMPLETED, PROJECT_STATUS_CANCELED));
        if(empty($type_filter)) {
          return array(null, new Pager(1, 0, $per_page));
        } // if
        
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        
        $total_items = (integer) array_var(db_execute_one("SELECT COUNT($project_objects_table.id) AS 'row_count' FROM $project_objects_table, $search_index_table WHERE $type_filter AND MATCH ($search_index_table.content) AGAINST (? IN BOOLEAN MODE) AND $project_objects_table.id = $search_index_table.object_id AND $search_index_table.type = ? AND state >= ? AND visibility >= ?", $search_for, $type, STATE_VISIBLE, $user->getVisibility()), 'row_count');
        if($total_items) {
          $items = ProjectObjects::findBySQL("SELECT $project_objects_table.* FROM $project_objects_table, $search_index_table WHERE $type_filter AND MATCH ($search_index_table.content) AGAINST (? IN BOOLEAN MODE) AND $project_objects_table.id = $search_index_table.object_id AND $search_index_table.type = ? AND state >= ? AND visibility >= ? LIMIT $offset, $per_page", array($search_for, $type, STATE_VISIBLE, $user->getVisibility()));
        } else {
          $items = null;
        } // if
          
        return array($items, new Pager($page, $total_items, $per_page));
        
      // Search for projects
      } elseif($type == 'Project') {
        $project_ids = Projects::findProjectIdsByUser($user, null, true);
        if(!is_foreachable($project_ids)) {
          return array(null, new Pager(1, 0, $per_page));
        } // if
        
        $projects_table = TABLE_PREFIX . 'projects';
        
        $total_items = (integer) array_var(db_execute_one("SELECT COUNT($projects_table.id) AS 'row_count' FROM $projects_table, $search_index_table WHERE $projects_table.id IN (?) AND MATCH ($search_index_table.content) AGAINST (? IN BOOLEAN MODE) AND $projects_table.id = $search_index_table.object_id AND $search_index_table.type = ?", $project_ids, $search_for, 'Project'), 'row_count');
        if($total_items) {
          $items = Projects::findBySQL("SELECT * FROM $projects_table, $search_index_table WHERE $projects_table.id IN (?) AND MATCH ($search_index_table.content) AGAINST (? IN BOOLEAN MODE) AND $projects_table.id = $search_index_table.object_id AND $search_index_table.type = ? LIMIT $offset, $per_page", array($project_ids, $search_for, 'Project'));
        } else {
          $items = null;
        } // if
        
        return array($items, new Pager($page, $total_items, $per_page));
        
      // Search for users
      } elseif($type == 'User') {
        $user_ids = $user->visibleUserIds();
        if(!is_foreachable($user_ids)) {
          return array(null, new Pager(1, 0, $per_page));
        } // if
        $users_table = TABLE_PREFIX . 'users';
        
        $total_items = (integer) array_var(db_execute_one("SELECT COUNT($users_table.id) AS 'row_count' FROM $users_table, $search_index_table WHERE $users_table.id IN (?) AND MATCH ($search_index_table.content) AGAINST (? IN BOOLEAN MODE) AND $users_table.id = $search_index_table.object_id AND $search_index_table.type = ?", $user_ids, $search_for, 'User'), 'row_count');
        if($total_items) {
          $items = Users::findBySQL("SELECT * FROM $users_table, $search_index_table WHERE $users_table.id IN (?) AND MATCH ($search_index_table.content) AGAINST (? IN BOOLEAN MODE) AND $users_table.id = $search_index_table.object_id AND $search_index_table.type = ? LIMIT $offset, $per_page", array($user_ids, $search_for, 'User'));
        } else {
          $items = null;
        } // if
        
        return array($items, new Pager($page, $total_items, $per_page));
        
      // Unknown search type
      } else {
        return array(null, new Pager(1, 0, $per_page));
      } // if
    } // search
    
    /**
     * Update
     *
     * @param integer $object_id
     * @param string $type
     * @param string $content
     * @param array $atributtes
     * @return null
     */
    function update($object_id, $type, $content, $atributtes = null) {
      $search_index_table = TABLE_PREFIX . 'search_index';
      if(search_index_has($object_id, $type)) {
        return db_execute("UPDATE $search_index_table SET content = ? WHERE object_id = ? AND type = ?", $content, $object_id, $type);
      } else {
        return db_execute("INSERT INTO $search_index_table (object_id, type, content) VALUES (?, ?, ?)", $object_id, $type, $content);
      } // if
    } // update
    
    /**
     * Remove from search index
     *
     * @param mixed $object_id
     * @param string $type
     * @return null
     */
    function remove($object_id, $type) {
      $search_index_table = TABLE_PREFIX . 'search_index';
    	return db_execute("DELETE FROM $search_index_table WHERE object_id IN (?) AND type = ?", $object_id, $type);
    } // remove
    
    /**
     * Returns true if we already have an search index for a given entry
     *
     * @param integer $object_id
     * @param string $type
     * @return boolean
     */
    function hasObject($object_id, $type) {
      $search_index_table = TABLE_PREFIX . 'search_index';
      return (boolean) array_var(db_execute_one("SELECT COUNT(*) AS 'row_count' FROM $search_index_table WHERE object_id = ? AND type = ?", $object_id, $type), 'row_count');
    } // hasObject
    
  } 

?>