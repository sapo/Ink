<?php

  /**
   * Starred object manager class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class StarredObjects {
    
    /**
     * Star specific project
     *
     * @param ProjectObject $object
     * @param User $user
     * @return boolean
     */
    function starObject($object, $user) {
      if(!$object->canView($user)) {
        return false;
      } // if
      
      if(!StarredObjects::isStarred($object, $user)) {
        $cache_id = 'object_starred_by_' . $user->getId();
        
        $starred_objects = cache_get($cache_id);
        if(!is_array($starred_objects)) {
          $starred_objects = StarredObjects::findObjectIdsByUser($user);
        } // if
        
        // Already starred?
        if(in_array($object->getId(), $starred_objects)) {
          return true;
        } // if
        
        $execute = db_execute('INSERT INTO ' . TABLE_PREFIX . 'starred_objects (object_id, user_id) VALUES (?, ?)', $object->getId(), $user->getId());
        if($execute && !is_error($execute)) {
          $starred_objects[] = $object->getId();
          cache_set($cache_id, $starred_objects);
        } // if
        return $execute;
      } // if
      return true;
    } // starObject
    
    /**
     * Remove star from a given object
     *
     * @param ProjectObject $object
     * @param User $user
     * @return boolean
     */
    function unstarObject($object, $user) {
      if(!$object->canView($user)) {
        return false;
      } // if
      
      if(StarredObjects::isStarred($object, $user)) {
        $cache_id = 'object_starred_by_' . $user->getId();
        
        $starred_objects = cache_get($cache_id);
        if(!is_array($starred_objects)) {
          $starred_objects = StarredObjects::findObjectIdsByUser($user);
        } // if
        
        // Not starred?
        if(!in_array($object->getId(), $starred_objects)) {
          return true;
        } // if
        
        $execute = db_execute('DELETE FROM ' . TABLE_PREFIX . 'starred_objects WHERE object_id = ? AND user_id = ?', $object->getId(), $user->getId());
        if($execute && !is_error($execute)) {
          unset($starred_objects[array_search($object->getId(), $starred_objects)]);
          cache_set($cache_id, $starred_objects);
        } // if
        return $execute;
      } // if
      return true;
    } // unstarObject
    
    /**
     * Check if $object is starred by $user
     *
     * @param ProjectObject $object
     * @param User $user
     * @return boolean
     */
    function isStarred($object, $user) {
      $cache_id = 'object_starred_by_' . $user->getId();
        
      $starred_objects = cache_get($cache_id);
      if(!is_array($starred_objects)) {
        $starred_objects = StarredObjects::findObjectIdsByUser($user);
        cache_set($cache_id, $starred_objects);
      } // if
      
      return in_array($object->getId(), $starred_objects);
    } // isStarred
    
    /**
     * Return starred project objects by $user
     *
     * @param User $user
     * @return array
     */
    function findByUser($user) {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $starred_objects_table = TABLE_PREFIX . 'starred_objects';
      
      if($user->isProjectManager()) {
        return ProjectObjects::findBySQL("SELECT $project_objects_table.* FROM $project_objects_table, $starred_objects_table WHERE $starred_objects_table.object_id = $project_objects_table.id AND $starred_objects_table.user_id = ? AND $project_objects_table.state >= ? ORDER BY $project_objects_table.priority DESC", array($user->getId(), STATE_VISIBLE));
      } else {
        $type_filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE, PROJECT_STATUS_PAUSED, PROJECT_STATUS_CANCELED, PROJECT_STATUS_COMPLETED));
        if($type_filter) {  
          return ProjectObjects::findBySQL("SELECT $project_objects_table.* FROM $project_objects_table, $starred_objects_table WHERE $type_filter AND $starred_objects_table.object_id = $project_objects_table.id AND $starred_objects_table.user_id = ? AND $project_objects_table.state >= ? AND $project_objects_table.visibility >= ? ORDER BY $project_objects_table.priority DESC", array($user->getId(), STATE_VISIBLE, $user->getVisibility()));
        } // if
      } // if
      
      return null;
    } // findByUser
    
    /**
     * Return object ID-s by user
     *
     * @param User $user
     * @return array
     */
    function findObjectIdsByUser($user) {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $starred_objects_table = TABLE_PREFIX . 'starred_objects';
      
      if($user->isProjectManager()) {
        $rows = db_execute_all("SELECT object_id FROM $starred_objects_table WHERE user_id = ?", $user->getId());
      } else {
        $type_filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE, PROJECT_STATUS_PAUSED, PROJECT_STATUS_CANCELED, PROJECT_STATUS_COMPLETED));
        if($type_filter) {
          $rows = db_execute_all("SELECT $project_objects_table.id AS 'object_id' FROM $project_objects_table, $starred_objects_table WHERE $type_filter AND $starred_objects_table.object_id = $project_objects_table.id AND $starred_objects_table.user_id = ? AND $project_objects_table.state >= ? AND $project_objects_table.visibility >= ? ORDER BY $project_objects_table.priority DESC", $user->getId(), STATE_VISIBLE, $user->getVisibility());
        } else {
          $rows = null;
        } // if
      } // if
      
      $result = array();
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $result[] = (integer) $row['object_id'];
        } // foreach
      } // if
      
      return $result;
    } // findObjectIdsByUser
    
    /**
     * Drop all records by user
     *
     * @param User $user
     * @return boolean
     */
    function deleteByUser($user) {
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'starred_objects WHERE user_id = ?', $user->getId());
    } // deleteByUser
    
    /**
     * Drop records by object
     *
     * @param ProjectObject $object
     * @return boolean
     */
    function deleteByObject($object) {
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'starred_objects WHERE object_id = ?', $object->getId());
    } // deleteByObject
    
    /**
     * Delete activity log entries by object ID-s
     *
     * @param array $ids
     * @return boolean
     */
    function deleteByObjectIds($ids) {
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'starred_objects WHERE object_id IN (?)', $ids);
    } // deleteByObjectIds
  
  }

?>