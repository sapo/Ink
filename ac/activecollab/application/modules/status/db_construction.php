CREATE TABLE `<?php echo $table_prefix ?>status_updates` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `message` varchar(255) NOT NULL,
  `created_by_id` smallint(5) unsigned NOT NULL,
  `created_by_name` varchar(100) default NULL,
  `created_by_email` varchar(100) default NULL,
  `created_on` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `created_on` (`created_on`)
) <?php echo $engine ?> <?php echo $default_charset ?>;