<?php
  // we need admin acontroller
  use_controller('admin');
  
  /**
   * Administration settings for Public Submit Module
   * 
   * @package activeCollab.modules.public_submit
   * @subpackage controllers 
   */
  class PublicSubmitAdminController extends AdminController {
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name='public_submit_admin';
    
    /**
     * Is GD library loaded
     *
     * @var boolean
     */
    var $gd_loaded = true;
    
    /**
     * Construct PublicSubmitAdminController
     * 
     * @param string $request
     * @return PublicSubmitAdminController
     *
     */
    function __construct($request) {
      parent::__construct($request);
      $this->smarty->assign(array(
        "public_submit_settings_url" =>  assemble_url('admin_settings_public_submit'),
      ));

      if (!(extension_loaded('gd') || extension_loaded('gd2')) || !function_exists('imagefttext')) {
        $this->gd_loaded = false;
      } // if
      
      $this->smarty->assign(array(
        'public_submit_url' => assemble_url('public_submit'),
        'public_submit_enabled' => ConfigOptions::getValue('public_submit_enabled'),
        'public_submit_captcha_enabled' => ConfigOptions::getValue('public_submit_enable_captcha'),
        'public_submit_project' => Projects::findById(ConfigOptions::getValue('public_submit_default_project')),
        'gd_loaded' => $this->gd_loaded,
      ));
      return $this;
    } // __construct
    
    /**
     * PublicSubmitAdmin index page
     *
     */
    function index() {
      $public_submit_data = $this->request->post('public_submit');
      if(!is_array($public_submit_data)) {
        $public_submit_data = array(
          'project_id' => ConfigOptions::getValue('public_submit_default_project'),
          'enabled' => ConfigOptions::getValue('public_submit_enabled'),
          'captcha' => ConfigOptions::getValue('public_submit_enable_captcha'),
        );
      } // if
      $this->smarty->assign(array(
        'public_submit_data' => $public_submit_data,
      ));
      
      if ($this->request->isSubmitted()){
        ConfigOptions::setValue('public_submit_default_project', array_var($public_submit_data, 'project_id', null));
        ConfigOptions::setValue('public_submit_enabled', array_var($public_submit_data, 'enabled', null));
        ConfigOptions::setValue('public_submit_enable_captcha', array_var($public_submit_data, 'captcha', null));
        flash_success('Public Submit settings have been updated');
        $this->redirectTo('admin_settings_public_submit');
      } // if
    } // index
    
    
  } // PublicSubmitAdminController