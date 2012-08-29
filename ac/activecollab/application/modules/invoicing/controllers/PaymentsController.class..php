<?php

  /**
   * Main payments controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class PaymentsController extends ApplicationController {

    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'payments';

    /**
     * Selected payment
     *
     * @var Invoice
     */
    var $active_payment;

    /**
     * Construct payments controller
     *
     * @param Request $request
     * @return InvoicesController
     */
    function __construct($request) {
      parent::__construct($request);

      $payment_id = $this->request->getId('payment_id');
      if($payment_id) {
        $this->active_payment = Payments::findById($payment_id);
      } // if

      if(!instance_of($this->active_payment, 'Payment')) {
        $this->active_payment = new Payment();
      } // if

      $add_payment_url = assemble_url('payments_add');

      $this->wireframe->addPageAction(lang('New Payment'), $add_payment_url);
      $this->wireframe->addBreadCrumb(lang('Payments'), assemble_url('payments'));

      $this->smarty->assign(array(
        'active_payment' => $this->active_payment,
        'add_payment_url' => $add_payment_url,
      ));
    } // __construct

    /**
     * Show invoicing dashboard
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

      list($payments, $pagination) = Payments::paginateAll($page, $per_page);

      $this->smarty->assign(array(
        'payments' => $payments,
        'pagination' => $pagination,
      ));
    } // index

    /**
     * Show payment details
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_payment->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if

      $this->wireframe->addPageAction(lang('Send'), $this->active_payment->getSendUrl());
    } // view

    /**
     * Create a new payment
     *
     * @param void
     * @return null
     */
    function add() {
      $payment_data = $this->request->post('payment');
      if(!is_array($payment_data)) {
        $payment_data = array(

        );
      } // if
      $this->smarty->assign('payment_data', $payment_data);

      if($this->request->isSubmitted()) {
        db_begin_work();
        $this->active_payment->setAttributes($payment_data);
        $save = $this->active_payment->save();

        if($save && !is_error($save)) {
            db_commit();
            flash_success('Payment has been saved');
            $this->redirectTo('invoices');
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
      if($this->active_payment->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if

      $payment_data = $this->request->post('payment');
      if(!is_array($payment_data)) {
        $payment_data = array(
          'invoice_id' => $this->active_payment->getInvoiceId(),
          'amount' => $this->active_payment->getAmount(),
          'comment' => $this->active_payment->getComment(),
        );
      } // if

      $this->smarty->assign('payment_data', $payment_data);

      if($this->request->isSubmitted()) {
        $this->active_invoice->setAttributes($payment_data);
        $save = $this->active_payment->save();

        if($save && !is_error($save)) {
          flash_success('Payment has been updated');
          $this->redirectTo('invoices');
        } else {
          $this->smarty->assign('errors', $save);
        } // if
      } // if
    } // edit

    /**
     * Drop invoice
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * invoices shuld not be dropped,
     * just change of status shoul be allowed
     *
     * @param void
     * @return null
     *
     **/
    function delete() {

    } // delete

  }

?>