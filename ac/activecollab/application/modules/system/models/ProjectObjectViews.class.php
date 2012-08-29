<?php

  $_SESSION['project_object_views'] = array();

  /**
   * Project object views manager
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectObjectViews {
    
    /**
     * Log new view
     *
     * @param ProjectObject $object
     * @param User $user
     * @return boolean
     */
    function log($object, $user) {
      if(!isset($_SESSION['project_object_views'][$object->getId()])) {
        $_SESSION['project_object_views'][$object->getId()] = array();
      } // if
      
      if(!isset($_SESSION['project_object_views'][$object->getId()][$user->getId()])) {
        $_SESSION['project_object_views'][$object->getId()][$user->getId()] = array(
          'name' => $user->getDisplayName(),
          'email' => $user->getEmail(),
        );
      } // if
    	//return db_execute('INSERT INTO ' . TABLE_PREFIX . 'project_object_views (object_id, created_by_id, created_by_name, created_by_email, created_on) VALUES (?, ?, ?, ?, ?)', $object->getId(), $user->getId(), $user->getDisplayName(), $user->getEmail(), date(DATETIME_MYSQL));
    } // log
    
    /**
     * Do save data collected in this request in database
     *
     * @param void
     * @return boolean
     */
    function save() {
      $date = db_escape(date(DATETIME_MYSQL));
      
    	$for_drop = array();
    	$for_insert = array();
    	
    	$user_ids = array();
    	
    	foreach($_SESSION['project_object_views'] as $object_id => $users) {
    	  foreach($users as $user_id => $user) {
    	    $object_id = (integer) $object_id;
    	    $user_id = (integer) $user_id;
    	    
    	    if(!in_array($user_id, $user_ids)) {
    	      $user_ids[] = $user_id;
    	    } // if
    	    
    	    if($object_id && $user_id) {
    	      $for_drop[] = "(object_id = '$object_id' AND created_by_id = '$user_id')";
    	      
    	      $name = db_escape($user['name']);
    	      $email = db_escape($user['email']);
    	      
    	      $for_insert[] = "($object_id, $user_id, $name, $email, $date)";
    	    } // if
    	  } // foreachs
    	} // foreach
    	
    	if(is_foreachable($for_drop)) {
    	  db_execute('DELETE FROM ' . TABLE_PREFIX . 'project_object_views WHERE ' . implode(' OR ', $for_drop));
    	} // if
    	
    	if(is_foreachable($for_insert)) {
    	  return db_execute('INSERT INTO ' . TABLE_PREFIX . 'project_object_views (object_id, created_by_id, created_by_name, created_by_email, created_on) VALUES ' . implode(', ', $for_insert));
    	} // if
    	
    	// Clear cache...
    	foreach($user_ids as $user_id) {
    	  cache_remove("object_viewed_by_$user_id");
    	} // foreach
    } // save
    
    /**
     * Remove records older than 30 days
     *
     * @param void
     * @return boolean
     */
    function cleanUp() {
      $older_than = new DateTimeValue('-30 days');
    	return db_execute('DELETE FROM ' . TABLE_PREFIX . 'project_object_views WHERE created_on <= ?', $older_than);
    } // cleanUp
    
    /**
     * Remove views by object
     *
     * @param ProjectObject $object
     * @return boolean
     */
    function clearByObject($object) {
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'project_object_views WHERE object_id = ?', $object->getId());
    } // clearByObject
    
    /**
     * Return ID-s of viewed objects for a given user
     *
     * @param User $user
     * @return array
     */
    function findViewedObjectIds($user) {
      $cache_id = 'object_viewed_by_' . $user->getId();
      
      $cached_value = array();
      
      $rows = db_execute_all('SELECT DISTINCT object_id FROM ' . TABLE_PREFIX . 'project_object_views WHERE created_by_id = ?', $user->getId());
    	if(is_foreachable($rows)) {
    	  foreach($rows as $row) {
    	    $cached_value[] = (integer) $row['object_id'];
    	  } // foreach
    	} // if
    	
    	cache_set($cache_id, $cached_value);
    	return $cached_value;
    } // findViewedObjectIds
    
    /**
     * Returns true if $object is viewed by $user
     *
     * @param ProjectObject $object
     * @param User $user
     * @return boolan
     */
    function isViewed($object, $user) {
      return (boolean) array_var(db_execute_one("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . 'project_object_views WHERE object_id = ? AND created_by_id = ?', $object->getId(), $user->getId()), 'row_count');
    } // isViewed
    
  }

?>