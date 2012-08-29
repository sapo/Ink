<?php

  /**
   * Upgrade activeCollab 1.0.x to 1.1
   * 
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0001 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '1.0';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '1.1';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'createNewTables' => 'Create new tables',
    	  'updateExistingTables' => 'Update existing tables',
    	  'refreshInitialData' => 'Insert initial data in new tables',
    	  'updateModuleDetails' => 'Update module details',
    	  'updateSystemRoles' => 'Update system roles',
    	  'updateProjectPermissions' => 'Update project permissions',
    	  'updateActivityLog' => 'Update activity log',
    	  'rebuildSearchIndex' => 'Rebuild search index',
    	  'rebuildParentType' => 'Build parent_type values',
    	  'moveTimeRecordData' => 'Update time record date data',
    	  'rebuildHasTime' => 'Rebuild has_time caches',
    	  'digestUserPasswords' => 'Encrypt user passwords',
    	);
    } // getActions
    
    // ---------------------------------------------------
    //  Utility functions
    // ---------------------------------------------------
    
    /**
     * Create new tables
     *
     * @param void
     * @return boolean
     */
    function createNewTables() {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';
      $charset = $this->utility->db->supportsCollation() ? 'default character set utf8 COLLATE utf8_general_ci' : '';
      
    	$tables = array(
    	
    	  // assignment_filters
    	  "CREATE TABLE `" . TABLE_PREFIX . "assignment_filters` (
          `id` smallint(5) unsigned NOT NULL auto_increment,
          `name` varchar(50) NOT NULL,
          `group_name` varchar(50) NOT NULL,
          `is_private` tinyint(1) unsigned NOT NULL,
          `user_filter` enum('anybody','logged_user','logged_user_responsible','company','selected') NOT NULL default 'logged_user',
          `user_filter_data` text,
          `project_filter` enum('all','active','selected') NOT NULL,
          `project_filter_data` text,
          `date_filter` enum('all','late','today','tomorrow','this_week','next_week','this_month','next_month','selected_date','selected_range') NOT NULL default 'all',
          `date_from` date default NULL,
          `date_to` date default NULL,
          `objects_per_page` smallint(5) unsigned default NULL,
          `order_by` varchar(50) default NULL,
          `created_by_id` smallint(6) NOT NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=$engine $charset;",
    	  
    	  // email_templates
    	  "CREATE TABLE `" . TABLE_PREFIX . "email_templates` ( 
    	    `name` varchar(50) not null, 
    	    `module` varchar(50) not null, 
    	    `subject` varchar(150) not null, 
    	    `body` text, 
    	    `variables` text default NULL, 
    	    PRIMARY KEY (`name`, `module`)
    	  ) ENGINE=$engine $charset", 
    	  
    	  // email_template_translations
    	  "CREATE TABLE `" . TABLE_PREFIX . "email_template_translations` ( 
    	    `name` varchar(50) not null, 
    	    `module` varchar(50) not null, 
    	    `locale` varchar(50) not null, 
    	    `subject` varchar(150) not null, 
    	    `body` text, 
    	    PRIMARY KEY (`name`, `module`, `locale`)
    	  ) ENGINE=$engine $charset;",
    	  
    	  // languages
    	  "CREATE TABLE `" . TABLE_PREFIX . "languages` ( 
    	    `id` tinyint(3) unsigned not null auto_increment, 
    	    `name` varchar(50) not null, 
    	    `locale` varchar(30) not null, 
    	    PRIMARY KEY (`id`)
    	  ) ENGINE=$engine $charset",
    	  
    	  // project_object_views
    	  "CREATE TABLE `" . TABLE_PREFIX . "project_object_views` ( 
    	    `object_id` int(10) unsigned not null, 
    	    `created_by_id` smallint(5) unsigned not null, 
    	    `created_by_name` varchar(100) not null, 
    	    `created_by_email` varchar(150) not null, 
    	    `created_on` datetime not null, 
    	    PRIMARY KEY (`object_id`, `created_by_id`), 
    	    INDEX `created_by_id`(`created_by_id`)
    	  ) ENGINE=$engine $charset",
    	  
    	  // reminders
    	  "CREATE TABLE `" . TABLE_PREFIX . "reminders` ( 
    	    `id` int(10) unsigned not null auto_increment, 
    	    `user_id` smallint(5) unsigned not null, 
    	    `object_id` int(10) unsigned not null, 
    	    `comment` text, 
    	    `created_by_id` smallint(5) unsigned not null, 
    	    `created_by_name` varchar(100) not null, 
    	    `created_by_email` varchar(100) not null, 
    	    `created_on` datetime not null, 
    	    PRIMARY KEY (`id`), 
    	    INDEX `user_id`(`user_id`), 
    	    INDEX `created_on`(`created_on`)
    	  ) ENGINE=$engine $charset",
    	);
    	
    	  $tables[] = "CREATE TABLE `" . TABLE_PREFIX . "time_reports` (
          `id` smallint(5) unsigned NOT NULL auto_increment,
          `name` varchar(50) NOT NULL,
          `group_name` varchar(50) NOT NULL,
          `is_default` tinyint(1) unsigned NOT NULL,
          `user_filter` enum('anyone','logged_user','company','selected') NOT NULL default 'anyone',
          `user_filter_data` text,
          `billable_filter` enum('all','billable','not_billable','billable_billed','billable_not_billed') NOT NULL default 'all',
          `date_filter` enum('all','today','last_week','this_week','last_month','this_month','selected_date','selected_range') NOT NULL default 'all',
          `date_from` date default NULL,
          `date_to` date default NULL,
          `sum_by_user` tinyint(1) unsigned NOT NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=$engine $charset;";
    	
    	foreach($tables as $table) {
    	  $created = $this->utility->db->execute($table);
    	  if(is_error($created)) {
    	    return $created->getMessage();
    	  } // if
    	} // foreach
    	
    	return true;
    } // createNewTables
    
    /**
     * Update existing tables
     *
     * @param void
     * @return boolean
     */
    function updateExistingTables() {
    	$changes = array(
    	  "alter table `" . TABLE_PREFIX . "activity_logs` add column `project_id` int(10) unsigned not null default '0' after `object_id`",
    	  "alter table `" . TABLE_PREFIX . "activity_logs` add index `created_on`(`created_on`)",
    	  "alter table `" . TABLE_PREFIX . "activity_logs` add index `project_id`(`project_id`)",
    	  "alter table `" . TABLE_PREFIX . "activity_logs` drop column `modifications`",
    	  "alter table `" . TABLE_PREFIX . "companies` drop column `created_on`",
    	  "alter table `" . TABLE_PREFIX . "companies` drop column `updated_on`",
    	  "alter table `" . TABLE_PREFIX . "config_options` drop column `serializer`",
    	  "alter table `" . TABLE_PREFIX . "config_options` drop column `unserializer`",
    	  "alter table `" . TABLE_PREFIX . "pinned_projects` drop column `created_on`",
    	  "alter table `" . TABLE_PREFIX . "project_objects` add column `parent_type` varchar(30) default NULL after `parent_id`",
    	  "alter table `" . TABLE_PREFIX . "project_objects` change column `body` `body` longtext default NULL after `name`",
    	  "alter table `" . TABLE_PREFIX . "project_objects` change column `project_id` `project_id` smallint(6) unsigned not null default '0' after `module`",
    	  "alter table `" . TABLE_PREFIX . "project_objects` change column `due_on` `due_on` date default NULL after `updated_by_email`",
    	  "alter table `" . TABLE_PREFIX . "project_objects` change column `float_field_1` `float_field_1` double(10, 2) default NULL after `integer_field_2`",
    	  "alter table `" . TABLE_PREFIX . "project_objects` change column `float_field_2` `float_field_2` double(10, 2) default NULL after `float_field_1`",
    	  "alter table `" . TABLE_PREFIX . "project_objects` add column `has_time` tinyint(1) unsigned not null default '0' after `completed_by_email`",
    	  "alter table `" . TABLE_PREFIX . "project_objects` add index `created_on`(`created_on`)",
    	  "alter table `" . TABLE_PREFIX . "project_objects` add index `due_on`(`due_on`)",
    	  "alter table `" . TABLE_PREFIX . "project_objects` add index `completed_on`(`completed_on`)",
    	  "alter table `" . TABLE_PREFIX . "project_users` drop index `user_id`",
    	  "alter table `" . TABLE_PREFIX . "project_users` drop column `id`",
    	  "alter table `" . TABLE_PREFIX . "project_users` add column `role_id` tinyint(3) unsigned not null default '0' after `project_id`",
    	  "alter table `" . TABLE_PREFIX . "project_users` add column `permissions` text default null after `project_id`",
    	  "alter table `" . TABLE_PREFIX . "project_users` add primary key (`user_id`, `project_id`)",
    	  "alter table `" . TABLE_PREFIX . "projects` add column `starts_on` date default null after `default_visibility`",
    	  "alter table `" . TABLE_PREFIX . "roles` add column `type` enum('system','project') not null default 'system' after `id`",
    	  "alter table `" . TABLE_PREFIX . "roles` add column `permissions` text default null after `type`",
    	  "alter table `" . TABLE_PREFIX . "search_index` add column `type` varchar(50) not null after `object_id`",
    	  "alter table `" . TABLE_PREFIX . "search_index` change column `content` `content` longtext default NULL after `type`",
    	  "alter table `" . TABLE_PREFIX . "search_index` drop primary key",
    	  "alter table `" . TABLE_PREFIX . "search_index` add primary key (`object_id`, `type`)",
    	  "alter table `" . TABLE_PREFIX . "starred_objects` drop column `created_on`",
    	  "alter table `" . TABLE_PREFIX . "subscriptions` drop column `created_on`",
    	  "alter table `" . TABLE_PREFIX . "users` drop column `created_on`",
    	  "alter table `" . TABLE_PREFIX . "users` drop column `updated_on`",
    	  "alter table `" . TABLE_PREFIX . "users` add column `auto_assign` tinyint(1) unsigned NOT NULL default 0 after `last_activity_on`",
    	  "alter table `" . TABLE_PREFIX . "users` add column `auto_assign_role_id` tinyint(3) unsigned default NULL after `auto_assign`",
    	  "alter table `" . TABLE_PREFIX . "users` add column `auto_assign_permissions` text default NULL after `auto_assign_role_id`",
    	  "alter table `" . TABLE_PREFIX . "users` add column `password_reset_key` varchar(20) default NULL after `auto_assign_permissions`",
    	  "alter table `" . TABLE_PREFIX . "users` add column `password_reset_on` datetime default NULL after `password_reset_key`",
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
     * Refresh initial data
     *
     * @param void
     * @return boolean
     */
    function refreshInitialData() {
      $inserts = array(
        "INSERT INTO `" . TABLE_PREFIX . "assignment_filters` (`id`, `name`, `group_name`, `is_private`, `user_filter`, `user_filter_data`, `project_filter`, `project_filter_data`, `date_filter`, `date_from`, `date_to`, `objects_per_page`, `order_by`, `created_by_id`) VALUES 
          (1, 'All', 'Mine', 0, 'logged_user', 'N;', 'active', 'N;', 'all', NULL, NULL, 30, 'priority DESC', 0),
          (2, 'Today', 'Mine', 0, 'logged_user', 'N;', 'active', 'N;', 'today', NULL, NULL, 30, 'priority DESC', 0),
          (3, 'Late', 'Mine', 0, 'logged_user', 'N;', 'active', 'N;', 'late', NULL, NULL, 30, 'due_on ASC', 0);",
        "INSERT INTO `" . TABLE_PREFIX . "languages` (`id`, `name`, `locale`) VALUES (1, 'English', 'en_US.UTF-8');",
        "INSERT INTO `" . TABLE_PREFIX . "config_options` (`name`, `module`, `type`, `value`) VALUES 
          ('default_assignments_filter', 'system', 'user', 'i:1;'),
          ('default_role', 'system', 'system', 'i:0;'),
          ('format_date', 'system', 'user', 's:9:\"%b %e. %Y\";'),
          ('format_time', 'system', 'user', 's:8:\"%I:%M %p\";'),
          ('language', 'system', 'user', 'i:1;'),
          ('projects_use_client_icons', 'system', 'system', 'b:0;'),
          ('show_welcome_message', 'system', 'system', 'b:1;'), 
          ('project_templates_group', 'system', 'system', 's:0:\"\";');",
        "INSERT INTO `" . TABLE_PREFIX . "email_templates` (`name`, `module`, `subject`, `body`, `variables`) VALUES 
          ('forgot_password', 'system', 'Reset your password', '<p>Hi,</p>\n<p>Visit <a href=\":reset_url\">this page</a> to reset your password. This page will be valid for 2 days!</p>\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\nreset_url'),
          ('new_user', 'system', 'Your account has been created', '<p>Hi,</p>\n\n<p><a href=\":created_by_url\">:created_by_name</a> has created a new account for you. You can <a href=\":login_url\">log in</a> with these parameters:</p>\n\n<p>Email: \':email\' (without quotes)<br />Password: \':password\' (without quotes)</p>\n\n<hr />\n\n<p>:welcome_body</p>\n\n<hr />\n\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\ncreated_by_id\ncreated_by_name\ncreated_by_url\nemail\npassword\nlogin_url\nwelcome_body'),
          ('reminder', 'system', '[:project_name] Reminder \':object_name\' :object_type', '<p>Hi,</p>\n\n<p><a href=\":reminded_by_url\">:reminded_by_name</a> wants you to check out <a href=\":object_url\">:object_name</a> :object_type. Comment:</p>\n\n<hr />\n\n<p>:comment_body</p>\n\n<hr />\n\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\nreminded_by_name\nreminded_by_url\nobject_name\nobject_url\nobject_type\ncomment_body\nproject_name\nproject_url'),
          ('new_comment', 'resources', '[:project_name] New comment on \':object_name\' :object_type', '<p>Hi,</p>\n\n<p><a href=\":created_by_url\">:created_by_name</a> has replied to <a href=\":object_url\">:object_name</a> :object_type in <a href=\":project_url\">:project_name</a> project:</p>\n\n<hr />\n\n<p>:comment_body</p>\n\n<hr />\n\n<p><a href=\":comment_url\">Click here</a> for more details.</p>\n\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\nproject_name\nproject_url\nobject_type\nobject_name\nobject_body\nobject_url\ncomment_body\ncomment_url\ncreated_by_url\ncreated_by_name'),
          ('task_assigned', 'resources', '[:project_name] A task has been assigned to you', '<p>Hi,</p>\n\n<p>We have a new assignment for you: :object_type <a href=\":object_url\">:object_name</a> in <a href=\":project_url\">:project_name</a> project. This :object_type has been created by <a href=\":created_by_url\">:created_by_name</a>. It says:</p>\n\n<hr />\n\n<p style=\"padding: 10px 20px;\">:object_body</p>\n\n<hr />\n\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\nproject_name\nproject_url\nobject_type\nobject_name\nobject_body\nobject_url\ncreated_by_name\ncreated_by_url'),
          ('task_reassigned', 'resources', '[:project_name] Task reassigned', '<p>Hi,</p>\n\n<p>We have an update that you might be interested in: :object_type <a href=\":object_url\">:object_name</a> from <a href=\":project_url\">:project_name</a> project has been updated. Changes:\n\n:changes_body\n\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\nproject_name\nproject_url\nobject_type\nobject_name\nobject_body\nobject_url\nchanges_body'),
          ('task_completed', 'resources', '[:project_name] \':object_name\' :object_type has been completed', '<p>Hi,</p>\n\n<p><a href=\":completed_by_url\">:completed_by_name</a> has completed :object_type <a href=\":object_url\">:object_name</a> in <a href=\":project_url\">:project_name</a>  project.</p>\n\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\nproject_name\nproject_url\nobject_type\nobject_name\nobject_body\nobject_url\ncreated_by_name\ncreated_by_url\ncompleted_by_name\ncompleted_by_url'),
          ('task_reopened', 'resources', '[:project_name] \':object_name\' :object_type has been reopened', '<p>Hi,</p>\n\n<p><a href=\":reopened_by_url\">:reopened_by_name</a> has reopened :object_type <a  href=\":object_url\">:object_name</a> in <a href=\":project_url\">:project_name</a>  project.</p>\n\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\nproject_name\nproject_url\nobject_type\nobject_name\nobject_body\nobject_url\ncreated_by_name\ncreated_by_url\nreopened_by_name\nreopened_by_url'),
          ('new_discussion', 'discussions', '[:project_name] Discussion \':object_name\' has been started', '<p>Hi,</p>\n<p><a href=\":created_by_url\">:created_by_name</a> has started a new discussion in <a href=\":project_url\">:project_name</a> project.</p>\n<hr />\n<h1>:object_name</h1>\n<p>:last_comment_body</p>\n<hr />\n<p><a href=\":object_url\">Click here</a> for more details</p>\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\nproject_name\nproject_url\nobject_type\nobject_name\nobject_body\nobject_url\ncreated_by_name\ncreated_by_url\nlast_comment_body'),
          ('new_file', 'files', '[:project_name] File \':object_name\' has been uploaded', '<p>Hi,</p>\n\n<p><a href=\":created_by_url\">:created_by_name</a> has uploaded a new file in <a href=\":project_url\">:project_name</a> project.</p>\n\n<p><a href=\":object_url\">Click here</a> for more details</p>\n\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\nproject_name\nproject_url\nobject_type\nobject_name\nobject_body\nobject_url\ncreated_by_name\ncreated_by_url'),
          ('new_revision', 'files', '[:project_name] New version of \':object_name\' file is up', '<p>Hi,</p>\n<p><a href=\":created_by_url\">:created_by_name</a> has uploaded a new version of <a href=\":object_url\">:object_name</a> file in <a href=\":project_url\">:project_name</a> project.</p>\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\nproject_name\nproject_url\nobject_type\nobject_name\nobject_body\nobject_url\ncreated_by_url\ncreated_by_name');"
      );

        $inserts[] = "INSERT INTO `" . TABLE_PREFIX . "time_reports` (`id`, `name`, `group_name`, `is_default`, `user_filter`, `user_filter_data`, `billable_filter`, `date_filter`, `date_from`, `date_to`, `sum_by_user`) VALUES 
          (1, 'Last week', 'General', 1, 'anyone', 'N;', 'all', 'last_week', NULL, NULL, 0),
          (2, 'Last week, summarized', 'General', 0, 'anyone', 'N;', 'all', 'last_week', NULL, NULL, 1),
          (3, 'Last month', 'General', 0, 'anyone', 'N;', 'all', 'last_month', NULL, NULL, 0),
          (4, 'Last month, summarized', 'General', 0, 'anyone', 'N;', 'all', 'last_month', NULL, NULL, 1);";
        $inserts[] = "INSERT INTO `" . TABLE_PREFIX . "email_templates` (`name`, `module`, `subject`, `body`, `variables`) VALUES 
          ('new_page', 'pages', '[:project_name] Page \':object_name\' has been created', '<p>Hi,</p>\n\n<p><a href=\":created_by_url\">:created_by_name</a> has created a new page in <a href=\":project_url\">:project_name</a> project - <a  href=\":object_url\">:object_name</a>. Content:</p>\n\n<hr />\n\n<p>:object_body</p>\n\n<hr />\n\n<p><a href=\":object_url\">Click here</a> for more details</p>\n\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\nproject_name\nproject_url\nobject_type\nobject_name\nobject_body\nobject_url\ncreated_by_name\ncreated_by_url'),
          ('new_revision', 'pages', '[:project_name] Revision #:revision_num of \':old_name\' page is up', '<p>Hi,</p>\n\n<p><a href=\":created_by_url\">:created_by_name</a> has created a new version of <a href=\":old_url\">:old_name</a> page in <a href=\":project_url\">:project_name</a> project. New content:</p>\n\n<hr />\n\n<p>:new_body</p>\n\n<hr />\n\n<p>Best,<br />:owner_company_name</p>', 'owner_company_name\nproject_name\nproject_url\nobject_type\nobject_name\nobject_body\nobject_url\ncreated_by_url\ncreated_by_name\nrevision_num\nold_url\nold_name\nold_body\nnew_url\nnew_name\nnew_body');";
      
      foreach($inserts as $insert) {
        $inserted = $this->utility->db->execute($insert);
        if(is_error($inserted)) {
          return $inserted->getMessage();
        } // if
      } // foreach
      
      return true;
    } // refreshInitialData
    
    /**
     * Update module details
     *
     * @param void
     * @return null
     */
    function updateModuleDetails() {
    	$update = $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'modules SET is_system = ? WHERE name NOT IN (?)', array(false, array('system', 'resources', 'milestones')));
    	return is_error($update) ? $update->getEmessage() : true;
    } // updateModuleDetails
    
    /**
     * Update activity log
     *
     * @param void
     * @return null
     */
    function updateActivityLog() {
    	$all_ids = $this->utility->db->execute('SELECT id, project_id FROM ' . TABLE_PREFIX . 'project_objects');
    	if(is_error($all_ids)) {
    	  return $all_ids->getMessage();
    	} // if
    	
    	if(is_foreachable($all_ids)) {
    	  $updates = array();
    	  foreach($all_ids as $row) {
    	    $project_id = (integer) $row['project_id'];
    	    
    	    if(!isset($updates[$project_id])) {
    	      $updates[$project_id] = array();
    	    } // if
    	    
    	    $updates[$project_id][] = (integer) $row['id'];
    	  } // foreach
    	  
    	  foreach($updates as $project_id => $object_ids) {
    	    $update = $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'activity_logs SET project_id = ? WHERE object_id IN (?)', array($project_id, $object_ids));
    	    if(is_error($update)) {
    	      return $update->getMessage();
    	    } // if
    	  } // foreach
    	} // if
    	
    	return true;
    } // updateActivityLog
    
    /**
     * Update system role permissions and related tasks
     *
     * @param void
     * @return null
     */
    function updateSystemRoles() {
      $all_roles = $this->utility->db->execute_all('SELECT id FROM ' . TABLE_PREFIX . 'roles');
      if(is_foreachable($all_roles)) {
        foreach($all_roles as $row) {
          $role_id = (integer) $row['id'];
          
          $permissions = array();
          
          $role_permissions = $this->utility->db->execute_all('SELECT permission, value FROM ' . TABLE_PREFIX . 'role_permissions WHERE role_id = ?', array($role_id));
          if(is_foreachable($role_permissions)) {
            foreach($role_permissions as $permission) {
              if($permission['value'] > 0) {
                if($permission['permission'] == 'auto_assign') {
                  $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'users SET auto_assign = ? WHERE role_id = ?', array(true, $role_id));
                } else {
                  $permissions[$permission['permission']] = true;
                }  // if
              } // if
            } // foreach
          } // if
          
          $permissions['can_see_private_objects'] = true; // Can see private objects - true by default
          
          $update = $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'roles SET type = ?, permissions = ? WHERE id = ?', array('system', serialize($permissions), $role_id));
        } // foreach
      } // if
      
      // Drop role permissions table
      $this->utility->db->execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'role_permissions');
      
    	// Save manager ID-s
      $manager_ids = $this->utility->db->execute_all('SELECT DISTINCT manager_id FROM ' . TABLE_PREFIX . 'companies WHERE is_owner = 0');
      if(is_error($manager_ids)) {
        return $manager_ids->getMessage();
      } // if
      
      // Drop manager ID column
      $save = $this->utility->db->execute("alter table `" . TABLE_PREFIX . "companies` drop column `manager_id`");
      if(is_error($save)) {
        return $save->getMessage();
      } // if
      
      // Create client company member role
      $save = $this->utility->db->execute('INSERT INTO ' . TABLE_PREFIX . 'roles (name, type, permissions) VALUES (?, ?, ?)', array('Client Company Member', 'system', serialize(array(
        'system_access' => true
      ))));
      
      if(is_error($save)) {
        return $save->getMessage();
      } // if
      
      $client_company_member_id = $this->utility->db->lastInsertId();
      
      // Create client company managers role
      $save = $this->utility->db->execute('INSERT INTO ' . TABLE_PREFIX . 'roles (name, type, permissions) VALUES (?, ?, ?)', array('Client Company Manager', 'system', serialize(array(
        'system_access' => true,
        'manage_company_details' => true,
      ))));
      
      if(is_error($save)) {
        return $save->getMessage();
      } // if
      
      $client_company_manager_id = $this->utility->db->lastInsertId();
      
      // Update manager roles
      if(is_foreachable($manager_ids)) {
        $save = $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'users SET role_id = ? WHERE id IN (?)', array($client_company_manager_id, $manager_ids));
        if(is_error($save)) {
          return $save->getMessage();
        } // if
      } // if
      
      $users_table = TABLE_PREFIX . 'users';
      $companies_table = TABLE_PREFIX . 'companies';
      
      // Update role ID-s
      $save = $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'users SET role_id = ? WHERE role_id = ? OR role_id IS NULL', array($client_company_member_id, 0));
      if(is_error($save)) {
        return $save->getMessage();
      } // if
      
      // Update default role setting
      $save = $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'config_options SET value = ? WHERE name = ?', array(serialize($client_company_member_id), 'default_role'));
      if(is_error($save)) {
        return $save->getMessage();
      } // if
      
      return true;
    } // updateSystemRoles
    
    /**
     * Update project level permissions
     *
     * @param void
     * @return null
     */
    function updateProjectPermissions() {
      if(!defined('PROJECT_PERMISSION_NONE')) {
        define('PROJECT_PERMISSION_NONE', 0);
      } // if
      if(!defined('PROJECT_PERMISSION_ACCESS')) {
        define('PROJECT_PERMISSION_ACCESS', 1);
      } // if
      if(!defined('PROJECT_PERMISSION_CREATE')) {
        define('PROJECT_PERMISSION_CREATE', 2);
      } // if
      if(!defined('PROJECT_PERMISSION_MANAGE')) {
        define('PROJECT_PERMISSION_MANAGE', 3);
      } // if
      
      // Move permission data from project_permissions table to project users table
      $project_users = $this->utility->db->execute('SELECT user_id, project_id FROM ' . TABLE_PREFIX . 'project_users');
      if(is_error($project_users)) {
        return $project_users->getMessage();
      } // if
      
      if(is_foreachable($project_users)) {
        foreach($project_users as $project_user) {
          $user_id = (integer) $project_user['user_id'];
          $project_id = (integer) $project_user['project_id'];
          
          $user_project_permissions = $this->utility->db->execute('SELECT permission, value FROM ' . TABLE_PREFIX . 'project_permissions WHERE user_id = ? AND project_id = ?', array($user_id, $project_id));
          if(is_error($user_project_permissions)) {
            return $user_project_permissions->getMessage();
          } // if
          
          $prepared_permissions = array();
          
          if(is_foreachable($user_project_permissions)) {
            foreach($user_project_permissions as $user_project_permission) {
              switch($user_project_permission['permission']) {
                case 'checklists_add':
                  if(!isset($prepared_permissions['checklist']) || ($prepared_permissions['checklist'] < PROJECT_PERMISSION_CREATE)) {
                    $prepared_permissions['checklist'] = PROJECT_PERMISSION_CREATE;
                  } // if
                  break;
                case 'checklists_manage':
                  $prepared_permissions['checklist'] = PROJECT_PERMISSION_MANAGE;
                  break;
                case 'discussions_add':
                  if(!isset($prepared_permissions['discussion']) || ($prepared_permissions['discussion'] < PROJECT_PERMISSION_CREATE)) {
                    $prepared_permissions['discussion'] = PROJECT_PERMISSION_CREATE;
                  } // if
                  break;
                case 'discussions_manage':
                  $prepared_permissions['discussion'] = PROJECT_PERMISSION_MANAGE;
                  break;
                case 'files_add':
                  if(!isset($prepared_permissions['file']) || ($prepared_permissions['file'] < PROJECT_PERMISSION_CREATE)) {
                    $prepared_permissions['file'] = PROJECT_PERMISSION_CREATE;
                  } // if
                  break;
                case 'files_manage':
                  $prepared_permissions['file'] = PROJECT_PERMISSION_MANAGE;
                  break;
                case 'milestones_add':
                  if(!isset($prepared_permissions['milestone']) || ($prepared_permissions['milestone'] < PROJECT_PERMISSION_CREATE)) {
                    $prepared_permissions['milestone'] = PROJECT_PERMISSION_CREATE;
                  } // if
                  break;
                case 'milestones_manage':
                  $prepared_permissions['milestone'] = PROJECT_PERMISSION_MANAGE;
                  break;
                case 'pages_add':
                  if(!isset($prepared_permissions['page']) || ($prepared_permissions['page'] < PROJECT_PERMISSION_CREATE)) {
                    $prepared_permissions['page'] = PROJECT_PERMISSION_CREATE;
                  } // if
                  break;
                case 'pages_manage':
                  $prepared_permissions['page'] = PROJECT_PERMISSION_MANAGE;
                  break;
                case 'tickets_add':
                  if(!isset($prepared_permissions['ticket']) || ($prepared_permissions['ticket'] < PROJECT_PERMISSION_CREATE)) {
                    $prepared_permissions['ticket'] = PROJECT_PERMISSION_CREATE;
                  } // if
                  break;
                case 'tickets_manage':
                  $prepared_permissions['ticket'] = PROJECT_PERMISSION_MANAGE;
                  break;
                case 'timetracking_add':
                  if(!isset($prepared_permissions['timerecord']) || ($prepared_permissions['timerecord'] < PROJECT_PERMISSION_CREATE)) {
                    $prepared_permissions['timerecord'] = PROJECT_PERMISSION_CREATE;
                  } // if
                  break;
                case 'timetracking_manage':
                  $prepared_permissions['timerecord'] = PROJECT_PERMISSION_MANAGE;
                  break;
              }
            } // foreach
          } // if
          
          $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'project_users SET permissions = ? WHERE user_id = ? AND project_id = ?', array(serialize($prepared_permissions), $user_id, $project_id));
        } // foreach
      } // if
      
    	// Drop project permissions table
    	$this->utility->db->execute('DROP TABLE IF EXISTS ' . TABLE_PREFIX . 'project_permissions');
    	
    	return true;
    } // updateProjectPermissions
    
    /**
     * Insert user and project data into search index, resync project ID values
     *
     * @param void
     * @return boolean
     */
    function rebuildSearchIndex() {
      $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'search_index SET type = "ProjectObject"');
      
      $to_insert = array();
      
      // Users
      $rows = $this->utility->db->execute('SELECT id, first_name, last_name, email FROM ' . TABLE_PREFIX . 'users');
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $content = $row['email'];
          if(!empty($row['first_name']) && !empty($row['last_name'])) {
            $content .= "\n\n" . $row['first_name'] . ' ' . $row['last_name'];
          } // if
          
          $to_insert[] = '(' . $row['id'] . ", 'User', " . $this->utility->db->escapeString($content) . ')';
        } // foreach
      } // if
      
    	// Projects
      $rows = $this->utility->db->execute('SELECT id, name, overview FROM ' . TABLE_PREFIX . 'projects');
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $content = $row['name'];
          if(!empty($row['overview'])) {
            $content .= "\n\n" . $row['overview'];
          } // if
          
          $to_insert[] = '(' . $row['id'] . ", 'Project', " . $this->utility->db->escapeString($content) . ')';
        } // foreach
      } // if
      
      $insert = $this->utility->db->execute('INSERT INTO ' . TABLE_PREFIX . 'search_index (object_id, type, content) VALUES ' . implode(', ', $to_insert));
      if(is_error($insert)) {
        return $insert->getMessage();
      } // if
      
      return true;
    } // rebuildSearchIndex
    
    /**
     * Rebuild parent type data in project objects table
     *
     * @param void
     * @return boolean
     */
    function rebuildParentType() {
    	$rows = $this->utility->db->execute_all('SELECT DISTINCT parent_id FROM ' . TABLE_PREFIX . 'project_objects WHERE parent_id IS NOT NULL');
    	if(is_foreachable($rows)) {
    	  foreach($rows as $row) {
    	    $type = array_var($this->utility->db->execute_one('SELECT type FROM ' . TABLE_PREFIX . 'project_objects WHERE id = ?', array($row['parent_id']), 'type'));
    	    if($type) {
    	      $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET parent_type = ? WHERE parent_id = ?', array($type, $row['parent_id']));
    	    } // if
    	  } // foreach
    	} // if
    	
    	return true;
    } // rebuildParentType
    
    /**
     * Move time record data from datetime to time fields
     *
     * @param void
     * @return boolean
     */
    function moveTimeRecordData() {
      $move = $this->utility->db->execute("UPDATE " . TABLE_PREFIX . "project_objects SET date_field_1 = DATE_FORMAT(datetime_field_1, '%Y-%m-%d'), datetime_field_1 = NULL WHERE type = ?", array('TimeRecord'));
      if(is_error($move)) {
        return $move->getMessage();
      } // if
    	return true;
    } // moveTimeRecordData
    
    /**
     * Rebuild has time data
     *
     * @param void
     * @return boolean
     */
    function rebuildHasTime() {
      $rows = $this->utility->db->execute_all('SELECT DISTINCT parent_id FROM '. TABLE_PREFIX . 'project_objects WHERE type = ?', array('TimeRecord'));
      
      if(is_foreachable($rows)) {
        $object_ids = array();
        foreach($rows as $row) {
          $object_ids[] = (integer) $row['parent_id'];
        } // foreach
        
        $update = $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET has_time = 1 WHERE id IN (?)', array($object_ids));
        if(is_error($update)) {
          return $update->getMessage();
        } // if
      } // if
      
    	return true;
    } // rebuildHasTime
    
    /**
     * Go through user passwords and digest them
     *
     * @param void
     * @return null
     */
    function digestUserPasswords() {
    	$rows = $this->utility->db->execute_all('SELECT id, password FROM ' . TABLE_PREFIX . 'users');
    	if(is_foreachable($rows)) {
    	  foreach($rows as $row) {
    	    $new_password = sha1( $row['password']);
    	    
    	    $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'users SET password = ? WHERE id = ?', array($new_password, $row['id']));
    	  } // foreach
    	} // if
    	
    	return true;
    } // digestUserPasswords
    
    /**
     * Import email templates from config table
     *
     * @param void
     * @return null
     */
