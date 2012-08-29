<?php

  /**
   * Comments manager class
   *
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class Comments extends ProjectObjects {
    
    /**
     * Return object comments
     *
     * @param ProjectObject $object
     * @param integer $min_state
     * @param integer $min_visiblity
     * @return array
     */
    function findByObject($object, $min_state = STATE_VISIBLE, $min_visiblity = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array("type = 'Comment' AND parent_id = ? AND state >= ? AND visibility >= ?", $object->getId(), $min_state, $min_visiblity),
        'order' => 'created_on',
      ));
    } // findByObject
    
    /**
     * Paginate comments by object
     *
     * @param ProjectObject $object
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visiblity
     * @return array
     */
    function paginateByObject($object, $page = 1, $per_page = 30, $min_state = STATE_VISIBLE, $min_visiblity = VISIBILITY_NORMAL) {
      return ProjectObjects::paginate(array(
        'conditions' => array("type = 'Comment' AND parent_id = ? AND state >= ? AND visibility >= ?", $object->getId(), $min_state, $min_visiblity),
        'order' => 'created_on',
      ), $page, $per_page);
    } // paginateByObject
    
    /**
      * Return number of comments that match given criteria
      *
      * @param ProjectObject $object
      * @param integer $min_state
      * @param integer $min_visiblity
      * @return integer
      */
    function countByObject($object, $min_state = STATE_VISIBLE, $min_visiblity = VISIBILITY_NORMAL) {
      return ProjectObjects::count(array("type = 'Comment' AND parent_id = ? AND state >= ? AND visibility >= ?", $object->getId(), $min_state, $min_visiblity));
    } // countByObject
    
    /**
     * Return comment # for a given comment in a parent object
     *
     * @param Comment $comment
     * @param integer $min_state
     * @param integer $min_visibility
     * @return integer
     */
    function findCommentNum($comment, $min_state = STATE_VISIBLE, $min_visiblity = VISIBILITY_NORMAL) {
      return ProjectObjects::count(array("type = 'Comment' AND created_on < ? AND parent_id = ? AND state >= ? AND visibility >= ?", $comment->getCreatedOn(), $comment->getParentId(), $min_state, $min_visiblity)) + 1;
    } // findCommentNum
    
    /**
     * Return people who commented on $object that $user can see
     *
     * @param ProjectObject $object
     * @param User $user
     * @return array
     */
    function findCommenters($object, $user) {
      if(!$object->can_have_comments) {
        return null;
      } // if
      
    	$visible_user_ids = Users::findVisibleUserIds($user);
    	if(is_foreachable($visible_user_ids)) {
    	  $users_table = TABLE_PREFIX . 'users';
    	  $project_objects_table = TABLE_PREFIX . 'project_objects';
    	  
    	  return Users::findBySQL("SELECT DISTINCT $users_table.* FROM $users_table, $project_objects_table WHERE $project_objects_table.parent_id = ? AND $users_table.id = $project_objects_table.created_by_id AND $project_objects_table.type = ? AND $project_objects_table.state >= ? AND $project_objects_table.visibility >= ? AND $users_table.id IN (?)", array($object->getId(), 'Comment', STATE_VISIBLE, $user->getVisibility(), $visible_user_ids));
    	} // if
    	
    	return null;
    } // findCommenters
    
    /**
     * Return $count number of most recent comment that match given criteria
     *
     * @param ProjectObject $object
     * @param integer $count
     * @param integer $min_state
     * @param integer $min_visiblity
     * @return array
     */
    function findRecentObject($object, $count = 5, $min_state = STATE_VISIBLE, $min_visiblity = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array("type = 'Comment' AND parent_id = ? AND state >= ? AND visibility >= ?", $object->getId(), $min_state, $min_visiblity),
        'order' => 'created_on DESC',
        'offset' => 0,
        'limit' => $count,
      ));
    } // findRecentObject
    
    /**
     * Return last comment for a given object
     *
     * @param ProjectObject $object
     * @param integer $min_state
     * @param integer $min_visiblity
     * @return Comment
     */
    function findLastCommentByObject($object, $min_state = STATE_VISIBLE, $min_visiblity = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array("type = 'Comment' AND parent_id = ? AND state >= ? AND visibility >= ?", $object->getId(), $min_state, $min_visiblity),
        'order' => 'created_on DESC',
        'limit' => 1,
        'offset' => 0,
        'one' => true,
      ));
    } // findLastCommentByObject
  
  } // Comments

?>