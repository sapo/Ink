<?php

  /**
   * Update activeCollab 2.1.2 to activeCollab 2.1.3
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0014 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '2.1.2';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '2.1.3';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateMailingConfigOptions' => 'Update mailing configuration options',
    	);
    } // getActions
    
    /**
     * Update emailing configuration options
     *
     * @param void
     * @return null
     */
    function updateMailingConfigOptions() {
      $this->utility->db->execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, type, value) VALUES
        ('mailing_native_options', 'system', 'system', 's:9:\"-oi -f %s\";'),
        ('mailing_mark_as_bulk', 'system', 'system', 'b:1;'),
        ('mailing_empty_return_path', 'system', 'system', 'b:0;');");
      
      return true;
    } // updateMailingConfigOptions
  }

?>