<?php
  // we need admin controller
  use_controller('admin');
  
  /**
   * Manages source settings
   * 
   * @package activeCollab.modules.source
   * @subpackage controllers
   *
   */
  class SourceAdminController extends AdminController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'source_admin';
        
    /**
     * Controller constructor
     *
     */
    function __construct($request) {
      parent::__construct($request);
      require_once(SOURCE_MODULE_PATH.'/engines/subversion.class.php');
      $this->wireframe->addBreadCrumb(lang('Source Module'));
    } // __construct
    
    /**
     * Settings form
     * 
     * @param void
     * @return null
     */
    function index() {
      js_assign('test_svn_url', assemble_url('admin_source_test_svn'));
      
      $source_data = $this->request->post('source');
      if (!is_foreachable($source_data)) {
        $source_data = array(
          'svn_path' => ConfigOptions::getValue('source_svn_path'),
          'svn_config_dir' => ConfigOptions::getValue('source_svn_config_dir')
        );
      } // if
      
      if ($this->request->isSubmitted()) {
        $svn_path = array_var($source_data, 'svn_path', null);
        $svn_path = $svn_path ? with_slash($svn_path) : null;
        ConfigOptions::setValue('source_svn_path', $svn_path);
        
        $svn_config_dir = array_var($source_data, 'svn_config_dir') == '' ? null : array_var($source_data, 'svn_config_dir');
        ConfigOptions::setValue('source_svn_config_dir', $svn_config_dir);
        
        flash_success("Source settings successfully saved");
        $this->redirectTo('admin_source');
      } // if
      
      if (!RepositoryEngine::executableExists()) {
        $this->wireframe->addPageMessage(lang("SVN executable not found. You won't be able to use this module"), 'error');
      } // if
      
      $this->smarty->assign(array(
        'source_data' => $source_data,
      ));
    } // function
    
    /**
     * Ajax that will return response from command line
     *
     * @param void
     * @return null
     */
    function test_svn() {
      $path = array_var($_GET, 'svn_path', null);
      $check_executable = RepositoryEngine::executableExists($path);
      
      echo $check_executable === true ? 'true' : $check_executable;
      die();
    } // function
    
  } // SourceAdminController

?>