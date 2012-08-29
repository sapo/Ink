<?php

  /**
   * Attachments manager class
   *
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class Attachments extends BaseAttachments {
    
    /**
    * Return attachments by parent object
    *
    * @param ApplicationObject $object
    * @return array
    */
    function findByObject($object) {
      return Attachments::find(array(
        'conditions' => array('parent_id = ? AND parent_type = ?', $object->getId(), get_class($object)),
        'order' => 'created_on',
      ));
    } // findByObject
    
    /**
     * Find attachments by ids
     *
     * @param array $ids
     * @param integer $min_state
     * @param integer $min_visibility
     */
    function findByIds($ids, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      $attachments_table = TABLE_PREFIX . 'attachments';
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      $total = array_var(db_execute_one("SELECT COUNT($attachments_table.id) AS 'row_count' FROM $attachments_table, $project_objects_table WHERE $attachments_table.attachment_type = ? AND $attachments_table.parent_id = $project_objects_table.id AND $attachments_table.id IN (?)  AND $project_objects_table.state >= ? AND $project_objects_table.visibility >= ?", ATTACHMENT_TYPE_ATTACHMENT ,$ids, $min_state, $min_visibility), 'row_count');
      if (!$total) {
        return null;
      };
      return Attachments::findBySQL("SELECT $attachments_table.* FROM $attachments_table, $project_objects_table WHERE $attachments_table.attachment_type = ? AND $attachments_table.parent_id = $project_objects_table.id AND $attachments_table.id IN (?)  AND $project_objects_table.state >= ? AND $project_objects_table.visibility >= ?", array(ATTACHMENT_TYPE_ATTACHMENT ,$ids, $min_state, $min_visibility));
    } // findbyids
    
    /**
     * Paginate attachments by project
     *
     * @param Project $project
     * @param User $user
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @return array
     */
    function paginateByProject($project, $user, $page = 1, $per_page = 30, $min_state = STATE_VISIBLE) {
      $attachments_table = TABLE_PREFIX . 'attachments';
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      $type_filter = ProjectUsers::getVisibleTypesFilterByProject($user, $project);
      if($type_filter) {
        $total = array_var(db_execute_one("SELECT COUNT($attachments_table.id) AS 'row_count' FROM $attachments_table, $project_objects_table WHERE $attachments_table.attachment_type = ? AND $attachments_table.parent_id = $project_objects_table.id AND $type_filter AND $project_objects_table.state >= ? AND $project_objects_table.visibility >= ?", ATTACHMENT_TYPE_ATTACHMENT, $min_state, $user->getVisibility()), 'row_count');
        if($total) {
          $offset = ($page - 1) * $per_page;
          $attachments = Attachments::findBySQL("SELECT $attachments_table.* FROM $attachments_table, $project_objects_table WHERE $attachments_table.attachment_type = ? AND $attachments_table.parent_id = $project_objects_table.id AND $type_filter AND $project_objects_table.state >= ? AND $project_objects_table.visibility >= ? ORDER BY `created_on` DESC LIMIT $offset, $per_page", array(ATTACHMENT_TYPE_ATTACHMENT, $min_state, $user->getVisibility()));
          if($attachments) {
            return array($attachments, new Pager($page, $total, $per_page));
          } // if
        } // if
      } // if
      
      return array(null, new Pager(1, 0, $per_page));
    } // paginateByProject

    /**
     * Find last attachment for a given object
     *
     * @param ApplicationObject $object
     * @return Attachment
     */
    function findLastByObject($object) {
    	return Attachments::find(array(
        'conditions' => array('parent_id = ? AND parent_type = ?', $object->getId(), get_class($object)),
        'order' => 'created_on DESC',
        'limit' => 1,
        'one' => true,
      ));
    } // findLastByObject
    
    /**
    * Return number of attachments for a given object
    *
    * @param ProjectObject $object
    * @return integer
    */
    function countByObject($object) {
      return Attachments::count(array('parent_id = ? AND parent_type = ?', $object->getId(), get_class($object)));
    } // countByObject
    
    /**
     * DElete by object
     *
     * @param ProjectObject $object
     * @return boolean
     */
    function deleteByObject($object) {
      return Attachments::delete(array('parent_id = ? AND parent_type = ?', $object->getId(), get_class($object)));
    } // deleteByObject
    
    /**
     * Delete attachment by project object ID-s
     *
     * @param array $ids
     * @return boolean
     */
    function deleteByProjectObjectIds($ids) {
      if(is_foreachable($ids)) {
        $attachments_table = TABLE_PREFIX . 'attachments';
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        
        $rows = db_execute_all("SELECT $attachments_table.id AS 'id' FROM $attachments_table, $project_objects_table WHERE $attachments_table.parent_id = $project_objects_table.id AND $attachments_table.parent_type = $project_objects_table.type AND $project_objects_table.id IN (?)", $ids);
        if(is_foreachable($rows)) {
          $attachment_ids = array();
          foreach($rows as $row) {
            $attachment_ids[] = (integer) $row['id'];
          } // if
          
          return Attachments::delete(array('id IN (?)', $attachment_ids));
        } // if
      } // if
      return true;
    } // deleteByProjectObjectids
    
    /**
     * Delete records from attachments table that match given $conditions
     * 
     * This function also deletes all files from /upload folder so this function 
     * is not 100% transaction safe
     *
     * @param mixed $conditions
     * @return boolean
     */
    function delete($conditions = null) {
      $attachments_table = TABLE_PREFIX . 'attachments';
      
      $perpared_conditions = DataManager::prepareConditions($conditions);
      $where_string = trim($perpared_conditions) == '' ? '' : "WHERE $perpared_conditions";
      
      $rows = db_execute("SELECT id, location FROM $attachments_table $where_string");
      if(is_foreachable($rows)) {
        $attachments = array();
        foreach($rows as $row) {
          if($row['location']) {
            $attachments[(integer) $row['id']] = $row['location'];
          } // if
        } // foreach
        
        // Delete attachments
        $delete = db_execute("DELETE FROM $attachments_table WHERE id IN (?)", array_keys($attachments));
        if($delete && !is_error($delete)) {
          foreach($attachments as $location) {
            @unlink(UPLOAD_PATH . '/' . $location);
          } // foreach
        } // if
        return $delete;
      } // if
      
      return true;
    } // delete
    
    /**
     * Clone attachments
     *
     * @param ProjectObject $from
     * @param ProjectObject $to
     * @return boolean
     */
    function cloneAttachments($original, $copy) {
      $attachments = Attachments::findByObject($original);
      if(is_foreachable($attachments)) {
        $new_files = array();
        $to_insert = array();
        foreach($attachments as $attachment) {
          $source_file = $attachment->getFilePath();
          $target_file = get_available_uploads_filename();
          
          if(copy($source_file, $target_file)) {
            $new_files[] = $target_file;
            $to_insert[] = db_prepare_string("(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array($copy->getId(), get_class($copy), $attachment->getName(), $attachment->getMimeType(), $attachment->getSize(), basename($target_file), $attachment->getAttachmentType(), $attachment->getCreatedOn(), $attachment->getCreatedById(), $attachment->getCreatedByName(), $attachment->getCreatedByEmail()));
          } // if
        } // foreach
        
        if(is_foreachable($to_insert)) {
          $insert = db_execute("INSERT INTO " . TABLE_PREFIX . 'attachments (parent_id, parent_type, name, mime_type, size, location, attachment_type, created_on, created_by_id, created_by_name, created_by_email) VALUES ' . implode(', ', $to_insert));
          if($insert && !is_error($insert)) {
            return true;
          } else {
            foreach($new_files as $new_file) {
              @unlink($new_file);
            } // if
            
            return $insert;
          } // if
        } // if
      } // if
      
      return true;
    } // cloneAttachments
    
  }

?>