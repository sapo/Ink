<?php
  if (!count($_POST)) {
    $backup_name = isset($_GET['backup']) ? $_GET['backup'] : null;
    $checksum = isset($_GET['checksum']) ? $_GET['checksum'] : null;
?>
﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <title>activeCollab restore</title>
      <style type="text/css">
        html {
          padding: 20px 10px 0px 30px;
        }
        
        body {
          font-family: 'Lucida Grande';
          font-size: 12px;
          margin-right: 30px;
          border-left: 1px solid #ddd;
          padding: 15px 20px 15px 20px;
        }
        
        h2 {
          margin: 0px 0px 15px;
        }
      </style>
  </head>
  <body>
    <h2>Restore activeCollab</h2>
    <p>Click on the button to perform activeCollab restoration.</p>
    <p><strong>IMPORTANT: When you do restore, all data of activeCollab installation in which you are restoring backup, will be lost.</strong></p>
    <form method="POST" action="#">
       <input type="hidden" name="backup" value="<?=$backup_name?>" />
       <input type="hidden" name="checksum" value="<?=$checksum?>" />
       <button type="submit">Perform Restore<?=$backup_name ? substr($backup_name, 6) : null?></button>
    </form>
  </body>
</html>
<?
  } else {
    /**
     * Calculate backup checksum
     *
     * @param string $backup_name
     * @return float
     */
    function calculate_checksum($backup_name) {
      $folders = array(
        BACKUP_PATH."/$backup_name/upload",
        BACKUP_PATH."/$backup_name/projects_icons",
        BACKUP_PATH."/$backup_name/avatars",
        BACKUP_PATH."/$backup_name/logos",
      );
      
      $files = array(
        BACKUP_PATH."/$backup_name/database.sql",
      );
          
      $total_file_size = 0;
      foreach ($folders as $folder) {
      	$total_file_size+= dir_size($folder);
      } // foreach
      
      foreach ($files as $file) {
      	$total_file_size+= filesize($file);
      } // foreach
      
      return md5($total_file_size);
    } // calculate_checksum
    
    /**
     * Returns forbidden header
     *
     */
    function forbidden() {
        header("HTTP/1.1 403 Forbidden");
        print '<h1>Forbidden</h1>';
        die();
    } // forbidden
    
    /**
     * Returns not found header
     *
     */
    function not_found() {
        header("HTTP/1.1 404 Not Found");
        print '<h1>Not Found</h1>';
        die();
    } // forbidden
    
    /**
     * copy directory, and log errors in $errors variable
     *
     * @param string $source_dir
     * @param string $destination_dir
     * @param boolean $halt_on_errors
     * @param array $errors
     * @return boolean
     */
    function special_copy_dir($source_dir, $destination_dir, $halt_on_errors, &$errors) {
      if (!is_dir($source_dir)) {
        $errors[] = "Source directory ($source_dir) does not exists";
        return false;
      } else if (!is_dir($destination_dir)) {
        if (!recursive_mkdir($destination_dir, 0777, WORK_PATH)) {
          $errors[] = "Could not create destination directory: $source_dir";
        	return false;
        } // if
      } // if
  
      $result = true;
         
    	$dh = opendir($source_dir);
    	while($file = readdir($dh)) {
    		if(($file != ".") && ($file != "..")) {
    			$full_src_path = $source_dir . "/" . $file;
    			$dest_src_path = $destination_dir . "/" . $file;
    			if(!is_dir($full_src_path)) {
    			  $tmp_result = copy($full_src_path, $dest_src_path);
    			  if (!$tmp_result) {
    			    $errors[] = 'Failed to copy file: '.$full_src_path;
    			    if ($halt_on_errors) {
    			      closedir($dh);
    			      return false;
    			    } // if
    			  } else {
    			    $result = $result && $tmp_result;  
    			  } // if
    			} else {
    			  if (!recursive_mkdir($dest_src_path, 0777, WORK_PATH)) {
    			    $errors[] = 'Could not create directory: '.$dest_src_path;
    			    if ($halt_on_errors) {
    			      closedir($dh);
    			      return false;
    			    } // if
    			  } // if
    			  $tmp_result = (special_copy_dir($full_src_path, $dest_src_path, $halt_on_errors, $errors));
    			  if (!$tmp_result && $halt_on_errors) {
    			      closedir($dh);
    			      return false;
    			  } // if
            $result = $result && $tmp_result;  
    			} // if
    		} // if
    	} // while
    	closedir($dh);
    	return $result;
    } // special_copy_dir
  
    /**
     * Restore from backup
     */
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    require_once '../config/config.php';
    require_once ROOT . '/angie.php';
    
    require_once ANGIE_PATH . '/functions/general.php'; 
    require_once ANGIE_PATH . '/functions/environment.php'; 
    require_once ANGIE_PATH . '/functions/files.php'; 
    require_once ANGIE_PATH . '/functions/utf.php'; 
    
    require_once ANGIE_PATH . '/classes/AngieObject.class.php'; 
    require_once ANGIE_PATH . '/classes/Error.class.php'; 
    require_once ANGIE_PATH . '/classes/ErrorCollector.class.php'; 
    require_once ANGIE_PATH . '/classes/logger/init.php'; 
    require_once ANGIE_PATH . '/classes/database/init.php'; 
    require_once ANGIE_PATH . '/classes/cache/init.php'; 
    
    $backup_name = array_var($_POST, 'backup', null);
    $checksum = array_var($_POST, 'checksum', null);
    
    if (!$backup_name || !$checksum) {
      forbidden();
    } // if
    
    define('BACKUP_PATH', WORK_PATH.'/backup');
    
    $backup_dir = BACKUP_PATH . '/' . urldecode($backup_name);
    
    if (!is_dir($backup_dir)) {
      not_found();
    } // if
    
    $calculated_checksum = calculate_checksum($backup_name);
    
    if ($calculated_checksum != $checksum) {
      forbidden();
    } // if
    
    $database_dir = $backup_dir.'/database';
    
    if (!db_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, true, DB_CHARSET)) {
      die('Could not connect to database. Check activeCollab database settings');
    } // if
    
    $database_file = $backup_dir.'/database.sql';
    if (!is_file($database_file)) {
      echo "<p>Could not restore backup. Database dump is missing</p>";
      die();
    } // if
    
    $restore_database = db_import($database_file);
    if (!$restore_database) {
      echo 'Cannot import database: Unknown Error';
      die();
    } else if (is_error($restore_database)) {
      echo 'Cannot import database: '.$restore_database->getMessage();
      die();
    } // if
      	
    // restore files
  	$errors = array();
  	special_copy_dir($backup_dir.'/upload', UPLOAD_PATH, false, $errors);
  	special_copy_dir($backup_dir.'/projects_icons', ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/projects_icons', false, $errors);
  	special_copy_dir($backup_dir.'/avatars', ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/avatars', false, $errors);
  	special_copy_dir($backup_dir.'/logos', ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/logos', false, $errors);
  	
  	// clean cache
  	cache_use_backend(CACHE_BACKEND, array('lifetime' => CACHE_LIFETIME));
  	cache_clear();
  	
  	if (is_foreachable($errors)) {
  	  echo "<p>Backup restored, but with some errors (warnings)</p>";
  	  echo "<ul>";
  	  foreach ($errors as $error) {
  	   echo "<li>$error</li>";	
  	  } // foreach
  	  echo "</ul>";
  	} else {
  	  echo "<p>Backup restored!</p>";
  	} // if
  } // if
?>