CREATE TABLE `<?php echo $table_prefix ?>documents` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `category_id` tinyint(3) unsigned default NULL,
  `type` enum('text','file') NOT NULL default 'text',
  `name` varchar(100) NOT NULL,
  `body` text,
  `mime_type` varchar(50) default NULL,
  `visibility` tinyint(4) unsigned NOT NULL default '0',
  `is_pinned` tinyint(1) unsigned NOT NULL default '0',
  `created_by_id` smallint(5) unsigned NOT NULL,
  `created_by_name` varchar(100) NOT NULL,
  `created_by_email` varchar(100) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) <?php echo $engine ?> <?php echo $default_charset ?>;

CREATE TABLE `<?php echo $table_prefix ?>document_categories` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) <?php echo $engine ?> <?php echo $default_charset ?>;