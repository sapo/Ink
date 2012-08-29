<?php

  /**
   * Public interface file
   * 
   * @package activeCollab
   * @subpackage instance
   */
  
  if(DIRECTORY_SEPARATOR == '\\') {
    define('PUBLIC_PATH', str_replace('\\', '/', dirname(__FILE__)));
  } else {
    define('PUBLIC_PATH', dirname(__FILE__));
  } // if

  define('USE_INIT', true);
  define('INIT_MODULES', true);
  define('INIT_APPLICATION', true);
  define('HANDLE_REQUEST', true);
  
  if(is_dir(PUBLIC_PATH . '/installer')) {
    define('PUBLIC_FOLDER_NAME', substr(PUBLIC_PATH, strrpos(PUBLIC_PATH, '/') + 1));
    
    $installer_path = defined('NOT_THROUGH_PUBLIC') && NOT_THROUGH_PUBLIC ? PUBLIC_FOLDER_NAME . '/installer' : 'installer';
    
    print "<p>Hi,</p>\n
    <p>Please remove $installer_path folder. If you haven't installed activeCollab go to <a href=\"$installer_path\">$installer_path</a> and follow the on screen instructions.</p>\n
    <p>activeCollab</p>";
    die();
  } // if
  
  // Load configuration
  require_once realpath(PUBLIC_PATH . '/../config/config.php');
  
  // Maintenance message
  if(defined('MAINTENANCE_MESSAGE') && MAINTENANCE_MESSAGE) {
    header("HTTP/1.1 503 Service Unavailable");
    print '<h3>Service Unavailable</h3>';
    print '<p>Info: ' . MAINTENANCE_MESSAGE . '</p>';
    print '<p>&copy;' . date('Y');
    if(!LICENSE_COPYRIGHT_REMOVED) {
      print '. <a href="http://www.vbsupport.org/forum/index.php" title="NulleD By">FintMax</a>';
    } // if
    print '.</p>';
    die();
  } // if
  
  // Initialize application and handle request
  require_once ROOT . '/init.php';

?>