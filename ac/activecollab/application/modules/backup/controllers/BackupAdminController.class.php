<?php
  // we need admin controller
  use_controller('admin');
  
  /**
   * Manages backup settings
   * 
   * @package activeCollab.modules.backup
   * @subpackage controllers
   *
   */
  class BackupAdminController extends AdminController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'backup_admin';
    
    /**
     * Is backup enabled
     *
     * @var true
     */
    var $backup_enabled = false;
    
    /**
     * How many backups to store
     * if 0, unlimited backups
     *
     * @var integer
     */
    var $how_many_backups = 5;
    
    /**
     * Controller constructor
     *
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->backup_enabled = (boolean) ConfigOptions::getValue('backup_enabled');
      $this->how_many_backups = (int) ConfigOptions::getValue('backup_how_many_backups');
      
      $total_size = dir_size(UPLOAD_PATH);
      $total_size+= dir_size(PUBLIC_PATH.'/avatars');
      $total_size+= dir_size(PUBLIC_PATH.'/projects_icons');
      $total_size+= dir_size(PUBLIC_PATH.'/logos');
      $total_size+= backup_module_calculate_database_size(TABLE_PREFIX) * 1.1685;
      
      $existing_backups = backup_module_get_backups(BACKUP_PATH);

      $this->smarty->assign(array(
        'backup_admin_url' => assemble_url('backup_admin'),
        'backup_enabled'  => $this->backup_enabled,
        'backup_how_many_backups' => $this->how_many_backups,
        'total_size' => $total_size,
        'backup_dir_size' => dir_size(BACKUP_PATH),
        'existing_backups'  => $existing_backups,
      ));
    } // __construct
    
    /**
     * Main Backup page
     *
     */
    function index() {
      $backup_data = $this->request->post('backup');
      if (!is_array($backup_data)) {
        $backup_data = array(
          'enabled' => $this->backup_enabled,
          'how_many_backups' => $this->how_many_backups,
        );
      } // if
      
      $this->smarty->assign(array(
        'backup_data' => $backup_data,
        'how_many_values' => array(
          3, 5, 10, 15, 30, 60
        ),
      ));
      
      if ($this->request->isSubmitted()) {
        ConfigOptions::setValue('backup_enabled', (boolean) array_var($backup_data, 'enabled', 0));
        $how_many = (integer) array_var($backup_data, 'how_many_backups', 5);
        ConfigOptions::setValue('backup_how_many_backups', $how_many < 0 ? 5 : $how_many);
        
        flash_success('Backup settings have been updated');
        $this->redirectTo('admin');
      } // if
    } // index
    
  } // BackupAdminController

?>