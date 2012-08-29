<?php

  /**
   * Update activeCollab 2.2.1 to activeCollab 2.2.2
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0017 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '2.2.1';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '2.2.2';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateConfigOptions' => 'Update configuration options',
    	  'endUpgrade' => 'Finish upgrade',
    	);
    } // getActions
    
    /**
     * Update parent type for old first discussion comments to Discussion
     *
     * @param void
     * @return boolean
     */
    function updateConfigOptions() {
      if(array_var($this->utility->db->execute_one("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . "modules WHERE name = 'source'"), 'row_count') == 1) {
        $this->utility->db->execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, type, value) VALUES ('source_svn_config_dir', 'source', 'system', 'N;')");
      } // if
      return true;
    } // updateConfigOptions
    
  }

?>