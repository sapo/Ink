<?php

  /**
   * Upgrade activeCollab 1.1 to 1.1.2
   * 
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0002 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '1.1';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '1.1.2';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateExistingTables' => 'Update existing tables',
    	  'refreshData' => 'Update data',
    	);
    } // getActions
    
    /**
     * Update existing tables
     *
     * @param void
     * @return boolean
     */
    function updateExistingTables() {
      $tables = $this->utility->db->listTables(TABLE_PREFIX);
      if(in_array(TABLE_PREFIX . 'time_reports', $tables)) {
      	$changes = array(
      	  "alter table `" . TABLE_PREFIX . "time_reports` change column `user_filter` `user_filter` enum('anybody', 'logged_user', 'company', 'selected') not null default 'anybody' after `is_default`",
      	);
      	
      	foreach($changes as $change) {
      	  $update = $this->utility->db->execute($change);
      	  if(is_error($update)) {
      	    return $update->getMessage();
      	  } // if
      	} // foreach
      } // if
    	
    	return true;
    } // updateExistingTables
    
    /**
     * Refresh data
     *
     * @param void
     * @return boolean
     */
    function refreshData() {
      $changes = array(
        'DELETE FROM ' . TABLE_PREFIX . "config_options WHERE name = 'format_datetime'",
        'DELETE FROM ' . TABLE_PREFIX . "user_config_options WHERE name = 'format_datetime'",
      );
      
      $row = $this->utility->db->execute_one('SELECT id FROM ' . TABLE_PREFIX . 'users ORDER BY id LIMIT 0, 1');
      if(is_array($row) && isset($row['id'])) {
        $user_id = (integer) $row['id'];
      } // if
      
      if($user_id) {
        $changes[] = 'UPDATE ' . TABLE_PREFIX . "assignment_filters SET created_by_id = '$user_id' WHERE created_by_id = '0'";
      } // if
      
      foreach($changes as $change) {
    	  $update = $this->utility->db->execute($change);
    	  if(is_error($update)) {
    	    return $update->getMessage();
    	  } // if
    	} // foreach
    	
    	return true;
    } // refreshData
    
  }

?>