<?php

  // Extend modules admin
  use_controller('modules_admin', SYSTEM_MODULE);

  /**
   * Documents module admin controller
   *
   * @package activeCollab.modules.documents
   * @subpackage controllers
   */
  class DocumentsModuleAdminController extends ModulesAdminController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'documents_module_admin';
    
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