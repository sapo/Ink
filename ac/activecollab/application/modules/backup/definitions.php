<?php

  /**
   * Backup module definitions
   *
   * @package activeCollab.modules.backup
   */
  
  return array(
  
    // Config options
    'config_options' => array(
      new ConfigOptionDefinition('backup_how_many_backups', 'backup', 'system', 5),
      new ConfigOptionDefinition('backup_enabled', 'backup', 'system', false),
    ),
  
  );

?>