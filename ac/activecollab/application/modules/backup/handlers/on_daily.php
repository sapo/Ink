<?php
  /**
   * Backup module on_daily event handler
   *
   * @package activeCollab.modules.backup
   * @subpackage handlers
   */

  /**
   * do daily backup
   *
   * @param null
   * @return null
   */
  function backup_handle_on_daily() {
    $start_time = time();
    
    if (!ConfigOptions::getValue('backup_enabled')) {
      return true;
    } // if
        
    // check if backup path exists and if it's writable
    recursive_mkdir(BACKUP_PATH, 0777, WORK_PATH);
    if (!is_dir(BACKUP_PATH) || !folder_is_writable(BACKUP_PATH)) {
      backup_module_log_error('Backup path ('.BACKUP_PATH.') does not exists or it is not writable', BACKUP_MODULE_SEND_ERROR_EMAIL);
      return false;
    } // if
    
    $folder_name = "backup ".date('Y-m-d H-i')." GMT";
    
    $backup_dir = BACKUP_PATH . '/' . $folder_name;
    
    // check if backup already exists
    if (is_dir($backup_dir)) {
      backup_module_log_error("Backup already exists ($folder_name)", BACKUP_MODULE_SEND_ERROR_EMAIL);
      return false;
    } // if
    
    // try to create backup directory
    if (!recursive_mkdir($backup_dir, 0777, WORK_PATH)) {
      backup_module_log_error("Could not create backup folder ($backup_dir)", BACKUP_MODULE_SEND_ERROR_EMAIL);
      return false;
    } // if
    chmod($backup_dir, 0777);

    // backup database (all tables that starts with TABLE_PREFIX)
    $tables = db_list_tables(TABLE_PREFIX);
    if (is_foreachable($tables)) {
      $result = db_dump_tables($tables, $backup_dir.'/database.sql');
      if (is_error($result)) {
        safe_delete_dir($backup_dir, BACKUP_PATH);
        backup_module_log_error($result->getMessage(), BACKUP_MODULE_SEND_ERROR_EMAIL);
        return false;       
      } // if
    } else {
      safe_delete_dir($backup_dir, BACKUP_PATH);
      backup_module_log_error("Database specified in config.php file does not have exportable tables. Check your config settings", BACKUP_MODULE_SEND_ERROR_EMAIL);
      return false;
    }
    
    // backup uploads
    $errors = array();
    $result = backup_module_copy_dir(UPLOAD_PATH, $backup_dir.'/upload', true, $errors);
    if (!$result) {
      safe_delete_dir($backup_dir, BACKUP_PATH);
      backup_module_log_error($errors, BACKUP_MODULE_SEND_ERROR_EMAIL);
      return false;
    } // if
    
    // backup project icons
    $errors = array();
    $result = backup_module_copy_dir(PUBLIC_PATH.'/projects_icons', $backup_dir.'/projects_icons', true, $errors);
    if (!$result) {
      safe_delete_dir($backup_dir, BACKUP_PATH);
      backup_module_log_error($errors, BACKUP_MODULE_SEND_ERROR_EMAIL);
      return false;
    } // if
    
    // backup avatars
    $errors = array();
    $result = backup_module_copy_dir(PUBLIC_PATH.'/avatars', $backup_dir.'/avatars', true, $errors);
    if (!$result) {
      safe_delete_dir($backup_dir, BACKUP_PATH);
      backup_module_log_error($errors, BACKUP_MODULE_SEND_ERROR_EMAIL);
      return false;
    } // if
    
    // backup logos
    $errors = array();
    $result = backup_module_copy_dir(PUBLIC_PATH.'/logos', $backup_dir.'/logos', true, $errors);
    if (!$result) {
      safe_delete_dir($backup_dir, BACKUP_PATH);
      backup_module_log_error($errors, BACKUP_MODULE_SEND_ERROR_EMAIL);
      return false;
    } // if
    
    $app =& application();
    $checksum = backup_module_calculate_checksum($folder_name);
    $backup_note = "<?php \n/* \n";
    $backup_note.= 'Backup is created with activeCollab v'.$app->version.' on '.date(DATETIME_MYSQL, $start_time)."\n\n";
    $backup_note.= "To restore system using this backup, visit this page: \n".ROOT_URL.'/restore.php?backup='.urlencode($folder_name).'&checksum='.$checksum;
    $backup_note.= "\n*/\n?>";
    
    if (!file_put_contents($backup_dir.'/restore_instructions.php', $backup_note)) {
      safe_delete_dir($backup_dir, BACKUP_PATH);
      backup_module_log_error("Could not create restore instructions for backup.", BACKUP_MODULE_SEND_ERROR_EMAIL);
      return false;
    } // if
    
    // remove old backups
    $how_many_backups = ConfigOptions::getValue('backup_how_many_backups');
    $how_many_backups = (int) $how_many_backups <=0 ? 5 : $how_many_backups;
    
    $folders_in_backup_directory = backup_module_get_backups(BACKUP_PATH);
    if (count($folders_in_backup_directory) > $how_many_backups) {
      $old_backups = array_splice($folders_in_backup_directory, -(count($folders_in_backup_directory) - $how_many_backups));
      foreach ($old_backups as $old_backup) {
      	safe_delete_dir($old_backup['path'], BACKUP_PATH);
      } // foreach
    } // if
       
    log_message('Daily backup created', LOG_LEVEL_INFO, 'backup');
  } // backup_handle_on_daily
?>