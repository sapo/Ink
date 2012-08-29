<?php

  /**
   * Subscriptions class
   * 
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class Subscriptions extends BaseSubscriptions {

    /**
      * Find subscriptions by parent
      *
      * @param ProjectObject $parent
      * @return array
      */ 
    function findSubscribersByParent($parent) {
      $users_table = TABLE_PREFIX . 'users';
      $subscriptions_table = TABLE_PREFIX . 'subscriptions';
      
      return Users::findBySQL("SELECT $users_table.* FROM $users_table, $subscriptions_table WHERE $users_table.id = $subscriptions_table.user_id AND $subscriptions_table.parent_id = ?", array($parent->getId()));
    } // findSubscribersByParent
  
    /**
     * Return subscriptions by parent object
     *
     * @param ProjectObject $parent
     * @return array
     */
    function findByParent($parent) {
      return Subscriptions::find(array(
        'conditions' => array('parent_id = ?', $parent->getId()),
      ));
    } // findByParent
    
    /**
     * Return number of users subscribed to $parent
     *
     * @param User $parent
     * @return integer
     */
    function countByParent($parent) {
      return Subscriptions::count(array('parent_id = ?', $parent->getId()));
    } // countByParent
    
    /**
     * Return subscriptions by user
     *
     * @param User $user
     * @return array
     */
    function findByUser($user) {
      return Subscriptions::find(array(
        'conditions' => array('user_id = ?', $user->getId()),
      ));
    } // findByUser
    
    /**
     * Check if $user is subscribed to given $object
     *
     * @param User $user
     * @param ProjectObject $object
     * @param boolean $use_cache
     * @return boolean
     */
    function isSubscribed($user, $object, $use_cache = true) {
      if($use_cache) {
        $cache_value = cache_get('user_subscriptions_' . $user->getId());
        if(is_array($cache_value)) {
          return in_array($object->getId(), $cache_value);
        } else {
          $cache_value = Subscriptions::rebuildUserCache($user);
          return in_array($object->getId(), $cache_value);
        } // if
      } else {
        return (boolean) Subscriptions::count(array('user_id = ? AND parent_id = ?', $user->getId(), $object->getId()));
      } // if
    } // isSubscribed
    
    /**
     * Subscribe $user to a given $object
     *
     * @param User $user
     * @param ProjectObject $object
     * @return boolean
     */
    function subscribe($user, $object) {
      if(!$object->can_have_subscribers) {
        return new InvalidParamError('$object', $object, '$object does not support subscribers', true);
      } // if
      
      if(Subscriptions::isSubscribed($user, $object, false)) {
        return true;
      } // if
      
      $subscription = new Subscription();
      
      $subscription->setUserId($user->getId());
      $subscription->setParentId($object->getId());
      
      $save = $subscription->save();
      if($save && !is_error($save)) {
        Subscriptions::dropUserCache($user);
        return true;
      } // if
      
      return $save;
    } // subscribe
    
    /**
     * Remove subscription
     *
     * @param User $user
     * @param ProjectObject $object
     * @return boolean
     */
    function unsubscribe($user, $object) {
      if(!$object->can_have_subscribers) {
        return new InvalidParamError('$object', $object, '$object does not support subscribers', true);
      } // if
      
      Subscriptions::dropUserCache($user);
      return Subscriptions::delete(array('user_id = ? AND parent_id = ?', $user->getId(), $object->getId()));
    } // unsubscribe
    
    /**
     * Subscribe array of users to the object
     * 
     * If $replace is set to true, all subscriptions for this object will be 
     * dropped and $users will be subscribed to it
     *
     * @param array $users
     * @param ProjectObject $object
     * @param boolean $replace
     * @return boolean
     */
    function subscribeUsers($users, $object, $replace = true) {
      db_begin_work();
      
      $object_id = (integer) $object->getId();
      if($object_id) {
        $subscriptions_table = TABLE_PREFIX . 'subscriptions';
        
        if($replace) {
          Subscriptions::deleteByParent($object); // cleanup
        } // if
      
        $to_subscribe = array();
        if(is_foreachable($users)) {
          foreach($users as $user) {
            if(instance_of($user, 'User')) {
              $user_id = (integer) $user->getId();
            } else {
              $user_id = (integer) $user;
            } // if
            
            if($user_id) {
              if(isset($to_subscribe[$user_id])) {
                continue; // duplicate user ID!
              } else {
                if(!$replace && array_var(db_execute_one("SELECT COUNT(*) AS 'row_count' FROM $subscriptions_table WHERE user_id = ? AND parent_id = ?", $user_id, $object_id), 'row_count') > 0) {
                  continue; // Make sure that we do not have this user already subscribed
                } // if
                
                cache_remove("user_subscriptions_$user_id");
                $to_subscribe[$user_id] = "($user_id, $object_id)";
              } // if
            } // if
          } // foreach
        } // if
        
        // Insert subscriptions
        if(is_foreachable($to_subscribe)) {
          $insert = db_execute("INSERT INTO $subscriptions_table VALUES " . implode(', ', $to_subscribe));
          
          if(!$insert || is_error($insert)) {
            db_rollback();
            return $insert;
          } // if
        } // if
      } // if
      
      db_commit();
      return true;
    } // subscribeUsers
    
    /**
     * Clone subscriptions from $from to $to object
     *
     * @param ProjectObject $from
     * @param ProjectObject $to
     * @return boolean
     */
    function cloneSubscriptions($from, $to) {
    	$project = $to->getProject(); // we need it to check if user has access to a given project
      
    	$rows = db_execute_all('SELECT * FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_id = ?', $from->getId());
    	if(is_foreachable($rows)) {
    	  foreach($rows as $row) {
      	  $user = Users::findById($row['user_id']);
      	  if(instance_of($user, 'User') && $user->isProjectMember($project)) {
      	    Subscriptions::subscribe($user, $to);
      	  } // if
    	  } // if
    	} // if
    	return true;
    } // cloneSubscriptions
    
    /**
     * Delete subscriptions by parent
     *
     * @param ProjectObject $parent
     * @return boolean
     */
    function deleteByParent($parent) {
      cache_remove_by_pattern('user_subscriptions_*');
      return Subscriptions::delete(array('parent_id = ?', $parent->getId()));
    } // deleteByParent
    
    /**
     * Delete subscriptions by object ID-s
     *
     * @param array $ids
     * @return boolean
     */
    function deleteByObjectIds($ids) {
      cache_remove_by_pattern('user_subscriptions_*');
      return Subscriptions::delete(array('parent_id IN (?)', $ids));
    } // deleteByObjectIds
    
    /**
     * Delete subscriptions by user
     *
     * @param User $user
     * @return boolean
     */
    function deleteByUser($user) {
      return Subscriptions::delete(array('user_id = ?', $user->getId()));
    } // deleteByUser
    
    /**
     * Rebuild user cache
     * 
     * This function loads array of subscribed project ID-s, caches it and 
     * returns it as a result
     *
     * @param User $user
     * @return array
     */
    function rebuildUserCache($user) {
    	$cache_id = 'user_subscriptions_' . $user->getId();
    	
    	$result = array();
    	
    	$rows = db_execute_all('SELECT DISTINCT parent_id FROM ' . TABLE_PREFIX . 'subscriptions WHERE user_id = ?', $user->getId());
    	if(is_foreachable($rows)) {
    	  foreach($rows as $row) {
    	    $result[] = (integer) $row['parent_id'];
    	  } // foreach
    	} // if
    	
    	cache_set($cache_id, $result);
    	return $result;
    } // rebuildUserCache
    
    /**
     * Drop user subscription cache
     *
     * @param User $user
     * @return null
     */
    function dropUserCache($user) {
    	cache_remove('user_subscriptions_' . $user->getId());
    } // dropUserCache
  
  }

?>