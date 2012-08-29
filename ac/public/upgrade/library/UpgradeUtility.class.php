<?php

  /**
   * Utility methods for upgrade script
   *
   * @package activeCollab.upgrade
   * @subpackage library
   */
  class UpgradeUtility extends AngieObject {
    
    /**
     * Database connection
     *
     * @var DbConnection
     */
    var $db;
    
    /**
     * Cached current version value
     *
     * @var string
     */
    var $current_version = false;
    
    /**
     * Construct upgrade utility
     *
     * @param void
     * @return UpgradeUtility
     */
    function __construct() {
      $charset = defined('DB_CHARSET') && DB_CHARSET ? DB_CHARSET : null;
      
      $this->db =& DBConnection::instance();
      $this->db->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, true, $charset);
    } // __construct
    
    /**
     * Authenticate user
     * 
     * Returns TRUE on success or error message on failure
     *
     * @param string $email
     * @param string $password
     * @return boolean
     */
    function authenticate($email, $password) {
    	if(empty($email) || trim($password) == '') {
        return 'Email address and password values are required';
      } // if
      
      if(!is_valid_email($email)) {
        return 'Invalid email address format';
      } // if
      
      $user = $this->db->execute_one('SELECT role_id, password FROM ' . TABLE_PREFIX . 'users WHERE email = ?', array($email));
      if(is_array($user)) {
        if(!$this->checkUserPassword($password, $user['password'])) {
          return 'Invalid password';
        } // if
      } else {
        return "Invalid email address. User does not exist";
      } // if
      
      if(!$user['role_id']) {
        return 'Authenticated user is not administrator';
      } // if
      
      // Check administration access
      if($this->isUserAdministrator($user['role_id'])) {
        return true;
      } else {
        return 'Authenticated user is not administrator';
      } // if
    } // authenticate
    
    /**
     * Print authentication error
     *
     * @param string $error
     * @param string $email
     * @param string $password
     * @return null
     */
    function authenticationError($authentication_error, $email, $password) {
    	require UPGRADE_SCRIPT_PATH . '/include/authenticate.php';
      die();
    } // authenticationError
    
    /**
     * Return current installation version
     *
     * @param void
     * @return string
     */
    function currentVersion() {
      if($this->current_version === false) {
        $row = $this->db->execute_one('SELECT version FROM ' . TABLE_PREFIX . 'update_history ORDER BY created_on DESC LIMIT 0, 1');
    	  $this->current_version = is_array($row) ? $row['version'] : '1.0';
    	  
    	  // activeCollab 2.0.2 failed to record proper version into update 
    	  // history  so we need to manually check if we have 2.0.2. This is done 
    	  // by checking if acx_attachments table exists (introduced in aC 2).
    	  if((version_compare($this->current_version, '2.0') < 0) && in_array(TABLE_PREFIX . 'attachments', $this->db->listTables(TABLE_PREFIX))) {
    	    $this->current_version = '2.0.2';
    	  } // if
      } // if
      return $this->current_version;
    } // currentVersion
    
    /**
     * Check if users password is OK. This function is aC version sensitive
     *
     * @param string $raw
     * @param string $from_database
     * @return boolean
     */
    function checkUserPassword($raw, $from_database) {
      if($this->currentVersion() == '1.0') {
        return $raw == $from_database;
      } else {
        return sha1( $raw) == $from_database;
      } // if
    } // checkUserPassword
    
    /**
     * Check if user is administrator by his role ID
     *
     * @param integer $role_id
     * @return boolean
     */
    function isUserAdministrator($role_id) {
      
      // activeCollab 1.0
      if($this->currentVersion() == '1.0') {
        $role_permission = $this->db->execute_one('SELECT value FROM ' . TABLE_PREFIX . 'role_permissions WHERE role_id = ? AND permission = ?', array($role_id, 'admin_access'));
        if($role_permission) {
          return (boolean) $role_permission['value'];
        } else {
          return false;
        } // if
      
      // Post 1.0
      } else {
        $role_permissions = $this->db->execute_one('SELECT permissions FROM ' . TABLE_PREFIX . 'roles WHERE id = ?', array($role_id));
        if(is_array($role_permissions) && isset($role_permissions['permissions']) && $role_permissions['permissions']) {
          return (boolean) array_var(unserialize($role_permissions['permissions']), 'admin_access', false);
        } else {
          return false;
        } // if
      } // if
      
    } // isUserAdministrator
    
    /**
     * Return script by group
     *
     * @param string $group
     * @return UpgradeScript
     */
    function getScriptByGroup($group) {
    	$files = get_files(UPGRADE_SCRIPT_PATH . '/scripts', 'php');
    	
    	$result = array();
    	if(is_foreachable($files)) {
    	  foreach($files as $file) {
    	    require_once $file;
    	    $basename = basename($file);
    	    
    	    $class_name = substr($basename, 0,  strpos($basename, '.'));
    	    
    	    $script = new $class_name($this);
    	    
    	    if($script->getGroup() == $group) {
    	      return $script;
    	    } // if
    	  } // foreach
    	} // if
    	
    	return null;
    } // getScript
    
    /**
     * Return list of upgrade scripts that are newer than $newer_than version
     *
     * @param string $newer_than
     * @return array
     */
    function availableScripts($newer_than) {
      $files = get_files(UPGRADE_SCRIPT_PATH . '/scripts', 'php');
    	
    	$result = array();
    	if(is_foreachable($files)) {
    	  
    	  // Skip old Upgrade_1, Upgrade_2 etc files
    	  $skip_files = array();
    	  for($i = 1; $i <= 9; $i++) {
    	    $skip_files[] = UPGRADE_SCRIPT_PATH . "/scripts/Upgrade_$i.class.php";
    	  } // for
    	  
    	  sort($files);
    	  
    	  foreach($files as $file) {
    	    if(in_array($file, $skip_files)) {
    	      continue; // skip old Upgrade_1, Upgrade_2 etc files
    	    } // if
    	    
    	    require_once $file;
    	    $basename = basename($file);
    	    
    	    $class_name = substr($basename, 0,  strpos($basename, '.'));
    	    
    	    $script = new $class_name($this);
    	    
    	    if(version_compare($script->from_version, $newer_than) >= 0) {
    	      $result[] = $script;
    	    } // if
    	  } // foreach
    	} // if
    	
    	return empty($result) ? null : $result;
    } // availableScripts
    
  }

?>