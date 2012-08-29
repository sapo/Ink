<?php

  // Extend modules admin
  use_controller('modules_admin', SYSTEM_MODULE);

  /**
   * Timetracking module document administration
   *
   * @package activeCollab.modules.timetracking
   * @subpackage controllers
   */
  class TimetrackingModuleAdminController extends ModulesAdminController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'timetracking_module_admin';
    
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