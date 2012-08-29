<?php

  // Extend modules admin
  use_controller('modules_admin', SYSTEM_MODULE);

  /**
   * Status module admin controller
   *
   * @package activeCollab.modules.status
   * @subpackage controllers
   */
  class StatusModuleAdminController extends ModulesAdminController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'status_module_admin';
    
    /**
     * Show module details page
     *
     * @param void
     * @return null
     */
    function module() {
      $this->smarty->assign('roles', Roles::findSystemRoles());
    } // module
    
  }

?>