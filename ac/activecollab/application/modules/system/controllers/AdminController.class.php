<?php

  /**
   * Base administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class AdminController extends ApplicationController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'admin';
    
    /**
     * Construct admin controller
     *
     * @param Request $request
     * @return AdminController
     */
    function __construct($request) {
      parent::__construct($request);
      
      // Turn off print button in entire administration
      $this->wireframe->print_button = false;
      
      if(!$this->logged_user->isAdministrator()) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->wireframe->addBreadCrumb(lang('Administration'), assemble_url('admin'));
      $this->wireframe->current_menu_item = 'admin';
    } // __construct
    
    /**
     * Show administration index page
     *
     * @param void
     * @return null
     */
    function index() {
      if(isset($this->application->version)) {
        $version = $this->application->version;
      } else {
        $version = '1.0';
      } // if
      
      $admin_sections = array(
        ADMIN_SECTION_SYSTEM => null,
        ADMIN_SECTION_MAIL   => null,
        ADMIN_SECTION_TOOLS  => null,
        ADMIN_SECTION_OTHER  => null,
      );
      event_trigger('on_admin_sections', array(&$admin_sections));

      $this->smarty->assign(array(
        'ac_version' => $version,
        'admin_sections' => $admin_sections,
        'php_version' => PHP_VERSION,
        'mysql_version' => db_version(),
        'support_url' => $support_url,
      ));
    } // index
    
  }

?>