<?php

  /**
   * InvoicePayment class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoicePayment extends BaseInvoicePayment {

    /**
     * Parent invoice
     *
     * @var Invoice
     */
    var $invoice = false;

    /**
     * Return parent invoice
     *
     * @param void
     * @return Invoice
     */
    function getInvoice() {
      if($this->invoice === false) {
        $this->invoice = Invoices::findById($this->getInvoiceId());
      } // if
      return $this->invoice;
    } // getInvoice

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if $user can edit this payment
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
      $invoice = $this->getInvoice();
      return ($invoice->isIssued() || $invoice->isBilled()) && $user->getSystemPermission('can_manage_invoices');
    } // canEdit

    /**
     * Returns true if $user can delete this payment
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      $invoice = $this->getInvoice();
      return ($invoice->isIssued() || $invoice->isBilled()) && $user->getSystemPermission('can_manage_invoices');
    } // canDelete

    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Return edit payment ID
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('invoice_payment_edit', array('invoice_id' => $this->getInvoiceId(), 'invoice_payment_id' => $this->getId()));
    } // getEditUrl

    /**
     * Return delete payment ID
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('invoice_payment_delete', array('invoice_id' => $this->getInvoiceId(), 'invoice_payment_id' => $this->getId()));
    } // getDeleteUrl

    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------

    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if(!$this->validatePresenceOf('paid_on')) {
        $errors->addError(lang('Payment day is required'), 'paid_on');
      } // if

      if($this->validatePresenceOf('invoice_id')) {
        $invoice = $this->getInvoice();
        if(instance_of($invoice, 'Invoice')) {
          
          // Validate amount, only for new invoices
          if($this->isNew()) {
            if($this->validatePresenceOf('amount')) {
              if($this->getAmount() > 0) {
                if($this->getAmount() > $invoice->getMaxPayment()) {
                  $errors->addError(lang('Amount too large'), 'amount');
                } // if
              } else {
                $errors->addError(lang('Payment amounts needs to be larger than 0'), 'amount');
              } // if
            } else {
              $errors->addError(lang('Payment amount is required'), 'amount');
            } // if
          } // if
          
        } else {
          $errors->addError(lang('Invoice is required'), 'invoice_id');
        } // if
      } else {
        $errors->addError(lang('Invoice is required'), 'invoice_id');
      } // if
    } // validate
    
    /**
     * Save this payment
     *
     * @param void
     * @return null
     */
    function save() {
      db_begin_work();
      
      $invoice = $this->getInvoice();
      if(!instance_of($invoice, 'Invoice')) {
        return new Error('$invoice is not valid instance of Invoice class', true);
      } // if
      
      $save = parent::save();
      if($save && !is_error($save)) {
        if($invoice->getMaxPayment(false) == 0 && $invoice->isIssued()) {
          $invoice->setStatus(INVOICE_STATUS_BILLED, get_logged_user(), $this->getPaidOn());
          $save = $invoice->save();
          if($save && !is_error($save)) {
            $logged_user = get_logged_user();            
            $issued_to_user = $invoice->getIssuedTo();
            if (instance_of($issued_to_user, 'User')) {
              $notify_users = array($logged_user);
              if ($issued_to_user->getId() != $logged_user->getId()) {
                $notify_users[] = $issued_to_user;
              } // if
              
              ApplicationMailer::send($notify_users, 'invoicing/billed', array(
                'closed_by_name' => $logged_user->getDisplayName(),
                'closed_by_url'  => $logged_user->getViewUrl(),
                'invoice_number' => $invoice->getNumber(), 
                'invoice_url' => $invoice->getCompanyViewUrl(),
              ));
            } // if
            
            db_commit();
            return true;
          } else {
            db_rollback();
            return $save;
          } // if
        } // if
        
        db_commit();
        return true;
      } else {
        db_rollback();
        return $save;
      } // if
    } // save
    
    /**
     * Drop this payment
     *
     * @param void
     * @return boolean
     */
    function delete() {
      db_begin_work();
      
      $invoice = $this->getInvoice();
      if(!instance_of($invoice, 'Invoice')) {
        return new Error('$invoice is not valid instance of Invoice class', true);
      } // if
      
      $delete = parent::delete();
      if($delete && !is_error($delete)) {
        if($invoice->isBilled() || $invoice->isCanceled()) {
          $invoice->setStatus(INVOICE_STATUS_ISSUED);
          
          $save = $invoice->save();
          if($save && !is_error($save)) {
            db_commit();
            return true;
          } else {
            db_rollback();
            return $save;
          } // if
        } // if
        
        db_commit();
        return true;
      } else {
        db_rollback();
        return $delete;
      } // if
    } // delete
    
  }

?>