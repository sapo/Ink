<?php

  // Extend modules admin
  use_controller('modules_admin', SYSTEM_MODULE);

  /**
   * Invoicing module administration controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class InvoicingModuleAdminController extends ModulesAdminController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'invoicing_module_admin';
    
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