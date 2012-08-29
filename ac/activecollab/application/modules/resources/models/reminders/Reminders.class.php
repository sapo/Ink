<?php

  /**
   * Reminders class
   * 
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class Reminders extends BaseReminders {
  
    /**
     * Return active reminders for a given user
     *
     * @param User $user
     * @return array
     */
    function findActiveByUser($user) {
    	return Reminders::find(array(
    	  'conditions' => array('user_id = ?', $user->getId()),
    	  'order' => 'created_on DESC',
    	));
    } // findActiveByUser
    
    /**
     * Return number of active reminders for a given user
     *
     * @param User $user
     * @return integer
     */
    function countActiveByUser($user) {
      return Reminders::count(array('user_id = ?', $user->getId()));
    } // countActiveByUser
    
    /**
     * Drop all reminders by user
     *
     * @param User $user
     * @return boolean
     */
    function deleteByUser($user) {
    	return Reminders::delete(array('user_id = ?', $user->getId()));
    } // deleteByUser
    
    /**
     * Clear reminders by object
     *
     * @param ProjectObject $object
     * @return boolean
     */
    function deleteByObject($object) {
      return Reminders::delete(array('object_id = ?', $object->getId()));
    } // deleteByObject
    
    /**
     * Delete reminders by object ID-s
     *
     * @param array $ids
     * @return boolean
     */
    function deleteByObjectIds($ids) {
      return Reminders::delete(array('object_id IN (?)', $ids));
    } // deleteByObjectIds
  
  }

?>