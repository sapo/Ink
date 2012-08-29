<?php

  /**
   * Upgrade script execution file
   * 
   * Async reqests are routes through this file
   *
   * @package activeCollab.upgrade
   */

  define('IN_UPGRADE_SCRIPT', true);
  define('UPGRADE_SCRIPT_PATH', dirname(__FILE__));;
  
  require UPGRADE_SCRIPT_PATH . '/include/include.php';
  
  sleep(1);
  switch(array_var($_POST, 'what')) {
    
    // Authenticate user and prepare upgrade steps
    case 'authenticate':
      $util = new UpgradeUtility();
      
      $email = trim(array_var($_POST, 'email'));
      $password = array_var($_POST, 'password');
      
      $auth = $util->authenticate($email, $password);
      if($auth !== true) {
        $util->authenticationError($auth, $email, $password);
      } // if
      
      $current_version = $util->currentVersion();
      $scripts = $util->availableScripts($current_version);
      
      // Nothing to do
      if(!is_foreachable($scripts)) {
        print '<p style="color: green; text-align: center; padding: 16px 0">Your setup is up to date!</p>';
        die();
      } // if
      
      // Get target version
      $final_version = 0;
      foreach($scripts as $script) {
        $final_version = max($script->to_version, $final_version);
      } // foreach
      
      require UPGRADE_SCRIPT_PATH . '/include/upgrade_steps.php';
      die();
      
      break;
      
    // Execute single upgrade step
    case 'execute_step':
      $group = array_var($_POST, 'group');
      $step = array_var($_POST, 'step');
      
      if(empty($group) || empty($step)) {
        die('Group and step are required');
      } // if
      
      $util = new UpgradeUtility();
      $script = $util->getScriptByGroup($group);
      if(instance_of($script, 'UpgradeScript')) {
        $execute = $script->$step();
        if($execute === true) {
          die('all_ok');
        } else {
          die('Error: ' . $execute);
        }
      } else {
        die("Failed to load gorup '$group'");
      } // if
      
      break;
      
    // Unknown action, nothing to do here
    default:
      die('Unknown action');
      
  } // switch

?>