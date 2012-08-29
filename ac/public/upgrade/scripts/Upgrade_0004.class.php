<?php

  /**
   * Upgrade 1.1.3 to 1.1.4
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0004 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '1.1.3';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '1.1.4';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateExistingTables' => 'Update existing tables',
    	  'newConfigOptions' => 'Create new configuration options',
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
    	  "alter table `" . TABLE_PREFIX . "project_objects` add `is_locked` tinyint(0) unsigned null default null after `has_time`",
    	  "alter table `" . TABLE_PREFIX . "project_objects` change `project_id` `project_id` int(10) unsigned not null default '0'",
    	  "alter table `" . TABLE_PREFIX . "pinned_projects` change `project_id` `project_id` int(10) unsigned not null default '0'",
    	  "alter table `" . TABLE_PREFIX . "project_config_options` change `project_id` `project_id` int(10) unsigned not null default '0'",
    	  "alter table `" . TABLE_PREFIX . "project_users` change `project_id` `project_id` int(10) unsigned not null default '0'",
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
     * Create new v1.1.4 configuration options
     *
     * @param void
     * @return boolean
     */
    function newConfigOptions() {
      $insert = $this->utility->db->execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, type, value) VALUES 
        ('last_frequently_activity', 'system', 'system', 'N;'),
        ('last_hourly_activity', 'system', 'system', 'N;'),
        ('last_daily_activity', 'system', 'system', 'N;')
      ");
    	
  	  if(is_error($insert)) {
  	    return $insert->getMessage();
  	  } // if
    	
    	return true;
    } // newConfigOptions
    
  }

?>