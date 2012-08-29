<?php

  /**
   * Update activeCollab 2.1.3 to activeCollab 2.1.4
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0015 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '2.1.3';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '2.1.4';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateSessionStorage' => 'Update session tables',
    	);
    } // getActions
    
    /**
     * Update the way session data is stored
     *
     * @param void
     * @return boolean
     */
    function updateSessionStorage() {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';
      $charset = $this->utility->db->supportsCollation() ? 'default character set utf8 COLLATE utf8_general_ci' : '';
      
      $this->utility->db->execute("CREATE TABLE " . TABLE_PREFIX . "user_sessions (
        id int(10) unsigned NOT NULL auto_increment,
        user_id int(10) unsigned NOT NULL default '0',
        user_ip varchar(15) default NULL,
        user_agent varchar(255) default NULL,
        visits int(10) unsigned NOT NULL default '0',
        remember tinyint(3) unsigned NOT NULL default '0',
        created_on datetime default NULL,
        last_activity_on datetime default NULL,
        expires_on datetime default NULL,
        session_key varchar(40) default NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY (session_key),
        KEY expires_on (expires_on)
      ) ENGINE=$engine $charset");
      
      $this->utility->db->execute("ALTER TABLE " . TABLE_PREFIX . "users DROP session_id");
      
      return true;
    } // updateSessionStorage
  }

?>