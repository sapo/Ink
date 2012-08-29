<?php

  /**
   * Invoice class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class Invoice extends BaseInvoice {
    
    /**
     * List of protected fields (can't be set using setAttributes() method)
     *
     * @var array
     */
  	var $protect = array('status', 'issued_on', 'issued_by_id', 'issued_by_name', 'issued_by_email', 'closed_on', 'closed_by_id', 'closed_by_name', 'closed_by_email', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email');

    /**
     * Return invoice name
     *
     * @param void
     * @return string
     */
    function getName($short=false) {
      if ($this->getNumber()) {
        if ($short) {
          return lang('#:num', array('num' => $this->getNumber()));  
        } else {
        	return lang('Invoice #:num', array('num' => $this->getNumber()));
        } // if
      } else {
        if ($short) {
          return lang('Draft #:invoice_id', array('invoice_id' => $this->getId()));
        } else {
          return lang('Invoice Draft #:invoice_id', array('invoice_id' => $this->getId()));  
        } // if
      } // if
    } // getName

    /**
     * Cached company value
     *
     * @var Company
     */
    var $company = false;

    /**
      * Return invoice company
      *
      * @param void
      * @return Company
      */
    function getCompany() {
      if($this->company === false) {
        $this->company = Companies::findById($this->getCompanyId());
      } // if
      return $this->company;
    } // getCompany

    /**
     * Cached project instance
     *
     * @var Project
     */
    var $project = false;

    /**
     * Return project instance
     *
     * @param void
     * @return Project
     */
    function getProject() {
      if($this->project === false) {
        $this->project = Projects::findById($this->getProjectId());
      } // if
      return $this->project;
    } // getProject

    /**
     * Cached currency instance
     *
     * @var Currency
     */
    var $currency = false;

    /**
     * Return invoice currency
     *
     * @param void
     * @return Currency
     */
    function getCurrency() {
      if($this->currency === false) {
        $this->currency = Currencies::findById($this->getCurrencyId());
      } // if
      return $this->currency;
    } // getCurrency

    /**
     * Return currency name
     *
     * @param void
     * @return string
     */
    function getCurrencyName() {
      $currency = $this->getCurrency();
      return instance_of($currency, 'Currency') ? $currency->getName() : lang('Unknown Currency');
    } // getCurrencyName

    /**
     * Return currency code
     *
     * @param void
     * @return string
     */
    function getCurrencyCode() {
      $currency = $this->getCurrency();
      return instance_of($currency, 'Currency') ? $currency->getCode() : lang('Unknown Currency');
    } // getCurrencyCode
    
    /**
     * Cached language value
     *
     * @var Language
     */
    var $language = false;

    /**
      * Return invoice company
      *
      * @param void
      * @return Company
      */
    function getLanguage() {
      if($this->language === false) {
        $this->language = Languages::findById($this->getLanguageId());
      } // if
      return $this->language;
    } // getLanguage
    
    /**
     * Cached issued by instance
     *
     * @var User
     */
    var $issued_by = false;
    
    /**
     * Return user object of person who issued this invoice
     *
     * @param void
     * @return User
     */
    function getIssuedBy() {
      if($this->issued_by === false) {
        $issued_by_id = $this->getIssuedById();
        if($issued_by_id) {
          $this->issued_by = Users::findById($issued_by_id);
        } // if
        
        if(!instance_of($this->issued_by, 'User')) {
          $this->issued_by = new AnonymousUser($this->getIssuedByName(), $this->getIssuedByEmail());
        } // if
      } // if
      return $this->issued_by;
    } // getIssuedBy
    
    /**
     * Cached issued to by instance
     *
     * @var User
     */
    var $issued_to = false;
    
    /**
     * Return user to which invoice is issued to
     *
     * @return User
     */
    function getIssuedTo() {
      if($this->issued_to === false) {
        $this->issued_to = Users::findById($this->getIssuedToId());
      } // if
      return $this->issued_to;
    } // getIssuedTo
    
    /**
     * Cached instance of person who closed this invoice
     *
     * @var User
     */
    var $closed_by = false;
    
    /**
     * Return user object of person who closed this invoice
     *
     * @param void
     * @return User
     */
    function getClosedBy() {
      if($this->closed_by === false) {
        $closed_by_id = $this->getClosedById();
        if($closed_by_id) {
          $this->closed_by = Users::findById($closed_by_id);
        } // if
        
        if(!instance_of($this->closed_by, 'User')) {
          $this->closed_by = new AnonymousUser($this->getClosedByName(), $this->getClosedByEmail());
        } // if
      } // if
      return $this->closed_by;
    } // getClosedBy

    /**
     * Cached invoice items
     *
     * @var array
     */
    var $items = false;

    /**
     * Return invoice items
     *
     * @param void
     * @return array
     */
    function getItems() {
      if($this->items === false) {
        $this->items = InvoiceItems::findByInvoice($this);
      } // if
      return $this->items;
    } // getItems

    /**
     * Cached invoice total
     *
     * @var float
     */
    var $total = false;

    /**
     * Cached tax value
     *
     * @var float
     */
    var $tax = false;

    /**
     * Cached taxed total
     *
     * @var float
     */
    var $taxed_total = false;
    
    /**
     * Return invoice total
     *
     * @param boolean $cache
     * @return float
     */
    function getTotal($cache = true) {
      $this->calculateTotal($cache);
      return $this->total;
    } // getTotal

    /**
     * Return calculated tax
     *
     * @param boolean $cache
     * @return float
     */
    function getTax($cache = true) {
      $this->calculateTotal($cache);
      return $this->tax;
    } // getTax

    /**
     * Returned taxed total
     *
     * @param boolean $cache
     * @return float
     */
    function getTaxedTotal($cache = true) {
      $this->calculateTotal($cache);
      return $this->taxed_total;
    } // getTaxedTotal
    
    /**
     * Generates invoice id by invoice pattern
     * 
     * @param null
     * @return string
     */
    function generateInvoiceId() {
      // retrieve pattern
      $pattern = Invoices::getInvoiceNumberGeneratorPattern();
      
      // retrieve counters
      list($total_counter, $year_counter, $month_counter) = Invoices::getDateInvoiceCounters();
      $total_counter++; $year_counter++; $month_counter++;
      
      // retrieve variables
      $variable_year = date('Y');
      $variable_month = date('n');
      $variable_month_short = date('M');
      $variable_month_long = date('F');
     
      $generated_invoice_id = str_ireplace(array(
        INVOICE_NUMBER_COUNTER_TOTAL,
        INVOICE_NUMBER_COUNTER_YEAR,
        INVOICE_NUMBER_COUNTER_MONTH,
        INVOICE_VARIABLE_CURRENT_YEAR,
        INVOICE_VARIABLE_CURRENT_MONTH,
        INVOICE_VARIABLE_CURRENT_MONTH_SHORT,
        INVOICE_VARIABLE_CURRENT_MONTH_LONG,
      ), array(
        $total_counter,
        $year_counter,
        $month_counter,
        $variable_year,
        $variable_month,
        $variable_month_short,
        $variable_month_long,
      ), $pattern);
      
      return $generated_invoice_id;
    } // function

    /**
     * Calculate total by walking through list of items
     *
     * @param void
     * @return null
     */
    function calculateTotal($cache = true) {
      if($cache == false || $this->total === false || $this->tax === false || $this->taxed_total === false) {
        $this->total = 0;
        $this->tax = 0;

        if(is_foreachable($this->getItems())) {
          foreach($this->getItems() as $item) {
            $this->total += $item->getSubTotal();
            $this->tax   += $item->getTax();
          } // foreach
        } // if
        
        // now we have total tax for invoice, we need to round it up to 2 decimals
        $this->tax = round($this->tax, 2);       
        $this->taxed_total = $this->total + $this->tax;
      } // if
    } // calculateTotal

    /**
     * List of available payments
     *
     * @var array
     */
    var $payments = false;

    /**
     * Return invoice payments
     *
     * @param void
     * @return array
     */
    function getPayments() {
      if($this->payments === false) {
        $this->payments = InvoicePayments::findByInvoice($this);
      } // if
      return $this->payments;
    } // getPayments

    /**
     * Cached amount paid
     *
     * @var float
     */
    var $paid_amount = false;

    /**
     * Return paid amount
     *
     * @param boolean $cache
     * @return float
     */
    function getPaidAmount($cache = true) {
      if($cache == false || $this->paid_amount === false) {
        $this->paid_amount = InvoicePayments::sumByInvoice($this);
      } // if
      return $this->paid_amount;
    } // getPaidAmount

    /**
     * Return % that was paid
     *
     * @param boolean $cache
     * @return integer
     */
    function getPercentPaid($cache = true) {
      return (float) number_format(($this->getPaidAmount($cache) * 100) / $this->getTaxedTotal($cache), 2, '.', '');
    } // getPercentPaid

    /**
     * Return max amount that can be paid with the next payment
     *
     * @param boolean $cache
     * @return float
     */
    function getMaxPayment($cache = true) {
      return $this->getTaxedTotal($cache) - $this->getPaidAmount($cache);
    } // getMaxPayment
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Return options for this object
     *
     * @param User $user
     * @return array
     */
    function getOptions($user) {
      $options = new NamedList();
      
      if($this->canView($user) && !$this->isCanceled()) {
        $options->add('view_pdf', array(
          'url' => $this->getPdfUrl(),
          'text' => $this->getStatus() == INVOICE_STATUS_DRAFT ? lang('Preview PDF') : lang('View PDF'),
        ));
      } // if
      
      if($this->canIssue($user)) {
        $options->add('issue', array(
          'url' => $this->getIssueUrl(),
          'text' => lang('Issue'),
        ));
      } // if
      
      if($this->canCancel($user)) {
        $options->add('cancel', array(
          'url' => $this->getCancelUrl(), 
          'text' => lang('Cancel'),
          'confirm' => lang('Are you sure that you want to cancel this invoice? All existing payments associated with this invoice will be deleted!'),
          'method' => 'post',
        ));
      }
      
      if($user->getSystemPermission('can_manage_invoices')) {
        $options->add('duplicate', array(
          'url' => $this->getDuplicateUrl(),
          'text' => lang('Duplicate'),
        ));
      } // if
      
      if($this->countTimeRecords()) {
        $options->add('time', array(
          'url' => $this->getDuplicateUrl(),
          'text' => lang('Time Records (:count)', array('count' => $this->countTimeRecords())),
        ));
      } // if
      
      if($this->canEdit($user)) {
        $options->add('edit', array(
          'url' => $this->getEditUrl(),
          'text' => lang('Edit'),
        ));
      } // if
      
      if($this->canDelete($user)) {
        $options->add('delete', array(
          'url' => $this->getDeleteUrl(),
          'text' => lang('Delete'),
          'confirm' => lang('Are you sure that you want to delete this invoice?'),
          'method' => 'post',
        ));
      } // if
      
      return $options->count() ? $options : null;
    } // getOptions
    
    // ---------------------------------------------------
    //  Time records
    // ---------------------------------------------------
    
    /**
     * Cached array of related time records
     *
     * @var array
     */
    var $time_record_ids = false;
    
    /**
     * Return array of related time record ID-s
     *
     * @param void
     * @return array
     */
    function getTimeRecordIds() {
      if($this->time_record_ids === false) {
        $rows = db_execute_all('SELECT time_record_id FROM ' . TABLE_PREFIX . 'invoice_time_records WHERE invoice_id = ?', $this->getId());
        if(is_foreachable($rows)) {
          $this->time_record_ids = array();
          foreach($rows as $row) {
            $this->time_record_ids[] = (integer) $row['time_record_id'];
          } // foreach
        } else {
          $this->time_record_ids = null;
        } // if
      } // if
      return $this->time_record_ids;
    } // getTimeRecordIds
    
    /**
     * Cached value of related time records count
     *
     * @var integer
     */
    var $time_records_count = false;
    
    /**
     * Return number of related time records
     *
     * @param void
     * @return integer
     */
    function countTimeRecords() {
      if($this->time_records_count === false) {
        $this->time_records_count = array_var(db_execute_one("SELECT COUNT(time_record_id) AS 'row_count' FROM " . TABLE_PREFIX . 'invoice_time_records WHERE invoice_id = ?', $this->getId()), 'row_count');
      } // if
      return $this->time_records_count;
    } // countTimeRecords
    
    /**
     * Release time records
     *
     * @param void
     * @return boolean
     */
    function releaseTimeRecords() {
      return db_execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_time_records WHERE invoice_id = ?', $this->getId());
    } // releaseTimeRecords
    
    /**
     * Return related time records
     *
     * @param void
     * @param integer $visibility
     * @return array
     */
    function getTimeRecords($visibility = VISIBILITY_NORMAL) {
      $ids = $this->getTimeRecordIds();
      return is_foreachable($ids) ? TimeRecords::findByIds($ids, STATE_VISIBLE, $visibility) : null;
    } // getTimeRecords
    
    /**
     * Set status to related time records
     *
     * @param integer $new_status
     * @return boolean
     */
    function setTimeRecordsStatus($new_status) {
      $ids = $this->getTimeRecordIds();
      if(is_foreachable($ids)) {
        $update = db_execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET integer_field_2 = ? WHERE id IN (?)', $new_status, $ids);
        if($update) {
          cache_remove_by_pattern(TABLE_PREFIX . 'project_objects_id_*');
          return true;
        } else {
          return $update;
        } // if
      } // if
      return true;
    } // setTimeRecordsStatus
       
    // ---------------------------------------------------
    //  Status
    // ---------------------------------------------------

    /**
     * Return verbose invoice status
     *
     * @param void
     * @return string
     */
    function getVerboseStatus() {
      switch($this->getStatus()) {
        case INVOICE_STATUS_DRAFT:
          return lang('Draft');
        case INVOICE_STATUS_ISSUED:
          return lang('Issued');
        case INVOICE_STATUS_BILLED:
          return lang('Billed');
        case INVOICE_STATUS_CANCELED:
          return lang('Canceled');
      } // switch
    } // getVerboseStatus
    
    /**
     * Change invoice status
     *
     * @param integer $status
     * @param User $by
     * @param DateValue $on
     * @return null
     */
    function setStatus($status, $by = null, $on = null) {
      $on = instance_of($on, 'DateValue') ? $on : new DateValue();
      $by = instance_of($by, 'User') ? $by : get_logged_user();
      
      switch($status) {
        
        // Mark invoice as draft
        case INVOICE_STATUS_DRAFT:
          parent::setStatus($status);
          
          $this->setIssuedOn(null);
          $this->setIssuedById(null);
          $this->setIssuedByName(null);
          $this->setIssuedByEmail(null);
          
          $this->setClosedOn(null);
          $this->setClosedById(null);
          $this->setClosedByName(null);
          $this->setClosedByEmail(null);
          break;
          
        // Mark invoice as issued
        case INVOICE_STATUS_ISSUED:
          parent::setStatus($status);
          
          if($on) {
            $this->setIssuedOn($on);
          } // if
          
          if($by) {
            $this->setIssuedById($by->getId());
            $this->setIssuedByName($by->getName());
            $this->setIssuedByEmail($by->getEmail());
          } // if
          
          $this->setClosedOn(null);
          $this->setClosedById(null);
          $this->setClosedByName(null);
          $this->setClosedByEmail(null);
          
          $this->setTimeRecordsStatus(BILLABLE_STATUS_PENDING_PAYMENT);
          break;
          
        // Mark invoice as billed
        case INVOICE_STATUS_BILLED:
          parent::setStatus(INVOICE_STATUS_BILLED);
          
          $this->setClosedOn($on);
          $this->setClosedById($by->getId());
          $this->setClosedByName($by->getName());
          $this->setClosedByEmail($by->getEmail());
          
          $this->setTimeRecordsStatus(BILLABLE_STATUS_BILLED);
          break;
          
        // Mark invoice as canceled
        case INVOICE_STATUS_CANCELED:
          parent::setStatus(INVOICE_STATUS_CANCELED);
          
          $this->setClosedOn($on);
          $this->setClosedById($by->getId());
          $this->setClosedByName($by->getName());
          $this->setClosedByEmail($by->getEmail());
          
          InvoicePayments::deleteByInvoice($this);
          
          $this->setTimeRecordsStatus(BILLABLE_STATUS_BILLABLE);
          $this->releaseTimeRecords();
          break;
          
        default:
          return new InvalidParamError('status', $status, '$status is not valid invoice status', true);
      } // switch
    } // setStatus
    
    /**
     * Returns true if this invoice is issued
     *
     * @param void
     * @return boolean
     */
    function isIssued() {
      return instance_of($this->getIssuedOn(), 'DateValue') && $this->getStatus() == INVOICE_STATUS_ISSUED;
    } // isIssued
    
    /**
     * Returns true if this invoice is marked as billed
     *
     * @param void
     * @return boolean
     */
    function isBilled() {
      return instance_of($this->getClosedOn(), 'DateValue') && $this->getStatus() == INVOICE_STATUS_BILLED;
    } // isBilled
    
    /**
     * Check if this invoice is overdue
     * 
     * @param void
     * @return null
     */
    function isOverdue() {
      $today = new DateValue(time() + get_user_gmt_offset());
      $due_on = $this->getDueOn();
      return (boolean) ($this->isIssued() && !$this->isBilled() && !$this->isCanceled() && (instance_of($due_on, 'DateValue') && ($due_on->getTimestamp() < $today->getTimestamp())));
    } // isOverdue
    
    /**
     * Cancel this invoice
     *
     * @param User $by
     * @return boolean
     */
    function cancel($by) {
      db_begin_work();
      
      $save = $this->save();
      if($save && !is_error($save)) {
        InvoicePayments::deleteByInvoice($this);
        db_commit();
      } else {
        db_rollback();
        return $save;
      } // if
    } // cancel
    
    /**
     * Returns true if this invoice is canceled
     *
     * @param void
     * @return boolean
     */
    function isCanceled() {
      return instance_of($this->getClosedOn(), 'DateValue') && $this->getStatus() == INVOICE_STATUS_CANCELED;
    } // isCanceled
    
    // ---------------------------------------------------
    //  PDF
    // ---------------------------------------------------
    
    /**
     * Return PDF filename
     *
     * @param void
     * @return string
     */
    function getPdfFileName() {
      return $this->getName() . '.pdf';
    } // getPdfFileName
    
    /**
     * Return PDF path
     *
     * @param void
     * @return string
     */
    function getPdfFilePath() {
      return INVOICES_WORK_PATH . '/invoice-' . $this->getId() . '.pdf';
    } // getPdfFilePath
    
    /**
     * Create a new PDF file
     *
     * @param void
     * @return boolean
     */
    function generatePdf() {
      require_once ANGIE_PATH . '/fpdf/init.php';
    } // generatePdf

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can access $company invoices
     *
     * @param User $user
     * @param Company $company
     * @return boolean
     */
    function canAccessCompanyInvoices($user, $company) {
      return $user->isAdministrator() || $user->getSystemPermission('can_manage_invoices') || $user->isCompanyManager($company);
    } // canAccessCompanyInvoices
    
    /**
     * Returns true if $user can view specific PDF file
     *
     * @param User $user
     * @return boolean
     */
    function canView($user) {
      return $user->getSystemPermission('can_manage_invoices');
    } // canView

    /**
     * Returns true if $user can create a new invoice
     *
     * @param User $user
     * @return boolean
     */
    function canAdd($user) {
      return $user->getSystemPermission('can_manage_invoices');
    } // canAdd

    /**
     * Returns true if $user can edit this invoice
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
      return (in_array($this->getStatus(), array(INVOICE_STATUS_DRAFT, INVOICE_STATUS_ISSUED))) && $user->getSystemPermission('can_manage_invoices');
    } // canEdit

    /**
     * Returns true if $user can delete this invoice
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      return ($this->getStatus() == INVOICE_STATUS_DRAFT || $this->getStatus() == INVOICE_STATUS_CANCELED) && $user->getSystemPermission('can_manage_invoices');
    } // canDelete
    
    /**
     * Returns true if this invoice can be issue by $user
     *
     * @param User $user
     * @return boolean
     */
    function canIssue($user) {
      return ($this->getStatus() == INVOICE_STATUS_DRAFT) && $user->getSystemPermission('can_manage_invoices');
    } // canIssue
    
    /**
     * Returns true if $user can cancel this invoice
     *
     * @param User $user
     * @return boolean
     */
    function canCancel($user) {
      return ($this->getStatus() == INVOICE_STATUS_ISSUED || $this->getStatus() == INVOICE_STATUS_BILLED) && $user->getSystemPermission('can_manage_invoices');
    } // canCancel

    /**
     * Returns true if $user can add payment to this invoice
     *
     * @param User $user
     * @return boolean
     */
    function canAddPayment($user) {
       return $this->getStatus() == INVOICE_STATUS_ISSUED && $user->getSystemPermission('can_manage_invoices');
    } // canAddPayment

    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Return view invoice URL
     *
     * @param void
     * @return string
     */
    function getViewUrl() {
      return assemble_url('invoice', array('invoice_id' => $this->getId()));
    } // getViewUrl
    
    /**
     * Return PDF doc URL
     *
     * @param void
     * @return string
     */
    function getPdfUrl() {
      return assemble_url('invoice_pdf', array('invoice_id' => $this->getId()));
    } // getPdfUrl
    
    /**
     * Return company view URL
     *
     * @param void
     * @return string
     */
    function getCompanyViewUrl() {
      return assemble_url('people_company_invoice', array('invoice_id' => $this->getId(), 'company_id' => $this->getCompanyId()));
    } // getCompanyViewUrl
    
    /**
     * Return public PDF URL accessible from company invoices page
     *
     * @param void
     * @return string
     */
    function getCompanyPdfUrl() {
      return assemble_url('people_company_invoice_pdf', array('invoice_id' => $this->getId(), 'company_id' => $this->getCompanyId()));
    } // getCompanyPdfUrl

    /**
     * Return send invoice URL
     *
     * @param void
     * @return string
     */
    function getIssueUrl() {
      return assemble_url('invoice_issue', array('invoice_id' => $this->getId()));
    } // getIssueUrl

    /**
     * Return delete invoice URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('invoice_delete', array('invoice_id' => $this->getId()));
    } // getdeleteUrl

    /**
     * Return edit currency URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('invoice_edit', array('invoice_id' => $this->getId()));
    } // getEditUrl

    /**
     * Return cancel invoice URL
     *
     * @param void
     * @return string
     */
    function getCancelUrl() {
      return assemble_url('invoice_cancel', array('invoice_id' => $this->getId()));
    } // getViewUrl

    /**
     * Return duplicate invoice URL
     *
     * @param void
     * @return string
     */
    function getDuplicateUrl() {
      return assemble_url('invoices_add', array('duplicate_invoice_id' => $this->getId()));
    } // getDuplicateUrl

    /**
     * Return add payment URL
     *
     * @param void
     * @return string
     */
    function getAddPaymentUrl() {
      return assemble_url('invoice_payments_add', array('invoice_id' => $this->getId()));
    } // getAddPaymentUrl
    
    /**
     * Return invoice time URL
     *
     * @param void
     * @return string
     */
    function getTimeUrl() {
      return assemble_url('invoice_time', array('invoice_id' => $this->getId()));
    } // getTimeUrl
    
    /**
     * Return release time URL
     *
     * @param void
     * @return string
     */
    function getReleaseTimeUrl() {
      return assemble_url('invoice_time_release', array('invoice_id' => $this->getId()));
    } // getReleaseTimeUrl

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
      if(!$this->validatePresenceOf('company_id')) {
        $errors->addError(lang('Client is required'), 'company_address');
      } // if
      
      if(!$this->validatePresenceOf('company_address')) {
        $errors->addError(lang('Client address is required'), 'company_address');
      } // if
      
      if($this->validatePresenceOf('number')) {
        if(!$this->validateUniquenessOf('number')) {
          $errors->addError(lang('Invoice No. needs to be unqiue'), 'number');
        } // if
      } // if
    } // validate
    
    /**
     * Delete existing invoice from database
     *
     * @param void
     * @return boolean
     */
    function delete() {
      db_begin_work();
      
      $delete = parent::delete();
      if($delete && !is_error($delete)) {
        InvoiceItems::deleteByInvoice($this);
        InvoicePayments::deleteByInvoice($this);
        
        db_commit();
        return true;
      } else {
        db_rollback();
        return $delete;
      } // if
    } // delete

  }

?>