//    function moveEmailTemplates() {
//      $all_templates = array(
//        'discussions' => array('new_discussion'),
//        'files'       => array('new_file', 'new_revision'),
//        'pages'       => array('new_page', 'new_revision'),
//        'resources'   => array('new_comment', 'task_assigned', 'task_completed', 'task_reopened'),
//        'system'      => array('forgot_password', 'new_user'),
//        'try'         => array('send_invitation'),
//      );
//      
//      foreach($all_templates as $module_name => $templates) {
//        foreach($templates as $template_name) {
//          $subject_config = $module_name . '_' . $template_name . '_subject';
//          $body_config = $module_name . '_' . $template_name . '_body';
//          
//          $rows = db_execute('SELECT name, value FROM ' . TABLE_PREFIX . 'config_options WHERE name IN (?)', array($subject_config, $body_config));
//          
//          $subject = '';
//          $body = '';
//          
//          if(is_foreachable($rows)) {
//            foreach($rows as $row) {
//              if($row['name'] == $subject_config) {
//                $subject = unserialize($row['value']);
//              } elseif($row['name'] == $body_config) {
//                $body = unserialize($row['value']);
//              } // if
//            } // foreach
//          } // if
//          
//          if($subject && $body) {
//            db_execute('INSERT INTO ' . TABLE_PREFIX . 'email_templates (name, module, subject, body) VALUES (?, ?, ?, ?)', $template_name, $module_name, $subject, $body);
//          } // if
//          
//          db_execute('DELETE FROM ' . TABLE_PREFIX . 'config_options WHERE name IN (?)', array($subject_config, $body_config));
//        } // foreach
//      } // foreach
//    } // moveEmailTemplates
    
  }

?>