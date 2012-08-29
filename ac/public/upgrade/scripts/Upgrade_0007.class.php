<?php

  /**
   * Upgrade latest v1.1.5 version to v2.0
   *
   * @param activeCollab.upgrade
   * @return scripts
   */
  class Upgrade_0007 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '1.1.6';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '2.0';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateExistingTables' => 'Update existing tables', 
    	  'updateParentTypes' => 'Update parent type cache',
    	  'updatePositionField' => 'Update position field value',
    	  'updateConfigOptions' => 'Create new and update existing configuration options',
    	  'updateActivityLog' => 'Update activity logs', 
    	  'updateDiscussions' => 'Update existing discussions', 
    	  'createCommentCountCache' => 'Create comment count cache', 
    	  'updateMilestoneIdCache' => 'Update milestone ID cache', 
    	  'createPagesCategories' => 'Create categories for Pages',
    	  'upgradePagesContruction' => 'Upgrade pages construction',
    	  'upgradePageRevisions' => 'Update versioned page data',
    	  'finishPagesUpgrade' => 'Finish upgrade of Pages module',
    	  'migrateAttachments' => 'Migrate attachments to a new table',
    	);
    } // getActions
    
    /**
     * Update existing tables
     *
     * @param void
     * @return boolean
     */
    function updateExistingTables() {
    	$changes = array(
    	  "alter table " . TABLE_PREFIX . "companies add is_archived tinyint(1) unsigned not null default '0'",
    	  "alter table " . TABLE_PREFIX . "projects change id id int(10) unsigned not null auto_increment",
    	  "alter table " . TABLE_PREFIX . "project_objects add source varchar(50) default null after type",
    	  "alter table " . TABLE_PREFIX . "project_objects change position position int unsigned null default null",
    	);
    	
    	foreach($changes as $change) {
    	  $update = $this->utility->db->execute($change);
    	  if(is_error($update)) {
    	    return $update->getMessage();
    	  } // if
    	} // foreach
    	
    	return true;
    } // updateExistingTables
    
    /**
     * Update position field
     *
     * @param void
     * @return boolean
     */
    function updatePositionField() {
      $this->utility->db->execute("update " . TABLE_PREFIX . "project_objects set position = NULL where position = '0'");
      return true;
    } // updatePositionField
    
    /**
     * Create new configuration options
     *
     * @param void
     * @return boolean
     */
    function updateConfigOptions() {
      $config_options_table = TABLE_PREFIX . 'config_options';
      $assignment_filters_table = TABLE_PREFIX . 'assignment_filters';
      $user_config_options_table = TABLE_PREFIX . 'user_config_options';
      
      $this->utility->db->execute("INSERT INTO $config_options_table 
        (name, module, type, value) VALUES 
        ('on_logout_url', 'system', 'system', 'N;'), 
        ('maintenance_enabled', 'system', 'system', 'b:0;'), 
        ('maintenance_message', 'system', 'system', 'N;'), 
        ('welcome_message', 'system', 'user', 'N;')"
      );
      
      // Lets make sure that default assignment filter is not private filter
      $row = $this->utility->db->execute_one("SELECT value FROM $config_options_table WHERE name = ?", array('default_assignments_filter'));
      if($row && isset($row['value'])) {
        $default_filter_id = (integer) unserialize($row['value']);
        if($default_filter_id) {
          $this->utility->db->execute("UPDATE $assignment_filters_table SET is_private = ? WHERE id = ?", array(false, $default_filter_id));
        } // if
      } // if
      
      // Reset default theme value
      $row = $this->utility->db->execute_one("SELECT value FROM $config_options_table WHERE name = ?", array('theme'));
      if($row && isset($row['value'])) {
        $this->utility->db->execute("DELETE FROM $user_config_options_table WHERE name = ? AND value = ?", array('theme', $row['value']));
      } // if
      
      return true;
    } // updateConfigOptions
    
    /**
     * Upgrade activity log
     *
     * @param void
     * @return boolean
     */
    function updateActivityLog() {
      $activity_logs_table = TABLE_PREFIX . 'activity_logs';
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      $discussion_ids = array();
      $file_ids = array();
      $page_ids = array();
      $task_ids = array();
      $comment_ids = array();
      $time_record_ids = array();
      $repository_ids = array();
      
      $rows = $this->utility->db->execute_all("SELECT id, LOWER(type) AS 'type' FROM $project_objects_table WHERE type IN (?)", array(array('discussion', 'file', 'page', 'task', 'comment', 'timerecord', 'repository')));
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          switch($row['type']) {
            case 'discussion':
              $discussion_ids[] = (integer) $row['id'];
              break;
            case 'file':
              $file_ids[] = (integer) $row['id'];
              break;
            case 'page':
              $page_ids[] = (integer) $row['id'];
              break;
            case 'task':
              $task_ids[] = (integer) $row['id'];
              break;
            case 'comment':
              $comment_ids[] = (integer) $row['id'];
              break;
            case 'timerecord':
              $time_record_ids[] = (integer) $row['id'];
              break;
            case 'repository':
              $repository_ids[] = (integer) $row['id'];
              break;
          } // switch
        } // foreach
      } // if
      
      $this->utility->db->execute("alter table $activity_logs_table add type varchar(50) not null default 'ActivityLog' after id");
      
      // Discussions
      if(is_foreachable($discussion_ids)) {
        $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE object_id IN (?) AND action = ?", array('DiscussionPinnedActivityLog', $discussion_ids, 'Pinned'));
        $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE object_id IN (?) AND action = ?", array('DiscussionUnpinnedActivityLog', $discussion_ids, 'Unpinned'));
      } // if
      
      // Files
      if(is_foreachable($file_ids)) {
        $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE object_id IN (?) AND action = ?", array('NewFileActivityLog', $file_ids, 'Created'));
        $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE object_id IN (?) AND action = ?", array('NewFileVersionActivityLog', $file_ids, 'New version'));
      } // if
      
      // Pages
      if(is_foreachable($page_ids)) {
        $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE object_id IN (?) AND action = ?", array('NewPageVersionActivityLog', $page_ids, 'New version'));
      } // if
      
      // Tasks
      if(is_foreachable($task_ids)) {
        $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE object_id IN (?) AND action = ?", array('NewTaskActivityLog', $task_ids, 'Created'));
      } // if
      
      // Comments
      if(is_foreachable($comment_ids)) {
        $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE object_id IN (?) AND action = ?", array('NewCommentActivityLog', $comment_ids, 'Created'));
      } // if
      
      // Timerecords
      if(is_foreachable($time_record_ids)) {
        $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE object_id IN (?) AND action = ?", array('TimeAddedActivityLog', $time_record_ids, 'Created'));
      } // if
      
      // Repositories
      if(is_foreachable($repository_ids)) {
        $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE object_id IN (?) AND action = ?", array('RepositoryCreatedActivityLog', $repository_ids, 'Created'));
        $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE object_id IN (?) AND action = ?", array('RepositoryUpdateActivityLog', $repository_ids, 'Updated'));
      } // if
      
      //  Everything else
      $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE type = 'ActivityLog' AND action = ?", array('ObjectCreatedActivityLog', 'Created'));
      $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE type = 'ActivityLog' AND action = ?", array('ObjectUpdatedActivityLog', 'Updated'));
      $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE type = 'ActivityLog' AND action = ?", array('ObjectTrashedActivityLog', 'Moved to Trash'));
      $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE type = 'ActivityLog' AND action = ?", array('ObjectRestoredActivityLog', 'Restored from Trash'));
      $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE type = 'ActivityLog' AND action = ?", array('TaskCompletedActivityLog', 'Completed'));
      $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE type = 'ActivityLog' AND action = ?", array('TaskReopenedActivityLog', 'Reopened'));
      $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE type = 'ActivityLog' AND action = ?", array('CommentsLockedActivityLog', 'Locked'));
      $this->utility->db->execute("UPDATE $activity_logs_table SET type = ? WHERE type = 'ActivityLog' AND action = ?", array('CommentsUnlockedActivityLog', 'Unlocked'));
      
      // And finally, drop action field
      $this->utility->db->execute("alter table $activity_logs_table drop action");
      
      return true;
    } // updateActivityLog
    
    /**
     * Update discussions and first comment problems
     *
     * @param void
     * @return boolean
     */
    function updateDiscussions() {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $activity_logs_table   = TABLE_PREFIX . 'activity_logs';
      $search_index_table    = TABLE_PREFIX . 'search_index';
      $starred_objects_table = TABLE_PREFIX . 'starred_objects';
      
      $rows = $this->utility->db->execute_all("SELECT DISTINCT id, name FROM $project_objects_table WHERE type = ?", array('Discussion'));
      if(is_foreachable($rows)) {
        $first_comment_ids = array();
        foreach($rows as $row) {
          $discussion_id = (integer) $row['id'];
          $discussion_name = trim($row['name']);
          
          $first_comment = $this->utility->db->execute_one("SELECT id, body FROM $project_objects_table WHERE parent_id = ? AND type = ? ORDER BY created_on LIMIT 0, 1", array($discussion_id, 'Comment'));
          if($first_comment) {
            $first_comment_id = (integer) $first_comment['id'];
            $first_comment_body = trim($first_comment['body']);
            
            $first_comment_ids[] = $first_comment_id;
          } else {
            $first_comment_id = 0;
            $first_comment_body = 'INFO: First discussion comment not found';
          } // if
          
          $this->utility->db->execute("UPDATE $project_objects_table SET body = ? WHERE id = ?", array($first_comment_body, $discussion_id)); // set body
          $this->utility->db->execute("UPDATE $project_objects_table SET parent_id = ? WHERE parent_id = ?", array($discussion_id, $first_comment_id)); // update comment attachments relation
          $this->utility->db->execute("UPDATE $search_index_table SET content = ? WHERE object_id = ?", array("$discussion_name\n\n$first_comment_body", $discussion_id)); // update search index
        } // foreach
        
        if(is_foreachable($first_comment_ids)) {
          $this->utility->db->execute("DELETE FROM $activity_logs_table WHERE object_id IN (?)", array($first_comment_ids)); // clear activity logs
          $this->utility->db->execute("DELETE FROM $starred_objects_table WHERE object_id IN (?)", array($first_comment_ids)); // clear starred objects
          $this->utility->db->execute("DELETE FROM $search_index_table WHERE object_id IN (?)", array($first_comment_ids)); // clear search index
          $this->utility->db->execute("DELETE FROM $project_objects_table WHERE id IN (?)", array($first_comment_ids)); // remove first comment
        } // if
        
        $this->utility->db->execute("UPDATE $project_objects_table SET varchar_field_1 = NULL, varchar_field_2 = NULL, integer_field_1 = NULL, integer_field_2 = NULL WHERE type = ?", array('Discussion')); // reset last comment data
      } // if
      
      return true;
    } // updateDiscussions
    
    /**
     * Update comments count cache
     *
     * @param void
     * @return boolean
     */
    function createCommentCountCache() {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      // Add cache field
      $update = $this->utility->db->execute("alter table $project_objects_table add comments_count smallint unsigned null default null after has_time");
      if($update && !is_error($update)) {
        $rows = $this->utility->db->execute_all("SELECT parent_id, COUNT(id) AS 'comments_count' FROM $project_objects_table WHERE type = ? GROUP BY parent_id", array('Comment'));
        if(is_foreachable($rows)) {
          $comment_counts = array();
          foreach($rows as $row) {
            $comment_counts[(integer) $row['parent_id']] = (integer) $row['comments_count'];
          } // foreach
        } else {
          $comment_counts = null;
        } // if
        
        foreach($comment_counts as $id => $comment_count) {
          $this->utility->db->execute("UPDATE $project_objects_table SET comments_count = ? WHERE id = ?", array($comment_count, $id));
        } // foreach
      } else {
        return $update;
      } // if
      
      return true;
    } // createCommentCountCache
    
    /**
     * Update cached milestone ID for tasks based on parent milestone ID
     *
     * @param void
     * @return boolean
     */
    function updateMilestoneIdCache() {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      $rows = $this->utility->db->execute_all("SELECT id, milestone_id FROM $project_objects_table WHERE type IN (?)", array(array('Checklist', 'Files', 'Discussions', 'Ticket', 'Page')));
      
      if(is_foreachable($rows)) {
        $milestone_objects_map = array();
        foreach($rows as $row) {
          $milestone_id = (integer) $row['milestone_id'];
          if(!isset($milestone_objects_map[$milestone_id])) {
            $milestone_objects_map[$milestone_id] = array();
          } // if
          
          $milestone_objects_map[$milestone_id][] = (integer) $row['id'];
        } // foreach
        
        foreach($milestone_objects_map as $milestone_id => $object_ids) {
          $this->utility->db->execute("UPDATE $project_objects_table SET milestone_id = ? WHERE type IN (?) AND parent_id IN (?)", array($milestone_id, array('Task', 'Comment', 'TimeRecord'), $object_ids));
        } // foreach
      } // if
      
      return true;
    } // updateMilestoneIdCache
    
    /**
     * Upgrade pages module
     *
     * @param void
     * @return null
     */
    function createPagesCategories() {
      $pages_installed = (boolean) array_var($this->utility->db->execute_one("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . "modules WHERE name = 'pages'"), 'row_count');
      if($pages_installed) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        $projects_table = TABLE_PREFIX . 'projects';
        $config_options_table = TABLE_PREFIX . 'config_options';
        
        $default_categories = array('General');
        
        // Create pages categories configuration option
        $this->utility->db->execute("INSERT INTO $config_options_table (name, module, type, value) VALUES ('pages_categories', 'pages', 'system', ?)", array(serialize($default_categories)));
        
        // Create default pages for projects
        $project_rows = $this->utility->db->execute_all("SELECT id, created_on, created_by_id, created_by_name, created_by_email FROM $projects_table");
        if(is_foreachable($project_rows)) {
          $to_insert = array();
          foreach($project_rows as $project_row) {
            $to_insert[] = $this->utility->db->prepareSQL('(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array('Category', 'pages', $project_row['id'], 'General', 3, 1, $project_row['created_on'], $project_row['created_by_id'], $project_row['created_by_name'], $project_row['created_by_email'], 'pages'));
          } // foreach
          $this->utility->db->execute("INSERT INTO $project_objects_table (type, module, project_id, name, state, visibility, created_on, created_by_id, created_by_name, created_by_email, varchar_field_1) VALUES " . implode(', ', $to_insert));
        } // if
      } // if
      
      return true;
    } // createPagesCategories
    
    /**
     * Upgrade root pages
     *
     * @param void
     * @return boolean
     */
    function upgradePagesContruction() {
      $pages_installed = (boolean) array_var($this->utility->db->execute_one("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . "modules WHERE name = 'pages'"), 'row_count');
      if($pages_installed) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        
        $project_category_map = array();
        
        $category_rows = $this->utility->db->execute_all("SELECT id, project_id FROM $project_objects_table WHERE type = ? AND module = ?", array('Category', 'pages'));
        if(is_foreachable($category_rows)) {
          foreach($category_rows as $category_row) {
            $project_category_map[(integer) $category_row['project_id']] = (integer) $category_row['id'];
          } // foreach
        } // if
        
        $root_page_rows = $this->utility->db->execute_all("SELECT id, project_id FROM $project_objects_table WHERE type = ? AND boolean_field_1 = ?", array('Page', true));
        if(is_foreachable($root_page_rows)) {
          foreach($root_page_rows as $root_page_row) {
            $page_id = (integer) $root_page_row['id'];
            $project_id = (integer) $root_page_row['project_id'];
            
            if(isset($project_category_map[$project_id])) {
              $this->utility->db->execute("UPDATE $project_objects_table SET parent_id = ? WHERE id = ?", array($project_category_map[$project_id], $page_id));
            } // if
          } // if
        } // if
      } // if
      return true;
    } // upgradePagesContruction
    
    /**
     * Upgrade page revisions data
     *
     * @param void
     * @return boolean
     */
    function upgradePageRevisions() {
      $pages_installed = (boolean) array_var($this->utility->db->execute_one("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . "modules WHERE name = 'pages'"), 'row_count');
      if($pages_installed) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        $page_versions_table = TABLE_PREFIX . 'page_versions';
        
        $storage_engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'ENGINE=InnoDB' : '';
        $default_charset = defined('DB_CHARSET') && (DB_CHARSET == 'utf8') ? 'DEFAULT CHARSET=utf8' : '';
      
        $this->utility->db->execute("CREATE TABLE $page_versions_table (
          page_id int(10) unsigned NOT NULL default '0',
          version smallint(5) unsigned NOT NULL default '0',
          name varchar(255) NOT NULL default '',
          body longtext,
          created_on datetime default NULL,
          created_by_id smallint(5) unsigned default NULL,
          created_by_name varchar(100) default NULL,
          created_by_email varchar(100) default NULL,
          PRIMARY KEY  (page_id,version)
        ) $storage_engine $default_charset;");
        
        $revision_rows = $this->utility->db->execute("SELECT id, parent_id, name, body, created_on, created_by_id, created_by_name, created_by_email, integer_field_1 FROM $project_objects_table WHERE type = ? AND boolean_field_2 = ?", array('Page', true));
        if(is_foreachable($revision_rows)) {
          $revision_ids = array(); // collect ID-s so we can drop these rows later on
          foreach($revision_rows as $revision_row) {
            $revision_ids[] = (integer) $revision_row['id'];
            $this->utility->db->execute("INSERT INTO $page_versions_table (page_id, version, name, body, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", array(
              $revision_row['parent_id'],
              $revision_row['integer_field_1'],
              $revision_row['name'],
              $revision_row['body'],
              $revision_row['created_on'],
              $revision_row['created_by_id'],
              $revision_row['created_by_name'],
              $revision_row['created_by_email'],
            ));
          } // foreach
          
          if(is_foreachable($revision_ids)) {
            $this->utility->db->execute("DELETE FROM $project_objects_table WHERE id IN (?)", array($revision_ids));
            $this->utility->db->execute("DELETE FROM " . TABLE_PREFIX . "activity_logs WHERE object_id IN (?)", array($revision_ids));
            $this->utility->db->execute("DELETE FROM " . TABLE_PREFIX . "starred_objects WHERE object_id IN (?)", array($revision_ids));
            $this->utility->db->execute("DELETE FROM " . TABLE_PREFIX . "search_index WHERE object_id IN (?) AND type = ?", array($revision_ids, 'Page'));
          } // if
        } // if
      } // if
      return true;
    } // upgradePageRevisions
    
    /**
     * Finish pages upgrade
     *
     * @param void
     * @return boolean
     */
    function finishPagesUpgrade() {
      $pages_installed = (boolean) array_var($this->utility->db->execute_one("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . "modules WHERE name = 'pages'"), 'row_count');
      if($pages_installed) {
        $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET boolean_field_1 = NULL, boolean_field_2 = NULL WHERE type = ?', array('Page')); // Reset flags
      } // if
      return true;
    } // finishPagesUpgrade
    
    /**
     * Migrate attachments to new table
     * 
     * @param void
     * @return null
     */
    function migrateAttachments() {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';
      $charset = $this->utility->db->supportsCollation() ? 'default character set utf8 COLLATE utf8_general_ci' : '';
      
      $this->utility->db->beginWork();
      
      $result = $this->utility->db->execute("CREATE TABLE " . TABLE_PREFIX . "attachments (
          id int(10) unsigned NOT NULL auto_increment,
          parent_id int(10) unsigned default NULL,
          parent_type varchar(30) default NULL,
          name varchar(150) default NULL,
          mime_type varchar(100) NOT NULL default 'application/octet-stream',
          size int(10) unsigned NOT NULL default '0',
          location varchar(50) default NULL,
          attachment_type enum('attachment','file_revision') NOT NULL default 'attachment',
          created_on datetime default NULL,
          created_by_id smallint(5) unsigned default NULL,
          created_by_name varchar(100) default NULL,
          created_by_email varchar(100) default NULL,
          PRIMARY KEY  (id),
          KEY parent_id (parent_id),
          KEY created_on (created_on)
        ) ENGINE=$engine $charset");
      
      if (!$result || is_error($result)) {
        return $result->getMessage();
      } // if
     
      // read attachments from project_objects table 
      $attachments = $this->utility->db->execute('SELECT id,parent_id, parent_type, name,varchar_field_2 AS mime_type, integer_field_1 AS size, varchar_field_1 AS location, created_on, created_by_id, created_by_name, created_by_email FROM ' . TABLE_PREFIX . 'project_objects WHERE type=?', array('Attachment'));
     
      // copy data from project_objects table to attachments table
      if (is_foreachable($attachments)) {
        $insert_query_start = 'INSERT INTO '.TABLE_PREFIX.'attachments (id, parent_id, parent_type, name, mime_type, size, location, attachment_type,created_on, created_by_id, created_by_name, created_by_email) VALUES '."\n";
        
        $insert_attachments = array(); $project_object_ids = array(); $counter = 0;
        
        foreach ($attachments as $attachment) {
          $insert_attachments[floor($counter / 22)][] = $this->utility->db->prepareSQL('(?,?,?,?,?,?,?,?,?,?,?,?)', array($attachment['id'],$attachment['parent_id'],$attachment['parent_type'], $attachment['name'],$attachment['mime_type'],$attachment['size'],$attachment['location'],strtolower($attachment['parent_type']) == 'file' ? 'file_revision': 'attachment',$attachment['created_on'],$attachment['created_by_id'],$attachment['created_by_name'],$attachment['created_by_email']));
          $project_object_ids[] = $attachment['id'];
          $counter ++;
        } // foreach
        
        if (is_foreachable($insert_attachments)) {
          foreach ($insert_attachments as $insert_group) {
            $insert_query = $insert_query_start . implode(",\n", $insert_group).';';
            $result = $this->utility->db->execute($insert_query);
            if (!$result || is_error($result)) {
              $this->utility->db->rollback();
              return $result->getMessage();
            } // if
          } // foreach
        } // if
               
        // delete data from projects_objects table
        $delete_result = $this->utility->db->execute('DELETE FROM ' . TABLE_PREFIX . 'project_objects WHERE id IN (?)', array($project_object_ids));
        if ($result && !is_error($result)) {
          $this->utility->db->commit();
        } else {
          $this->utility->db->rollback();
          return $result->getMessage();
        } // if
      } // if
      return true;
    } //migrateAttachments
    
    /**
     * Update project type values
     *
     * @param void
     * @return boolean
     */
    function updateParentTypes() {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      $rows = $this->utility->db->execute_all("SELECT DISTINCT parent_id FROM $project_objects_table WHERE parent_id > '0' AND parent_type IS NULL");
      if(is_foreachable($rows)) {
        $parent_ids = array();
        foreach($rows as $row) {
          $parent_ids[] = (integer) $row['parent_id'];
        } // foreach
        
        $parent_rows = $this->utility->db->execute_all("SELECT id, type FROM $project_objects_table WHERE id IN (?)", array($parent_ids));
        if(is_foreachable($parent_rows)) {
          foreach($parent_rows as $row) {
            if($row['type']) {
              $this->utility->db->execute("UPDATE $project_objects_table SET parent_type = ? WHERE parent_id = ?", array($row['type'], $row['id']));
            } // if
          } // foreach
        } // if
      } // if
      
      return true;
    } // updateParentTypes
    
  }

?>