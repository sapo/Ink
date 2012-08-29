<?php

  // We need admin controller
  use_controller('admin');

  /**
   * Company identity controller implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class CompanyIdentitySettingsAdminController extends AdminController {

    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'company_identity_settings_admin';
    
    /**
     * Contruct tax Invoicing settings controller
     *
     * @param Request $request
     * @return InvoicingSettingsAdminController
     */
    function __construct($request) {
      parent::__construct($request);

      $this->wireframe->addBreadCrumb(lang('Invoicing Company Identity Settings'), assemble_url('admin_invoicing_company_identity'));
    } // __construct
    
    /**
     * Save company details info
     * 
     * @param void
     * @return void
     */
    function index() {
      $brand_path = PUBLIC_PATH . '/brand';
      $default_image_name = 'invoicing_logo.jpg';
      $default_full_image_name = $brand_path . '/' . $default_image_name;
      if (!folder_is_writable($brand_path)) {
        $brand_folder_writable = false;
        $this->wireframe->addPageMessage(lang('Brand folder is not writable (:brand_folder). You will not be able to upload company logo.', array('brand_folder' => $brand_path)), PAGE_MESSAGE_WARNING);
      } // if
      
      $company_data = $this->request->post('company');
      if (!is_foreachable($company_data)) {
        $company_data = array(
          'name' => ConfigOptions::getValue('invoicing_company_name'),
          'details' => ConfigOptions::getValue('invoicing_company_details'),
        );
      } // if
      
      if ($this->request->isSubmitted()) {
        $errors = new ValidationErrors();
        db_begin_work();
        
        $company_name = trim(array_var($company_data, 'name'));
        $company_details = trim(array_var($company_data, 'details'));
        
        if (!$company_name || !$company_details) {
          if (!$company_name) {
            $errors->addError(lang('Company name is required'), 'company_name');
          } // if
          if (!$company_details) {
            $errors->addError(lang('Company details are required'), 'company_details');
          } // if
        } else {         
          // copy and convert logo
          $logo_file = array_var($_FILES, 'company_logo', null);
          if ($logo_file['name']) {
            $pathinfo = pathinfo($logo_file['name']);
            
            do {
              $new_filename = make_string(30) . '.' . array_var($pathinfo, 'extension');
              $new_file_full_path = $brand_path . '/' . $new_filename;
            } while (is_file($new_file_full_path));
            
            if (move_uploaded_file($logo_file['tmp_name'], $new_file_full_path)) {
              scale_image($new_file_full_path, $new_file_full_path, 600, 150, IMAGETYPE_JPEG, 100);
            } else {
              $errors->addError(lang('Could not upload company logo'), 'company_logo');
            } // if          
          } // if
          $company_logo_url = get_company_invoicing_logo_url();
          db_commit();
        } // if
        
        if (!$errors->hasErrors()) {
          // set config options
          ConfigOptions::setValue('invoicing_company_name', $company_name);
          ConfigOptions::setValue('invoicing_company_details', $company_details);
          @unlink($default_full_image_name);
          rename($new_file_full_path, $default_full_image_name);
          flash_success('Company identity successfully modified');
          $this->redirectTo('admin_invoicing_company_identity');
          db_commit();
        } else {
          @unlink($new_file_full_path);
          db_rollback();
          $this->smarty->assign('errors', $errors);
        } // if
      } // if
      
      $company_logo_url = get_company_invoicing_logo_url();
      
      $this->smarty->assign(array(
        'company_data' => $company_data,
        'company_logo_url' => $company_logo_url,
      ));      
    } // index
    
  }

?>