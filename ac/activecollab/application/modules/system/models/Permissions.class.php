<?php

  /**
   * Permissions management class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Permissions {
    
    function getByRole() {
    	
    } // getByRole
    
    /**
     * Return system permissions
     *
     * @param void
     * @return array
     */
    function findSystem() {
    	static $permissions = null;
    	
    	if($permissions === null) {
    	  $permissions = array();
    	  event_trigger('on_system_permissions', array(&$permissions));
    	} // if
    	
    	return $permissions;
    } // getSystem
    
    /**
     * Return project permissions
     *
     * @param void
     * @return array
     */
    function findProject() {
    	static $permissions = null;
    	
    	if($permissions === null) {
    	  $permissions = array();
    	  event_trigger('on_project_permissions', array(&$permissions));
    	} // if
    	
    	return $permissions;
    } // getProject
    
    /**
     * Return portal permissions
     *
     * @param void
     * @return array
     */
    function findPortal() {
    	static $permissions = null;
    	
    	if($permissions === null) {
    		$permissions = array();
    		event_trigger('on_portal_permissions', array(&$permissions));
    	} // if
    	
    	return $permissions;
    } // findPortal
    
  }

?>