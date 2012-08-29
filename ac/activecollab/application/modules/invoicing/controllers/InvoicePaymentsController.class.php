<?php
  // we need invoices controller
  use_controller('invoices', INVOICING_MODULE);

  /**
   * Invoice payments controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class InvoicePaymentsController extends InvoicesController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'invoice_payments';
    
    /**
     * Selected invoice payment
     *
     * @var InvoicePayment
     */
    var $active_invoice_payment;
    
    /**
     * Construct invoice payments controller
     *
     * @param Request $request
     * @return InvoicePaymentsController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $invoice_payment_id = $this->request->getId('invoice_payment_id');
      if($invoice_payment_id) {
        $this->active_invoice_payment = InvoicePayments::findById($invoice_payment_id);
      } // if
      
      if(!instance_of($this->active_invoice_payment, 'InvoicePayment')) {
        $this->active_invoice_payment = new InvoicePayment();
      } // if
      
      $this->smarty->assign(array(
        'active_invoice_payment' => $this->active_invoice_payment,
      ));
    } // __construct
    
    /**
     * Show payments history
     *
     * @param void
     * @return null
     */
    function index() {
      $per_page = 50;
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if

      list($payments, $pagination) = InvoicePayments::paginateAll($page, $per_page);

      $this->smarty->assign(array(
        'payments' => group_by_month($payments, 'getPaidOn'),
        'pagination' => $pagination,
      ));
    } // index
    
    /**
     * Create a new payment
     *
     * @param void
     * @return null
     */
    function add() {
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $invoice_payment_data = $this->request->post('invoice_payment');
      if(!is_array($invoice_payment_data)) {
        $invoice_payment_data = array(
          'amount' => $this->active_invoice->getMaxPayment(),
          'paid_on' => new DateValue(),
        );
      } // if
      $this->smarty->assign('invoice_payment_data', $invoice_payment_data);
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_invoice_payment->setAttributes($invoice_payment_data);
        $this->active_invoice_payment->setInvoiceId($this->active_invoice->getId());
        $this->active_invoice_payment->setCreatedBy($this->logged_user);
        $this->active_invoice_payment->setCreatedOn(new DateTimeValue());
        
        $save = $this->active_invoice_payment->save();
        if($save && !is_error($save)) {
          db_commit();
          
          flash_success('Payment #:id has been added', array('id' => $this->active_invoice_payment->getId()));
          $this->redirectToUrl($this->active_invoice->getViewUrl());
        } else {
          db_rollback();
          $this->smarty->assign('errors', $save);
        } // if
      } // if
    } // add
    
    /**
     * Update existing payment
     *
     * @param void
     * @return null
     */
    function edit() {
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->active_invoice_payment->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_invoice_payment->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $invoice_payment_data = $this->request->post('invoice_payment');
      if(!is_array($invoice_payment_data)) {
        $invoice_payment_data = array(
          'amount' => $this->active_invoice_payment->getAmount(),
          'paid_on' => $this->active_invoice_payment->getPaidOn(),
          'comment' => $this->active_invoice_payment->getComment(),
        );
      } // if
      $this->smarty->assign('invoice_payment_data', $invoice_payment_data);
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        $old_amount = $this->active_invoice_payment->getAmount();
        
        $this->active_invoice_payment->setAttributes($invoice_payment_data);
        $this->active_invoice_payment->setAmount($old_amount); // don't allow overwrite
        $this->active_invoice_payment->setInvoiceId($this->active_invoice->getId());
        
        $save = $this->active_invoice_payment->save();
        if($save && !is_error($save)) {
          db_commit();
          
          flash_success('Payment #:id has been updated', array('id' => $this->active_invoice_payment->getId()));
          $this->redirectToUrl($this->active_invoice->getViewUrl());
        } else {
          db_rollback();
          $this->smarty->assign('errors', $save);
        } // if
      } // if
    } // edit
    
    /**
     * Remove existing payment
     *
     * @param void
     * @return null
     */
    function delete() {
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->active_invoice_payment->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_invoice_payment->canDelete($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isSubmitted()) {
        $delete = $this->active_invoice_payment->delete();
        if($delete && !is_error($delete)) {
          flash_success('Payment has been successfully deleted');
        } else {
          flash_error('Failed to delete selected payment');
        } // if
        
        $this->redirectToUrl($this->active_invoice->getViewUrl());
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // delete
    
  }

?>