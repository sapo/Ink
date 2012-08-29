<?php

  // Interit administration controller
  use_controller('admin', SYSTEM_MODULE);

  /**
   * Modules administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ModulesAdminController extends AdminController {
    
    /**
     * Name of this controller (underscore)
     *
     * @var string
     */
    var $controller_name = 'modules_admin';
    
    /**
     * Selected module
     *
     * @var Module
     */
    var $active_module;
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return ModulesAdminController
     */
    function __construct($request) {
      parent::__construct($request);
      $this->wireframe->addBreadCrumb(lang('Modules'), assemble_url('admin_modules'));
      
      $module_name = $this->request->get('module_name');
      if($module_name) {
        $this->active_module = Modules::findById($module_name);
        if(!instance_of($this->active_module, 'Module')) {
          $module_class = Inflector::camelize($module_name) . 'Module';
          $module_class_file = APPLICATION_PATH . "/modules/$module_name/$module_class.class.php";
          if(is_file($module_class_file)) {
            require_once $module_class_file;
            $this->active_module = new $module_class();
          } // if
        } // if
        
        if(instance_of($this->active_module, 'Module')) {
          $this->wireframe->addBreadCrumb($this->active_module->getDisplayName(), $this->active_module->getViewUrl());
          
          if($this->active_module->isInstalled()) {
            if($this->active_module->canBeUninstalled()) {
              $uninstall_message = lang('Your are about to uninstall :name module', array('name' => $this->active_module->getDisplayName())) . '. ';
              if($this->active_module->getUninstallMessage()) {
                $uninstall_message .= $this->active_module->getUninstallMessage() . '. ';
              } // if
              $uninstall_message .= lang('There is NO UNDO. Continue?');
              
              $this->wireframe->addPageAction(lang('Uninstall'), $this->active_module->getUninstallUrl(), null, array('method' => 'post', 'confirm' => $uninstall_message));
            } // if
          } else {
            $this->wireframe->addPageMessage(lang(':name module is not installed yet. Click on the Install button above this message to install this module', array('name' => $this->active_module->getDisplayName())));
            $this->wireframe->addPageAction(lang('Install'), $this->active_module->getInstallUrl());
          } // if
        } else {
          $this->httpError(HTTP_ERR_NOT_FOUND);
        } // if
      } else {
        $this->active_module = new Module();
      } // if
      
      $this->smarty->assign(array(
        'active_module' => $this->active_module,
      ));
    } // __construct
    
    /**
     * Show modules administration index page
     *
     * @param void
     * @return null
     */
    function index() {
      $this->smarty->assign(array(
        'modules' => Modules::findAll(), 
        'available_modules' => Modules::findNotInstalled()
      ));
    } // index
    
    /**
     * Show specific module details
     *
     * @param void
     * @return null
     */
    function module() {
      // Just display the template
    } // module
    
    /**
     * Install module
     *
     * @param void
     * @return null
     */
    function install() {
      if($this->active_module->isLoaded()) {
        $this->httpError(HTTP_ERR_CONFLICT, 'Module already installed');
      } // if
      
      $log = array();
      $this->smarty->assign(array(
        'can_be_installed' => $this->active_module->canBeInstalled($log),
        'installation_check_log' => $log,
      ));
      
      if($this->request->isSubmitted()) {
        if($this->active_module->isLoaded()) {
          $this->httpError(HTTP_ERR_NOT_FOUND);
        } // if
        
        $module_name = $this->request->get('module_name');
        $this->active_module->setName($module_name);
        
        if(!is_dir($this->active_module->getPath())) {
          $this->httpError(HTTP_ERR_NOT_FOUND);
        } // if
        
        $install = $this->active_module->install();
        if($install && !is_error($install)) {
          cache_remove('all_modules');
          cache_remove_by_pattern('lang_cache_for_*');
          
          flash_success(':name module is installed', array('name' => Inflector::humanize($module_name)));
        } else {
          flash_error('Failed to install :name module', array('name' => Inflector::humanize($module_name)));
        } // if
        
        $this->redirectToUrl($this->active_module->getViewUrl());
      } // if
    } // install
    
    /**
     * Uninstall
     *
     * @param void
     * @return null
     */
    function uninstall() {
      if($this->request->isSubmitted()) {
        if($this->active_module->isNew()) {
          $this->httpError(HTTP_ERR_NOT_FOUND);
        } // if
        
        if(!$this->active_module->canBeUninstalled()) {
          flash_error(':name module cannot be uninstalled', array('name' => $this->active_module->getName()));
          $this->redirectToReferer(assemble_url('admin_modules'));
        } // if
        
        $uninstall = $this->active_module->uninstall();
        if($uninstall && !is_error($uninstall)) {
          cache_remove('all_modules');
          
          flash_success(':name module is uninstalled', array('name' => Inflector::humanize($this->active_module->getName())));
        } else {
          flash_error('Failed to uninstall :name module', array('name' => Inflector::humanize($this->active_module->getName())));
        } // if
        
        $this->redirectToUrl($this->active_module->getViewUrl());
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // uninstall
  
  }

?>