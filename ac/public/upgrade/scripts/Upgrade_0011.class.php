<?php

  /**
   * Update activeCollab 2.0.3 to activeCollab 2.1
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0011 extends UpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    var $from_version = '2.0.3';
    
    /**
     * Final system version
     *
     * @var string
     */
    var $to_version = '2.1';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'emptyAction' => 'Skip 2.0.3 to 2.1 version logging step',
    	);
    } // getActions
    
    /**
     * Empty action
     *
     * @param void
     * @return null
     */
    function emptyAction() {
      return true;
    } // emptyAction
    
  }

?>