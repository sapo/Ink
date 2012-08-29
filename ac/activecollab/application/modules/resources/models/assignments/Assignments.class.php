<?php

  /**
   * Assignments class
   * 
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class Assignments extends BaseAssignments {
    
    /**
     * Returns true if $user is assigneed to $object
     *
     * @param User $user
     * @param ProjectObject $object
     * @return boolean
     */
    function isAssignee($user, $object) {
      $cache_id = 'user_assignments_' . $user->getId();
      
      $cached_values = cache_get($cache_id);
      if(is_array($cached_values) && isset($cached_values[$object->getId()])) {
        return $cached_values[$object->getId()];
      } // if
      
      $value = (boolean) Assignments::count(array('user_id = ? AND object_id = ?', $user->getId(), $object->getId()));
      
      if(is_array($cached_values)) {
        $cached_values[$object->getId()] = $value;
      } else {
        $cached_values = array($object->getId() => $value);
      } // if
      
      cache_set($cache_id, $cached_values);
      return $value;
    } // isAssignee
  
    /**
     * Return all users assigned to a specific object
     *
     * @param ProjectObject $object
     * @return array
     */
    function findAssigneesByObject($object) {
      $cache_id = 'object_assignments_' . $object->getId();
      
      $cached_values = cache_get($cache_id);
      if(is_array($cached_values)) {
        if(count($cached_values) > 0) {
          return Users::findByIds($cached_values);
        } else {
          return null;
        } // if
      } // if
      
      $users_table = TABLE_PREFIX . 'users';
      $assignments_table = TABLE_PREFIX . 'assignments';
      
      $cached_values = array();
      
      $rows = db_execute_all("SELECT $users_table.id FROM $users_table, $assignments_table WHERE $assignments_table.object_id = ? AND $assignments_table.user_id = $users_table.id", $object->getId());
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $cached_values[] = (integer) $row['id'];
        } // foreach
      } // if
      
      cache_set($cache_id, $cached_values);
      
      if(count($cached_values) > 0) {
        return Users::findByIds($cached_values);
      } else {
        return null;
      } // if
    } // findAssigneesByObject
    
    /**
     * Return muber of users assigned to a given object
     *
     * @param ProjectObject $object
     * @return integer
     */
    function countAssigneesByObject($object) {
      return Assignments::count(array('object_id = ?', $object->getId()));
    } // countAssigneesByObject
    
    /**
     * Return assignment data by object
     * 
     * This function will return array of assignment data where first element is 
     * list of user ID-s and second parameter is ID of owner user
     *
     * @param ProjectObject $object
     * @return array
     */
    function findAssignmentDataByObject($object) {
      $assignees = array();
      $owner_id = 0;
      
      $rows = db_execute_all('SELECT user_id, is_owner FROM ' . TABLE_PREFIX . 'assignments WHERE object_id = ?', $object->getId());
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $user_id = (integer) $row['user_id'];
          $is_owner = (integer) $row['is_owner'];
          
          $assignees[] = $user_id;
          
          if($is_owner) {
            $owner_id = $user_id;
          } // if
        } // foreach
      } // if
      
      return array($assignees, $owner_id);
    } // findAssignmentDataByObject
    
    /**
     * Return object owner by object
     *
     * @param ProjectObject $object
     * @return User
     */
    function findOwnerByObject($object) {
      $users_table = TABLE_PREFIX . 'users';
      $assignments_table = TABLE_PREFIX . 'assignments';
      
      $users = Users::findBySQL("SELECT $users_table.* FROM $users_table, $assignments_table WHERE $assignments_table.object_id = ? AND $assignments_table.user_id = $users_table.id AND $assignments_table.is_owner = ? ORDER BY $users_table.company_id LIMIT 0, 1", array($object->getId(), true));
      
      if(is_array($users) && isset($users[0]) && instance_of($users[0], 'User')) {
        return $users[0];
      } else {
        return null;
      } // if
    } // findOwnerByObject
    
    /**
     * Clone assignmnets from $from to $to object
     *
     * @param ProjectObject $from
     * @param ProjectObject $to
     * @return boolean
     */
    function cloneAssignments($from, $to) {
      $rows = db_execute_all('SELECT * FROM ' . TABLE_PREFIX . 'assignments WHERE object_id = ?', $from->getId());
    	if(is_foreachable($rows)) {
    	  $project = $to->getProject(); // we need it to check if user has access to a given project
    	  
    	  $owner_id = null;
    	  $assignees = null;
    	  
    	  foreach($rows as $row) {
    	    $user = Users::findById($row['user_id']);
    	    if(instance_of($user, 'User') && $user->isProjectMember($project)) {
    	      if($row['is_owner']) {
    	        $owner_id = (integer) $row['user_id'];
    	      } else {
    	        $assignees[] = (integer) $row['user_id'];
    	      }
    	    } // if
    	  } // if
    	  
    	  if($owner_id) {
    	    $object_id = $to->getId();
    	    
    	    $to_insert = array("($owner_id, $object_id, '1')");
    	    if(is_foreachable($assignees)) {
    	      foreach($assignees as $user_id) {
    	        $to_insert[] = "($user_id, $object_id, '0')";
    	      } // foreach
    	    } // if
    	    
    	    return db_execute('INSERT INTO ' . TABLE_PREFIX . 'assignments (user_id, object_id, is_owner) VALUES ' . implode(', ', $to_insert));
    	  } // if
    	} // if
    	return true;
    } // cloneAssignments
    
    /**
     * Delete assignmnets by project object
     *
     * @param ProjectObject $object
     * @return boolean
     */
    function deleteByObject($object) {
      cache_remove('object_assignments_' . $object->getId());
      cache_remove('object_assignments_' . $object->getId() . '_rendered');
      return Assignments::delete(array('object_id = ?', $object->getId()));
    } // deleteByObject
    
    /**
     * Delete assignemtns by object ID-s
     *
     * @param array $ids
     * @return boolean
     */
    function deleteByObjectIds($ids) {
      cache_remove_by_pattern('object_assignments_*');
      return Assignments::delete(array('object_id IN (?)', $ids));
    } // deleteByObjectIds
    
    /**
     * Delete assignments by User
     *
     * @param User $user
     * @return boolean
     */
    function deleteByUser($user) {
      cache_remove('user_assignments_' . $user->getId());
      return Assignments::delete(array('user_id = ?', $user->getId()));
    } // deleteByUser
  
  }

?>