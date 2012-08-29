<?php

  /**
   * Update activeCollab 2.1.1 to activeCollab 2.1.2
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0013 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '2.1.1';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '2.1.2';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateAttachmentParentType' => 'Update attachment parent types',
    	);
    } // getActions
    
    /**
     * Update parent type for old first discussion comments to Discussion
     *
     * @param void
     * @return boolean
     */
    function updateAttachmentParentType() {
      $rows = $this->utility->db->execute('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ?', array('Discussion'));
      if(is_foreachable($rows)) {
        $discussion_ids = array();
        foreach($rows as $row) {
          $discussion_ids[] = (integer) $row['id'];
        } // foreach
        
        $this->utility->db->execute('UPDATE ' . TABLE_PREFIX . 'attachments SET parent_type = ? WHERE parent_id IN (?)', array('Discussion', $discussion_ids));
      } // if
      
      return true;
    } // updateExistingTables
    
  }

?>