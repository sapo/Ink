CREATE TABLE <?php echo $table_prefix ?>activity_logs (
  id int(11) NOT NULL auto_increment,
  type varchar(50) NOT NULL default 'ActivityLog',
  object_id int(11) unsigned NOT NULL default '0',
  project_id int(10) unsigned NOT NULL default '0',
  action varchar(100) <?php echo $default_collation ?> default NULL,
  created_on datetime default NULL,
  created_by_id smallint(6) default NULL,
  created_by_name varchar(100) <?php echo $default_collation ?> default NULL,
  created_by_email varchar(100) <?php echo $default_collation ?> default NULL,
  comment text <?php echo $default_collation ?>,
  PRIMARY KEY (id),
  KEY created_on (created_on),
  KEY project_id (project_id)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>companies (
  id smallint(5) unsigned NOT NULL auto_increment,
  name varchar(100) NOT NULL default '',
  is_owner tinyint(1) unsigned NOT NULL default '0',
  is_archived tinyint(1) unsigned not null default '0',
  created_on datetime default NULL,
  updated_on datetime default NULL,
  PRIMARY KEY (id)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>company_config_options (
  company_id smallint(5) unsigned NOT NULL default '0',
  name varchar(50) NOT NULL default '',
  value text,
  PRIMARY KEY (company_id,name)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>config_options (
  name varchar(50) NOT NULL default '',
  module varchar(30) NOT NULL default '',
  type enum('system','project','user','company') NOT NULL default 'system',
  value text,
  PRIMARY KEY (name)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>email_template_translations (
  name varchar(50) NOT NULL,
  module varchar(50) NOT NULL,
  locale varchar(50) NOT NULL,
  subject varchar(150) NOT NULL,
  body text,
  PRIMARY KEY (name,module,locale)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>email_templates (
  name varchar(50) NOT NULL,
  module varchar(50) NOT NULL,
  subject varchar(150) NOT NULL,
  body text,
  variables text,
  PRIMARY KEY (name,module)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>languages (
  id tinyint(3) unsigned NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  locale varchar(30) NOT NULL,
  PRIMARY KEY (id)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>modules (
  name varchar(50) NOT NULL default '',
  is_system tinyint(1) unsigned NOT NULL default '0',
  position smallint(6) NOT NULL default '0',
  PRIMARY KEY (name)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>pinned_projects (
  project_id int(10) unsigned NOT NULL default '0',
  user_id smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY (project_id,user_id)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>projects (
  id smallint(5) unsigned NOT NULL auto_increment,
  group_id smallint(5) unsigned NOT NULL default '0',
  company_id smallint(5) unsigned NOT NULL default '0',
  name varchar(150) NOT NULL default '',
  leader_id smallint(5) unsigned NOT NULL default '0',
  leader_name varchar(100) default NULL,
  leader_email varchar(100) default NULL,
  overview text,
  status enum('active','paused','completed','canceled') NOT NULL default 'active',
  type enum('normal','system') NOT NULL default 'normal',
  default_visibility tinyint(1) unsigned NOT NULL default '0',
  starts_on date default NULL,
  completed_on datetime default NULL,
  completed_by_id smallint(5) unsigned default NULL,
  completed_by_name varchar(100) default NULL,
  completed_by_email varchar(100) default NULL,
  created_on datetime default NULL,
  created_by_id smallint(5) unsigned default NULL,
  created_by_name varchar(100) default NULL,
  created_by_email varchar(100) default NULL,
  updated_on datetime default NULL,
  open_tasks_count mediumint(8) unsigned NOT NULL default '0',
  total_tasks_count mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY created_on (created_on),
  KEY group_id (group_id),
  KEY name (name)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>project_config_options (
  project_id int(10) unsigned NOT NULL default '0',
  name varchar(50) NOT NULL default '',
  value text,
  PRIMARY KEY (project_id,name)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>project_groups (
  id smallint(5) unsigned NOT NULL auto_increment,
  name varchar(100) NOT NULL default '',
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>project_objects (
  id int(10) unsigned NOT NULL auto_increment,
  source varchar(50) default NULL,
  type varchar(30) NOT NULL default 'ProjectObject',
  module varchar(30) NOT NULL default 'system',
  project_id int(10) unsigned NOT NULL default '0',
  milestone_id int(10) unsigned default NULL,
  parent_id int(10) unsigned default NULL,
  parent_type varchar(30) default NULL,
  name varchar(150) default NULL,
  body longtext,
  tags text,
  state tinyint(4) NOT NULL default '0',
  visibility tinyint(4) NOT NULL default '0',
  priority tinyint(4) default NULL,
  resolution varchar(50) default NULL,
  created_on datetime default NULL,
  created_by_id smallint(5) unsigned NOT NULL default '0',
  created_by_name varchar(100) default NULL,
  created_by_email varchar(100) default NULL,
  updated_on datetime default NULL,
  updated_by_id smallint(5) unsigned default NULL,
  updated_by_name varchar(100) default NULL,
  updated_by_email varchar(100) default NULL,
  due_on date default NULL,
  completed_on datetime default NULL,
  completed_by_id smallint(5) unsigned default NULL,
  completed_by_name varchar(100) default NULL,
  completed_by_email varchar(100) default NULL,
  comments_count smallint unsigned default NULL,
  has_time tinyint(1) unsigned NOT NULL default '0',
  is_locked tinyint(3) unsigned default NULL,
  varchar_field_1 varchar(255) default NULL,
  varchar_field_2 varchar(255) default NULL,
  integer_field_1 int(11) default NULL,
  integer_field_2 int(11) default NULL,
  float_field_1 double(10,2) default NULL,
  float_field_2 double(10,2) default NULL,
  text_field_1 text,
  text_field_2 text,
  date_field_1 date default NULL,
  date_field_2 date default NULL,
  datetime_field_1 datetime default NULL,
  datetime_field_2 datetime default NULL,
  boolean_field_1 tinyint(1) unsigned default NULL,
  boolean_field_2 tinyint(1) unsigned default NULL,
  position int unsigned default NULL,
  version int unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  KEY type (type),
  KEY module (module),
  KEY project_id (project_id),
  KEY parent_id (parent_id),
  KEY created_on (created_on),
  KEY due_on (due_on),
  KEY completed_on (completed_on)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>project_object_views (
  object_id int(10) unsigned NOT NULL,
  created_by_id smallint(5) unsigned NOT NULL,
  created_by_name varchar(100) NOT NULL,
  created_by_email varchar(150) NOT NULL,
  created_on datetime NOT NULL,
  KEY object_id (object_id,created_by_id),
  KEY created_by_id (created_by_id)
) <?php echo $engine ?> <?php echo $default_charset ?>;


CREATE TABLE <?php echo $table_prefix ?>project_users (
  user_id smallint(5) unsigned NOT NULL default '0',
  project_id int(10) unsigned NOT NULL default '0',
  role_id tinyint(3) unsigned NOT NULL default '0',
  permissions text default NULL,
  PRIMARY KEY (user_id,project_id)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>roles (
  id tinyint(3) unsigned NOT NULL auto_increment,
  name varchar(50) NOT NULL default '',
  type enum('system','project') NOT NULL default 'system',
  permissions text,
  PRIMARY KEY (id)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>search_index (
  object_id int(10) unsigned NOT NULL default '0',
  type varchar(50) NOT NULL,
  content longtext,
  PRIMARY KEY (object_id,type),
  FULLTEXT KEY content (content)
) ENGINE=MyISAM <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>update_history (
  id smallint(11) unsigned NOT NULL auto_increment,
  version varchar(30) NOT NULL default '',
  created_on datetime NOT NULL,
  PRIMARY KEY (id)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>user_config_options (
  user_id int(10) unsigned NOT NULL default '0',
  name varchar(50) NOT NULL default '',
  value text,
  PRIMARY KEY (user_id,name)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>users (
  id smallint(5) unsigned NOT NULL auto_increment,
  company_id smallint(5) unsigned NOT NULL default '0',
  role_id tinyint(3) unsigned default NULL,
  first_name varchar(50) default NULL,
  last_name varchar(50) default NULL,
  email varchar(150) NOT NULL default '',
  password varchar(40) NOT NULL default '',
  token varchar(40) NOT NULL default '',
  created_on datetime default NULL,
  updated_on datetime default NULL,
  last_login_on datetime default NULL,
  last_visit_on datetime default NULL,
  last_activity_on datetime default NULL,
  auto_assign tinyint(1) unsigned NOT NULL default 0,
  auto_assign_role_id tinyint(3) unsigned default NULL,
  auto_assign_permissions text default NULL,
  password_reset_key varchar(20) default NULL,
  password_reset_on datetime default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  KEY company_id (company_id)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE <?php echo $table_prefix ?>user_sessions (
  id int(10) unsigned NOT NULL auto_increment,
  user_id int(10) unsigned NOT NULL default '0',
  user_ip varchar(15) default NULL,
  user_agent varchar(255) default NULL,
  visits int(10) unsigned NOT NULL default '0',
  remember tinyint(3) unsigned NOT NULL default '0',
  created_on datetime default NULL,
  last_activity_on datetime default NULL,
  expires_on datetime default NULL,
  session_key varchar(40) default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (session_key),
  KEY expires_on (expires_on)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE `<?php echo $table_prefix ?>assignment_filters` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `is_private` tinyint(1) unsigned NOT NULL,
  `user_filter` enum('anybody', 'not_assigned', 'logged_user', 'logged_user_responsible', 'company', 'selected') NOT NULL default 'logged_user',
  `user_filter_data` text,
  `project_filter` enum('all','active','selected') NOT NULL,
  `project_filter_data` text,
  `date_filter` enum('all','late','today','tomorrow','this_week','next_week','this_month','next_month','selected_date','selected_range') NOT NULL default 'all',
  `date_from` date default NULL,
  `date_to` date default NULL,
  `status_filter` enum('active','completed','all') NOT NULL default 'all',
  `objects_per_page` smallint(5) unsigned default NULL,
  `order_by` varchar(50) default NULL,
  `created_by_id` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE `<?php echo $table_prefix ?>assignments` (
  `user_id` smallint(5) unsigned NOT NULL default '0',
  `object_id` int(10) unsigned NOT NULL default '0',
  `is_owner` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`object_id`),
  KEY `object_id` (`object_id`)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE `<?php echo $table_prefix ?>attachments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned default NULL,
  `parent_type` varchar(30) default NULL,
  `name` varchar(150) default NULL,
  `mime_type` varchar(100) NOT NULL default 'application/octet-stream',
  `size` int(10) unsigned NOT NULL default '0',
  `location` varchar(50) NOT NULL,
  `attachment_type` enum('attachment','file_revision') NOT NULL default 'attachment',
  `created_on` datetime default NULL,
  `created_by_id` smallint(5) unsigned NOT NULL default '0',
  `created_by_name` varchar(100) default NULL,
  `created_by_email` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `created_on` (`created_on`)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE `<?php echo $table_prefix ?>reminders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` smallint(5) unsigned NOT NULL default '0',
  `object_id` int(10) unsigned NOT NULL default '0',
  `comment` text,
  `created_by_id` smallint(5) unsigned NOT NULL default '0',
  `created_by_name` varchar(100) NOT NULL,
  `created_by_email` varchar(100) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `created_on` (`created_on`)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE `<?php echo $table_prefix ?>starred_objects` (
  `object_id` int(10) unsigned NOT NULL default '0',
  `user_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`user_id`)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE `<?php echo $table_prefix ?>subscriptions` (
  `user_id` smallint(5) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`parent_id`)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE `<?php echo $table_prefix ?>page_versions` (
  `page_id` int(10) unsigned NOT NULL default '0',
  `version` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `body` longtext,
  `created_on` datetime default NULL,
  `created_by_id` smallint(5) unsigned default NULL,
  `created_by_name` varchar(100) default NULL,
  `created_by_email` varchar(100) default NULL,
  PRIMARY KEY  (`page_id`,`version`)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE `<?php echo $table_prefix ?>ticket_changes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ticket_id` int(10) unsigned NOT NULL default '0',
  `version` int(10) unsigned NOT NULL default '0',
  `old_value` text,
  `new_value` text,
  `changes` longtext,
  `created_on` datetime default NULL,
  `created_by_id` int(11) default NULL,
  `created_by_name` varchar(100) default NULL,
  `created_by_email` varchar(150) default NULL,
  PRIMARY KEY  (`id`)
) <?php echo $engine ?> <?php echo $default_charset ?>;


CREATE TABLE `<?php echo $table_prefix ?>time_reports` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `is_default` tinyint(1) unsigned NOT NULL default '0',
  `user_filter` enum('anybody','logged_user','company','selected') NOT NULL default 'anybody',
  `user_filter_data` text,
  `billable_filter` enum('all','billable','not_billable','billable_billed','billable_not_billed', 'pending_payment') NOT NULL default 'all',
  `date_filter` enum('all','today','last_week','this_week','last_month','this_month','selected_date','selected_range') NOT NULL default 'all',
  `date_from` date default NULL,
  `date_to` date default NULL,
  `sum_by_user` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) <?php echo $engine ?> <?php echo $default_charset ?>;

