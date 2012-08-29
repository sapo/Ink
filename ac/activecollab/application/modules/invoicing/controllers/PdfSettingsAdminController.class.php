<?php
  // we need admin controller
  use_controller('admin');

  /**
   * PDF settings controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class PdfSettingsAdminController extends AdminController {

    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'pdf_settings_admin';
    
    /**
     * Contruct tax Invoicing settings controller
     *
     * @param Request $request
     * @return InvoicingSettingsAdminController
     */
    function __construct($request) {
      parent::__construct($request);

      $this->wireframe->addBreadCrumb(lang('Invoicing PDF Settings'), assemble_url('admin_invoicing_pdf'));
    } // __construct

    /**
     * Show invoicing settings panel
     *
     * @param void
     * @return null
     */
    function index() {
      require_once(INVOICING_MODULE_PATH.'/models/InvoicePdfGenerator.class.php');

      $paper_formats = array(
        PAPER_FORMAT_A4,
        PAPER_FORMAT_A3,
        PAPER_FORMAT_A5,
        PAPER_FORMAT_LETTER,
        PAPER_FORMAT_LEGAL,
      );
            
      $paper_orientations = array(
        PAPER_ORIENTATION_PORTRAIT,
        PAPER_ORIENTATION_LANDSCAPE,
      );
      
      $pdf_settings_data = $this->request->post('pdf_settings');
      if (!is_array($pdf_settings_data)) {
        $pdf_settings_data = array(
          'paper_format' => ConfigOptions::getValue('invoicing_pdf_paper_format'),
          'paper_orientation' => ConfigOptions::getValue('invoicing_pdf_paper_orientation'),
          'header_text_color' =>  ConfigOptions::getValue('invoicing_pdf_header_text_color'),
          'page_text_color' =>  ConfigOptions::getValue('invoicing_pdf_page_text_color'),
          'border_color' =>  ConfigOptions::getValue('invoicing_pdf_border_color'),
          'background_color' =>  ConfigOptions::getValue('invoicing_pdf_background_color'),
        );
      } // if
      
      if ($this->request->isSubmitted()) {
        db_begin_work();
        ConfigOptions::setValue('invoicing_pdf_paper_format', array_var($pdf_settings_data, 'paper_format', 'A4'));
        ConfigOptions::setValue('invoicing_pdf_paper_orientation', array_var($pdf_settings_data, 'paper_orientation', 'Portrait'));
        ConfigOptions::setValue('invoicing_pdf_header_text_color', array_var($pdf_settings_data, 'header_text_color', '000000'));
        ConfigOptions::setValue('invoicing_pdf_page_text_color', array_var($pdf_settings_data, 'page_text_color', '000000'));
        ConfigOptions::setValue('invoicing_pdf_border_color', array_var($pdf_settings_data, 'border_color', '000000'));
        ConfigOptions::setValue('invoicing_pdf_background_color', array_var($pdf_settings_data, 'background_color', 'FFFFFF'));
        db_commit();
        flash_success('Successfully modified PDF settings');
        $this->redirectTo('admin_invoicing_pdf');
      } // if
      
      $this->smarty->assign(array(
        'paper_formats' => $paper_formats,
        'paper_orientations' => $paper_orientations,
        'pdf_settings_data' => $pdf_settings_data,
      ));
    } // index
  } // PdfSettingsAdminController

?>