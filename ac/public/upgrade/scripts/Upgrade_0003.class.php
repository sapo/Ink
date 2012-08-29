<?php

  /**
   * Upgrade 1.1.2 to 1.1.3
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0003 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '1.1.2';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '1.1.3';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateExistingTables' => 'Update existing tables',
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
    	  "alter table `" . TABLE_PREFIX . "subscriptions` change column `parent_id` `parent_id` int(10) unsigned NOT NULL default '0' after `user_id`",
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