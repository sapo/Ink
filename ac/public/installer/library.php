<?php

  /**
   * Abstract installer task
   */
  class InstallerTask {
    
    /**
     * Installer instance
     *
     * @var Installer
     */
    var $installer;
    
    /**
     * Construct installer task
     *
     * @param Installer $installer
     * @return InstallerTask
     */
    function InstallerTask(&$installer) {
      $this->installer = $installer;
    } // InstallerTask
  
    /**
     * Execute task
     *
     * @param void
     * @return boolean
     */
    function execute() {
      die('Not implemented');
    } // execute
  
  } // InstallerTask

?>