<?php

  /**
   * Base class for every upgrade script
   * 
   * @package activeCollab.upgrade
   * @subpackage library
   */
  class UpgradeScript extends AngieObject {
    
    /**
     * Upgrade utility instance
     *
     * @var UpgradeUtility
     */
    var $utility;
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version;
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version;
    
    /**
     * Construct adn set utility instance
     *
     * @param UpgradeUtility $util
     * @return UpgradeScript
     */
    function __construct(&$util) {
    	$this->utility = $util;
    } // __construct
    
    /**
     * Return upgrade actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return null;
    } // getActions
    
    /**
     * Return from version
     *
     * @param void
     * @return float
     */
    function getFromVersion() {
    	return $this->from_version;
    } // getFromVersion
    
    /**
     * Return to version
     *
     * @param void
     * @return float
     */
    function getToVersion() {
    	return $this->to_version;
    } // getToVersion
    
    /**
     * Identify this uprade script
     *
     * @param void
     * @return string
     */
    function getGroup() {
    	return (string) $this->from_version . '-' . (string) $this->to_version;
    } // getGroup
    
    /**
     * Start upgrade by creating backup
     *
     * @param void
     * @return null
     */
    function startUpgrade() {
      $work_path = ENVIRONMENT_PATH . '/work';
      if(is_dir($work_path)) {
        if(is_writable($work_path)) {
          $connection =& DBConnection::instance();
          $tables = $connection->listTables(TABLE_PREFIX);
          if(is_foreachable($tables)) {
            $dump = $connection->dumpTables($tables, $work_path . '/database-backup-' . date('Y-m-d-H-i-s') . '.sql', true, true);
            
            if($dump && !is_error($dump)) {
              return true;
            } else {
              return is_error($dump) ? $dump->getMessage() : 'Failed to back up database content';
            } // if
          } else {
            return 'There are no activeCollab tables in the database';
          } // if
        } else {
          return "Work folder not writable";
        } // if
      } else {
        return "Work folder not found. Expected location: $work_path";
      } // if
    } // startUpgrade
    
    /**
     * This action will write entry in upgrade history
     *
     * @param void
     * @return null
     */
    function endUpgrade() {
    	$update = $this->utility->db->execute('INSERT INTO ' . TABLE_PREFIX . 'update_history SET version = ?, created_on = NOW()', array($this->getToVersion()));
    	return is_error($update) ? $update->getMessage() : true;
    } // endUpgrade
    
  }

?>