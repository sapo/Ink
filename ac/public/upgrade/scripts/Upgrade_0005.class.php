<?php

  /**
   * Upgrade 1.1.4 to 1.1.5
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0005 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '1.1.4';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '1.1.5';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateExistingTables' => 'Update existing tables',
    	  'updateTimeRecords' => 'Update time records',
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
    	  "alter table `" . TABLE_PREFIX . "project_objects` change `milestone_id` `milestone_id` int(10) unsigned null default null",
    	);
    	
    	$tables = $this->utility->db->listTables(TABLE_PREFIX);
    	if(is_array($tables) && in_array(TABLE_PREFIX . 'time_reports', $tables)) {
    	  $changes[] = "alter table  `" . TABLE_PREFIX . "time_reports` change `billable_filter` `billable_filter` enum('all', 'billable', 'not_billable', 'billable_billed', 'billable_not_billed', 'pending_payment') not null default 'all'";
    	} // if
    	
    	foreach($changes as $change) {
    	  $update = $this->utility->db->execute($change);
    	  if(is_error($update)) {
    	    return $update->getMessage();
    	  } // if
    	} // foreach
    	
    	return true;
    } // updateExistingTables
    
    /**
     * Update time records
     * 
     * Move boolean_field_1 and boolean_field_2 into integer_field_1
     * 
     * - boolean_field_1 - is_billable
     * - boolean_field_2 - is_billed
     * - integer_field_2 - billable status
     *
     * @param void
     * @return boolean
     */
    function updateTimeRecords() {
      $rows = db_execute_all('SELECT id, boolean_field_1, boolean_field_2, integer_field_2 FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ?', 'TimeRecord');
      
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          if($row['integer_field_2'] > 0) {
            continue; // in case we are running upgrade after some of the time records that use the new system are posted!
          } // if
          
          if($row['boolean_field_1']) {
            $new_status = $row['boolean_field_2'] ? 3 : 1;
          } else {
            $new_status = 0;
          } // if
          
          db_execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET integer_field_2 = ?, boolean_field_1 = NULL, boolean_field_2 = NULL WHERE id = ?', $new_status, $row['id']);
        } // foreach
      } // if
      
      return true;
    } // updateTimeRecords
    
  }

?>