<?php

  /**
   * StatusUpdates class
   * 
   * @package activeCollab.modules.status
   * @subpackage models
   */
  class StatusUpdates extends BaseStatusUpdates {
    
    /**
     * Return status updates created by $user
     *
     * @param User $user
     * @param integer $limit
     * @return array
     */
    function findByUser($user, $limit = null) {
      return StatusUpdates::find(array(
    	  'order' => 'created_on DESC',
    	  'conditions' => array('created_by_id = ?', $user->getID()),
      ));
    } // findByUser
  
    /**
     * Return status updates that are visible to provided user
     *
     * @param User $user
     * @param boolean $include_himself if true include users status updates in result
     * @return array
     */
    function findVisibleForUser($user, $limit = null) {     
      $criteria = array(
    	  'order' => 'created_on DESC',
    	  'conditions' => array('created_by_id IN (?)', $user->visibleUserIds()),
    	);
    	
    	if ($limit) {
    	  $criteria['limit'] = $limit;
    	} // if
    	
    	return StatusUpdates::find($criteria);
    } // findActiveByUser
    
    /**
     * Return messages by user ID-s
     *
     * @param array $user_ids
     * @return array
     */
    function findByUserIds($user_ids = null) {
      return StatusUpdates::find(array(
        'conditions' => array('created_by_id IN (?)', $user_ids),
        'order' => 'created_on DESC',
      ));
    } // findByUserIds
    
    /**
     * Return paginated status updates by user
     *
     * @param User $user
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateByUser($user, $page = 1, $per_page = 30) {
      return StatusUpdates::paginate(array(
        'conditions' => array('created_by_id = ?', $user->getId()),
        'order' => 'created_on DESC',
      ), $page, $per_page);
    } // paginateByProject
    
    /**
     * Return paginated status updates for user ids
     *
     * @param array $user_ids
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateByUserIds($user_ids = null, $page = 1, $per_page = 30) {
      return StatusUpdates::paginate(array(
        'conditions' => array('created_by_id IN (?)', $user_ids),
        'order' => 'created_on DESC',
      ), $page, $per_page);
    } // paginateByUserIds
    
    /**
     * Count new messages since date for provided user
     *
     * @param User $user
     * @param date $date
     * @return integer
     */
    function countNewMessagesForUser($user, $date) {
      return (integer) StatusUpdates::count(array(
        "created_by_id IN (?) AND created_on > ?", $user->visibleUserIds(), $date
      ));
    } // countNewMessages
    
    /**
     * Return status update #
     *
     * @param StatusUpdate $status_update
     * @param User $user
     * @return integer
     */
    function findStatusUpdateNumForUser($status_update, $user) {
      return StatusUpdates::count(array("id > ? AND created_by_id IN (?)", $status_update->getId(), $user->visibleUserIds())) + 1;
    } // findCommentNum
    
  }

?>