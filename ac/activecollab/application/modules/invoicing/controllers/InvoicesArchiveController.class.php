<?php
  // we need invoices controller
  use_controller('invoices', INVOICING_MODULE);

  /**
   * Invoices archive controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class InvoicesArchiveController extends InvoicesController {
    
    /**
     * Name of this controller
     *
     * @var string
     */
    var $controller_name = 'invoices_archive';
    
    /**
     * Construct invoices archive controller
     *
     * @param Request $request
     * @return InvoicesArchiveController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->wireframe->addBreadCrumb(lang('Archive'), assemble_url('invoices_archive'));
    } // __construct
    
    /**
     * Show archive page
     *
     * @param void
     * @return null
     */
    function index() {
      $this->smarty->assign('invoiced_companies', Invoices::findInvoicedCompaniesInfo(array(INVOICE_STATUS_BILLED, INVOICE_STATUS_CANCELED)));
    } // index
    
    /**
     * Show billed / canceled company invoices
     *
     * @param void
     * @return null
     */
    function company() {
      
      $status = $this->request->get('status') ? $this->request->get('status') : 'billed';
      $company = null;
      
      $company_id = $this->request->getId('company_id');
      if($company_id) {
        $company = Companies::findById($company_id);
      } // if
      
      if(instance_of($company, 'Company')) {
        $this->wireframe->addBreadCrumb($company->getName(), assemble_url('company_invoices', array('company_id' => $company->getId())));
      } else {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if ($status == 'canceled') {
        $invoices = group_invoices_by_currency(Invoices::findByCompany($company, array(INVOICE_STATUS_CANCELED), 'closed_on DESC'));
      } else {
        $invoices = group_invoices_by_currency(Invoices::findByCompany($company, array(INVOICE_STATUS_BILLED), 'closed_on DESC'));
      } // if
      
      $this->smarty->assign(array(
        'company' => $company,
        'invoices' => $invoices,
        'status' => $status
      ));
    } // company
    
  }

?>