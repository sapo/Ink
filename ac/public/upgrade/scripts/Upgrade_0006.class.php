<?php

  /**
   * Upgrade 1.1.5 to 1.1.6
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0006 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '1.1.5';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '1.1.6';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateExistingTables' => 'Update existing tables',
    	  'updateAssignmentFilters' => 'Update assignment filters',
    	);
    } // getActions
    
    /**
     * Update existing tables
     *
     * @param void
     * @return boolean
     */
    function updateExistingTables() {
      $config_created_on = filectime(ENVIRONMENT_PATH . '/config/config.php');
      $created_on = $config_created_on > 0 ? date(DATETIME_MYSQL, $config_created_on) : date(DATETIME_MYSQL, time());
      
      $companies_table_fields = $this->utility->db->listTableFields(TABLE_PREFIX . 'companies');
      $projects_table_fields = $this->utility->db->listTableFields(TABLE_PREFIX . 'projects');
      $users_table_fields = $this->utility->db->listTableFields(TABLE_PREFIX . 'users');
      
      $changes = array();
      
      if(!in_array('created_on', $companies_table_fields)) {
        $changes[] = "alter table " . TABLE_PREFIX . "companies add created_on datetime null default null after name";
        $changes[] = "update " . TABLE_PREFIX . "companies set created_on = '$created_on'";
      } // if
      
      if(!in_array('updated_on', $companies_table_fields)) {
        $changes[] = "alter table " . TABLE_PREFIX . "companies add updated_on datetime null default null after created_on";
      } // if
      
      if(!in_array('updated_on', $projects_table_fields)) {
        $changes[] = "alter table " . TABLE_PREFIX . "projects add updated_on datetime null default null after created_by_email";
      } // if
      
      if(!in_array('created_on', $users_table_fields)) {
        $changes[] = "alter table " . TABLE_PREFIX . "users add created_on datetime null default null after session_id";
        $changes[] = "update " . TABLE_PREFIX . "users set created_on = '$created_on'";
      } // if
      
      if(!in_array('updated_on', $users_table_fields)) {
        $changes[] = "alter table " . TABLE_PREFIX . "users add updated_on datetime null default null after created_on";
      } // if
    	
    	foreach($changes as $change) {
    	  $update = $this->utility->db->execute($change);
    	  if(is_error($update)) {
    	    return $update->getMessage();
    	  } // if
    	} // foreach
    	
    	return true;
    } // updateExistingTabless
    
    /**
     * Update existing tables
     *
     * @param void
     * @return boolean
     */
    function updateAssignmentFilters() {
      $assignment_filters_table = TABLE_PREFIX . 'assignment_filters';
      
    	$changes = array(
    	  "alter table $assignment_filters_table change user_filter user_filter enum('anybody', 'not_assigned', 'logged_user', 'logged_user_responsible', 'company', 'selected') not null default 'logged_user'",
    	  "update $assignment_filters_table set user_filter = 'not_assigned' where user_filter = 'anybody'",
    	  "alter table $assignment_filters_table add status_filter enum('all', 'active', 'completed') not null default 'active' after date_to",
    	);
    	
    	foreach($changes as $change) {
    	  $update = $this->utility->db->execute($change);
    	  if(is_error($update)) {
    	    return $update->getMessage();
    	  } // if
    	} // foreach
    	
    	return true;
    } // updateExistingTables
    
  }

?>