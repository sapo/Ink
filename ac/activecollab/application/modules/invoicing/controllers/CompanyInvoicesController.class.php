<?php

  // Extend company profile
  use_controller('companies', SYSTEM_MODULE);

  /**
   * Company invoices controller implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class CompanyInvoicesController extends CompaniesController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'company_invoices';
    
    /**
     * Selected invoice
     *
     * @var Invoice
     */
    var $active_invoice;
    
    /**
     * Construct company invoices controller
     *
     * @param Request $request
     * @return CompanyInvoicesController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if(!Invoice::canAccessCompanyInvoices($this->logged_user, $this->active_company)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->wireframe->current_menu_item = 'invoicing';
      
      $this->wireframe->page_actions = array();
      $this->wireframe->addBreadCrumb(lang('Invoices'), assemble_url('people_company_invoices', array('company_id' => $this->active_company->getId())));
      
      $invoice_id = $this->request->getId('invoice_id');
      if($invoice_id) {
        $this->active_invoice = Invoices::findById($invoice_id);
      } // if
      
      if(instance_of($this->active_invoice, 'Invoice')) {
        if($this->active_invoice->getCompanyId() != $this->active_company->getId()) {
          $this->httpError(HTTP_ERR_CONFLICT);
        } // if
        
        $this->wireframe->addBreadCrumb($this->active_invoice->getName(), $this->active_invoice->getCompanyViewUrl());
      } else {
        $this->active_invoice = new Invoice();
      } // if
      
      $this->smarty->assign(array(
        'active_invoice' => $this->active_invoice,
        'page_tab' => 'invoices',
      ));
    } // __construct
    
    /**
     * Show company invoices
     *
     * @param void
     * @return null
     */
    function index() {
      $per_page = 30;
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
      
      $status = $this->request->get('status') ? $this->request->get('status') : 'active';
      if ($status == 'active') {
        $invoice_status = INVOICE_STATUS_ISSUED;
      } else if ($status == 'paid') {
        $invoice_status = INVOICE_STATUS_BILLED;
      } else if ($status == 'canceled') {
        $invoice_status = INVOICE_STATUS_CANCELED;
      } // if

      list($invoices, $pagination) = Invoices::paginateByCompany($this->active_company, array($invoice_status), $page, $per_page, 'due_on ASC, created_on DESC');
      
      $this->smarty->assign(array(
        'invoices' => $invoices,
        'pagination' => $pagination,
        'status' => $status,
      ));
    } // index
    
    /**
     * Company payments
     * 
     * @param void
     * @return null
     */
    function payments() {
      $this->smarty->assign(array(
        'payments' => InvoicePayments::findByCompany($this->active_company),
      ));
    } // payments
    
    /**
     * Show invoice details
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->active_invoice->getStatus() == INVOICE_STATUS_DRAFT) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
    } // view
    
    /**
     * Render invoice PDF
     *
     * @param void
     * @return null
     */
    function pdf() {
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->active_invoice->getStatus() == INVOICE_STATUS_DRAFT) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      require_once(INVOICING_MODULE_PATH.'/models/InvoicePdf.class.php');
      InvoicePDF::download($this->active_invoice, lang('#:invoice_id.pdf', array('invoice_id' => $this->active_invoice->getName())));
      die();
    } // pdf
    
  }

?>