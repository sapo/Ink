<?php

  /**
   * Init backup module
   *
   * @package activeCollab.modules.backup
   */
  
  define('BACKUP_MODULE', 'backup');
  define('BACKUP_MODULE_PATH', APPLICATION_PATH . '/modules/backup');
  
  define('BACKUP_PATH', WORK_PATH.'/backup');
  
  define('BACKUP_MODULE_SEND_ERROR_EMAIL', true);
  
  /**
   * Returns relative path
   *
   * @param string $path
   * @param string $reference_path
   * @return mixed
   */
  function backup_module_get_relative_path($path, $reference_path) {
    if (strpos($path,$reference_path) === false || strpos($path,$reference_path) !== 0) {
      return false;
    } // if
    
    return substr($path,strlen($reference_path));
  } // backup_module_get_relative_path
  
 /**
   * Copy folder tree and returns a true if all tree is copied and false if there was errors.
   * if $halt_on_errors is true, function will exit if there is error when copying file.
   * $errors is array of errors.
   *
   * @param string $source_dir Source Directory
   * @param string $destination_dir Destination Directory
   * @return boolean
   */
  function backup_module_copy_dir($source_dir, $destination_dir, $halt_on_errors, $errors) {
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
  			    return false;
  			  } // if
  			  $tmp_result = (backup_module_copy_dir($full_src_path, $dest_src_path, $halt_on_errors, $errors));
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
  } // backup_module_copy_dir
  
  /**
   * Create checksum of backup
   *
   * @param string $backup_name
   * @return string
   */
  function backup_module_calculate_checksum($backup_name) {
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
  } // backup_module_calculate_checksum
  
  /**
   * Calculate tables size (without indexes)
   *
   * @param string $table_prefix
   * @return float
   */
  function backup_module_calculate_database_size($table_prefix) {
    $status = db_execute("SHOW TABLE STATUS LIKE '$table_prefix%'");

    $total_size = 0;
    if (is_foreachable($status)) {
      foreach ($status as $table_status) {
      	$total_size+= ($table_status['Data_length']);
      } // foreach
    } // if
    
    return $total_size;
  } // backup_module_calculate_database_size
  
  
  /**
   * Remove backup folder
   *
   * @param string $backup_name
   * @return boolean
   */
  function backup_module_remove_backup($backup_name) {
    $backup_directory = BACKUP_PATH.'/'.$backup_name;
    if (!is_dir($backup_directory)) {
      return true;
    } // if
    return safe_delete_dir($backup_directory, BACKUP_PATH);
  } // backup_module_remove_backup
  
  /**
   * Send error log to administrator
   *
   * @param array $errors
   * @return boolean
   */
  function backup_module_log_error($errors, $send_email = false) {
    $log_message = is_foreachable($errors) ? implode("\n", $errors) : $errors;
   
    if ($send_email) {
      $mailer =& ApplicationMailer::mailer();
      
      $recipient = new Swift_Address();
      $recipient->setAddress(ADMIN_EMAIL);
      $recipient->setName('activeCollab admin');
      
      $sender = new Swift_Address();
      $sender->setAddress(ConfigOptions::getValue('notifications_from_email'));
      $sender->setName(ConfigOptions::getValue('notifications_from_name'));
      
      $tmp_message = "Automatic backup of activeCollab on ".ROOT_URL." failed.\n\r";
      $tmp_message.= "Backup returned these errors: \n\r\n\r";
      $tmp_message.= $log_message;
      
      $message = new Swift_Message();
      $message->setSubject('activeCollab automatic backup error log');
      $message->setData($tmp_message);
      $message->setContentType('text/plain');
      $mailer->send($message, $recipient, $sender);
    } // if
    
    log_message($log_message, LOG_LEVEL_ERROR, 'backup');
  } // backup_module_send_error_log
  
  /**
   * Checks if $backup_2 is newer than $backup_1
   *
   * @param array $backup_1
   * @param array $backup_2
   * @return boolean
   */
  function backup_module_compare_backup_dates($backup_1, $backup_2) {
    $timestamp_1 = $backup_1['timestamp'];
    $timestamp_2 = $backup_2['timestamp'];
    
    if ($timestamp_1 == $timestamp_2) {
      return 0;
    } // if
    return ($timestamp_1 > $timestamp_2) ? -1 : 1;
  } // backup_module_compare_backup_dates
  
  /**
   * Check if backup is valid
   *
   * @param string $backup_name
   * @return array
   */
  function backup_module_backup_is_valid($backup_name) {
    $full_path = BACKUP_PATH . '/' . $backup_name;
    
    $stats = array();
    
    $required_file = $full_path.'/restore_instructions.php';
    if (!is_file($required_file) && (filesize($required_file) <= 0)) {
      return new Error(lang('Backup is Corrupted'));
    } // if
    
    $required_file = $full_path.'/database.sql';
    if (!is_file($required_file) && (filesize($required_file) <= 0)) {
      return new Error(lang('Backup is Corrupted. Database backup is missing'));
    } // if
    
    $stats[] = lang('<strong>:filesize</strong> is file size of database backup', array('filesize' => format_file_size(filesize($required_file))));
    
    $folder = get_files($full_path.'/upload');
    $stats[] = lang('<strong>:count</strong> uploaded files backed up', array('count' => count($folder)));
    
    $folder = get_files($full_path.'/avatars');
    $stats[] = lang('<strong>:count</strong> avatars backed up', array('count' => floor(count($folder) / 2)));
    
    $folder = get_files($full_path.'/projects_icons');
    $stats[] = lang('<strong>:count</strong> projects icons backed up', array('count' => floor(count($folder) /2)));
    
    $folder = get_files($full_path.'/logos');
    $stats[] = lang('<strong>:count</strong> customer logos backed up', array('count' => floor(count($folder) /2)));
    
    return $stats;
  } // backup_module_backup_is_valid
  
  
  /**
   * Retrieves backups from backup folder and sort them in creation date
   *
   * @param string $folder
   * @return array
   */
  function backup_module_get_backups($folder) {
    $existing_backups = get_folders($folder);
    $existing_backups_list = array();
    if (is_foreachable($existing_backups)) {
      foreach ($existing_backups as $existing_backup) {
        $pathinfo = pathinfo($existing_backup);
        if (strpos($pathinfo['filename'], 'backup ') === 0) {
          $backup_name = $pathinfo['filename'];
          if (preg_match("/backup (.*)-(.*)-(.*) (.*)-(.*) GMT/", $backup_name, $results)) {
            $timestamp = mktime($results[4], $results[5], 0, $results[2], $results[3], $results[1]);
            $existing_backups_list[] = array(
              'time' => new DateTimeValue($timestamp),
              'timestamp' => $timestamp,
              'size' => dir_size($existing_backup),
              'complete' => backup_module_backup_is_valid($backup_name),
              'path' => $existing_backup,
            );
          } // if
        } // if
      } // foreach
    } // if

    // sort arrays by date
    usort($existing_backups_list, 'backup_module_compare_backup_dates');
    return $existing_backups_list;
  } // backup_module_get_backups
  
?>