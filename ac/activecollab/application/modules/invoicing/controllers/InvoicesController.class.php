<?php

  /**
   * Main invoices controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class InvoicesController extends ApplicationController {

    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'invoices';

    /**
     * Selected invoice
     *
     * @var Invoice
     */
    var $active_invoice;

    /**
     * Construct invoices controller
     *
     * @param Request $request
     * @return InvoicesController
     */
    function __construct($request) {
      parent::__construct($request);
      if(!$this->logged_user->getSystemPermission('can_manage_invoices')) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      // Warning message about invoices folder existance / writability
      if(is_dir(INVOICES_WORK_PATH)) {
        if(!folder_is_writable(INVOICES_WORK_PATH)) {
          $this->wireframe->addPageMessage(lang('/work/invoices exists, but it is not writable. PDF files will not be generated'), 'error');
        } // if
      } else {
        $this->wireframe->addPageMessage(lang('/work/invoices folder does not exist. PDF files will not be generated!'), 'error');
      } // if

      $invoice_id = $this->request->getId('invoice_id');
      if($invoice_id) {
        $this->active_invoice = Invoices::findById($invoice_id);
      } // if

      $this->wireframe->addBreadCrumb(lang('Invoices'), assemble_url('invoices'));
      if(instance_of($this->active_invoice, 'Invoice')) {
        $this->wireframe->addBreadCrumb($this->active_invoice->getName(), $this->active_invoice->getViewUrl());
      } else {
        $this->active_invoice = new Invoice();
      } // if

      if(Invoice::canAdd($this->logged_user)) {
        $add_invoice_url = assemble_url('invoices_add');
        $this->wireframe->addPageAction(lang('New Invoice'), $add_invoice_url);
      } else {
        $add_invoice_url = false;
      } // if
      
      $this->wireframe->current_menu_item = 'invoicing';

      $this->smarty->assign(array(
        'active_invoice' => $this->active_invoice,
        'add_invoice_url' => $add_invoice_url,
        'drafts_count' => Invoices::countDrafts()
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
      
      $status = $this->request->get('status') ? $this->request->get('status') : 'issued';
      if ($status == 'issued') {
        $invoice_status = INVOICE_STATUS_ISSUED;
      } else if ($status == 'billed') {
        $invoice_status = INVOICE_STATUS_BILLED;
      } else if ($status == 'drafts') {
        $invoice_status = INVOICE_STATUS_DRAFT;
      } else if ($status == 'canceled') {
        $invoice_status = INVOICE_STATUS_CANCELED;
      } // if

      list($invoices, $pagination) = Invoices::paginateAll(array($invoice_status), $page, $per_page, 'due_on ASC, created_on DESC');	

      $this->smarty->assign(array(
        'invoices' => $invoices,
        'pagination' => $pagination,
        'status' => $status
      ));
    } // index
    
    /**
     * Show invoicing archive
     *
     * @param void
     * @return null
     */
    function archive() {
      $this->smarty->assign('invoiced_companies', Invoices::findInvoicedCompaniesInfo(array(INVOICE_STATUS_BILLED, INVOICE_STATUS_CANCELED)));
    } // archive

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
      
      if(!$this->active_invoice->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
    } // view
    
    /**
     * Show PDF file
     *
     * @param void
     * @return null
     */
    function pdf() {
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_invoice->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      require_once(INVOICING_MODULE_PATH.'/models/InvoicePdf.class.php');
      InvoicePDF::download($this->active_invoice, lang(':invoice_id.pdf', array('invoice_id' => $this->active_invoice->getName())));
      die();
    } // pdf

    /**
     * Create a new invoice
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      if(!Invoice::canAdd($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $default_currency = Currencies::findDefault();
      if(!instance_of($default_currency, 'Currency')) {
        $this->httpError(HTTP_ERR_NOT_FOUND, 'Default currency not set');
      } // if
      
      $time_report = null;
      $project = null;
      $timerecord_ids = null;

      $invoice_data = $this->request->post('invoice');
      if(!is_array($invoice_data)) {
        $duplicate_invoice_id = $this->request->getId('duplicate_invoice_id');
        $time_report_id = $this->request->getId('time_report_id');
        $ticket_id = $this->request->getId('ticket_id');
        
        // ---------------------------------------------------
        //  Duplicate existing invoice
        // ---------------------------------------------------
        if($duplicate_invoice_id) {
          $duplicate_invoice = Invoices::findById($duplicate_invoice_id);
          if(instance_of($duplicate_invoice, 'Invoice')) {
            $invoice_data = array(
              'company_id'      => $duplicate_invoice->getCompanyId(),
              'company_address' => $duplicate_invoice->getCompanyAddress(),
              'comment'         => $duplicate_invoice->getComment(),
              'status'          => INVOICE_STATUS_DRAFT,
              'project_id'      => $duplicate_invoice->getProjectId(),
              'note'            => $duplicate_invoice->getNote(),
              'currency_id'     => $duplicate_invoice->getCurrencyId(),
            );
            
            if(is_foreachable($duplicate_invoice->getItems())) {
              $invoice_data['items'] = array();
              foreach($duplicate_invoice->getItems() as $item) {
                $invoice_data['items'][] = array(
                  'description' => $item->getDescription(),
                  'unit_cost'   => $item->getUnitCost(),
                  'quantity'    => $item->getQuantity(),
                  'tax_rate_id' => $item->getTaxRateId(),
                  'total'       => $item->getTotal(),
                  'subtotal'    => $item->getSubtotal(),
                );
              } // foreach
            } // if
          } // if
          
        // ---------------------------------------------------
        //  Create invoice from time report
        // ---------------------------------------------------
        } else if($time_report_id) {
          $time_report = TimeReports::findById($time_report_id);
          if(instance_of($time_report, 'TimeReport')) {
            $project_id = $this->request->getId('project_id');
            $client_company_id = null;
            if($project_id) {
              $project = Projects::findById($project_id);
            } // if
            
            $time_report->setBillableFilter(BILLABLE_FILTER_BILLABLE);
            $conditions = $time_report->prepareConditions($this->logged_user, $project);
            if($conditions === false) {
              $this->httpError(HTTP_ERR_OPERATION_FAILED, 'Failed to prepare time report conditions');
            } // if
            
            $timerecord_ids = array();
            $total_time = 0;
            
            $rows = db_execute_all('SELECT DISTINCT id, float_field_1 FROM ' . TABLE_PREFIX . "project_objects WHERE $conditions");
            if(is_foreachable($rows)) {
              foreach($rows as $row) {
                $timerecord_ids[] = (integer) $row['id'];
                $total_time += (float) $row['float_field_1'];
              } // foreach
            } // if
            
            if(count($timerecord_ids) && $total_time) {
              if(instance_of($project, 'Project')) {
                $description = lang('Total of :total hours in :project project', array('total' => $total_time, 'project' => $project->getName()));
              } else {
                $description = lang('Total of :total hours', array('total' => $total_time));
              } // if
              
              $invoice_data = array(
                'due_on' => new DateValue(),
                'currency_id' => $default_currency->getId(),
                'project_id' => instance_of($project, 'Project') ? $project->getId() : null,
                'company_id' => instance_of($project, 'Project') ? $project->getCompanyId() : null,
                'items' => array(
                  array(
                    'description' => $description,
                    'unit_cost'   => $default_currency->getDefaultRate(),
                    'quantity'    => $total_time,
                    'subtotal' => $default_currency->getDefaultRate() * $total_time,
                    'total' => $default_currency->getDefaultRate() * $total_time,
                    'tax_rate_id' => null,
                    'time_record_ids' => $timerecord_ids
                  )
                ),
              );
            } // if
          } // if
        // ---------------------------------------------------
        //  Create invoice from ticket
        // ---------------------------------------------------
        } else if ($ticket_id) {
          $ticket = Tickets::findById($ticket_id);
          if (instance_of($ticket, 'Ticket')) {
            
            $items = array();
            if ($ticket->getHasTime()) {
              $timerecords = TimeRecords::findByParent($ticket, array(BILLABLE_STATUS_BILLABLE), STATE_VISIBLE, $this->logged_user->getVisibility());
              $timerecord_ids = array();
              $ticket_total_time = 0;
              if (is_foreachable($timerecords)) {
                foreach ($timerecords as $timerecord) {
                	if ($timerecord->getValue() > 0) {
                    $ticket_total_time+= $timerecord->getValue();
                    $timerecord_ids[] = $timerecord->getId();
                	} // if
                } // foreach
                
                $items[] = array(
                  'description' => lang('Ticket: :ticket_name', array('ticket_name' => $ticket->getName())),
                  'unit_cost' => $default_currency->getDefaultRate(),
                  'quantity' => $ticket_total_time,
                  'subtotal' => $default_currency->getDefaultRate() * $ticket_total_time,
                  'total' => $default_currency->getDefaultRate() * $ticket_total_time,
                  'tax_rate_id' => null,
                  'time_record_ids' => $timerecord_ids,
                );
              } // if
            } // if
            
            $tasks = $ticket->getTasks();
            if (is_foreachable($tasks)) {
              foreach ($tasks as $task) {
                if ($task->getHasTime()) {
                  $timerecords = TimeRecords::findByParent($task, array(BILLABLE_STATUS_BILLABLE), STATE_VISIBLE, $this->logged_user->getVisibility());
                  $task_total_time = 0;
                  $timerecord_ids = array();
                  if (is_foreachable($timerecords)) {
                    foreach ($timerecords as $timerecord) {
  	                  if ($timerecord->getValue() > 0) {
                        $task_total_time+= $timerecord->getValue();
                        $timerecord_ids[] = $timerecord->getId();
  	                  } // if
                    } // foreach
                    
                    if ($task_total_time > 0) {
                      $items[] = array(
                        'description' => lang('Task: :task_name', array('task_name' => $task->getName())),
                        'unit_cost' => $default_currency->getDefaultRate(),
                        'quantity' => $task_total_time,
                        'subtotal' => $default_currency->getDefaultRate() * $task_total_time,
                        'total' => $default_currency->getDefaultRate() * $task_total_time,
                        'tax_rate_id' => null,
                        'time_record_ids' => $timerecord_ids,
                      );
                    } // if
                  } // if
                } // if
              } // foreach
            } // if
            
            $project = $ticket->getProject();
            $invoice_data = array(
              'due_on' => new DateValue(),
              'currency_id' => $default_currency->getId(),
              'project_id' => $ticket->getProjectId(),
              'company_id' => instance_of($project, 'Project') ? $project->getCompanyId() : null,
              'time_record_ids' => $timerecord_ids,
              'items' => $items
            );
          } // if
        } // if
        
        // ---------------------------------------------------
        //  Start blank
        // ---------------------------------------------------
        
        if(!is_array($invoice_data)) {
          $invoice_data = array(
            'due_on' => new DateValue(),
            'currency_id' => $default_currency->getId(),
            'project_id' => instance_of($project, 'Project') ? $project->getId() : null,
            'time_record_ids' => null,
          );
        } // if
      } // if
      
      $invoice_notes = InvoiceNoteTemplates::findAll();
      $invoice_item_templates = InvoiceItemTemplates::findAll();
      
      $this->smarty->assign(array(
        'invoice_data' => $invoice_data,
        'tax_rates' => TaxRates::findAll(),
        'invoice_notes' => $invoice_notes,
        'invoice_item_templates' => $invoice_item_templates,
        'original_note' => $this->active_invoice->getNote(),
      ));
      
      $cleaned_notes = array();
      if (is_foreachable($invoice_notes)) {
        foreach ($invoice_notes as $invoice_note) {
        	$cleaned_notes[$invoice_note->getId()] = $invoice_note->getContent();
        } // foreach
      } // if
      js_assign('invoice_notes', $cleaned_notes);
      js_assign('original_note', $this->active_invoice->getNote());
      
      $cleaned_item_templates = array();
      if (is_foreachable($invoice_item_templates)) {
        foreach ($invoice_item_templates as $invoice_item_template) {
        	$cleaned_item_templates[$invoice_item_template->getId()] = array(
        	 'description' => $invoice_item_template->getDescription(),
        	 'unit_cost' => $invoice_item_template->getUnitCost(),
        	 'quantity' => $invoice_item_template->getQuantity(),
        	 'tax_rate_id' => $invoice_item_template->getTaxRateId(),
        	);
        } // foreach
      } // if
      js_assign('invoice_item_templates', $cleaned_item_templates);
      
      js_assign('company_details_url', assemble_url('invoice_company_details'));
      js_assign('move_icon_url', get_image_url('move.gif'));

      if($this->request->isSubmitted()) {
        db_begin_work();
        $this->active_invoice->setAttributes($invoice_data);
        $this->active_invoice->setCreatedBy($this->logged_user);

        $save = $this->active_invoice->save();
        if($save && !is_error($save)) {
          $counter = 0;
          if(is_foreachable($invoice_data['items'])) {
            foreach($invoice_data['items'] as $invoice_item_data) {
              $invoice_item = new InvoiceItem();
              $invoice_item->setAttributes($invoice_item_data);
              $invoice_item->setInvoiceId($this->active_invoice->getId());
              $invoice_item->setPosition($counter + 1);

              $item_save = $invoice_item->save();
              if($item_save && !is_error($item_save)) {
                $invoice_item->setTimeRecordIds(array_var($invoice_item_data, 'time_record_ids'));
                $counter++;
              } else {
                // error in invoice_item_data
              } // if
            } // foreach
          } // if

          if($counter > 0) {
            db_commit();
            
            flash_success('Invoice ":number" has been created', array('number' => $this->active_invoice->getName()));
            $this->redirectToUrl($this->active_invoice->getViewUrl());
          } else {
            db_rollback();
            $this->smarty->assign('errors', new ValidationErrors(array(
              'items' => lang('Invoice items data is not valid. All descriptions are required and there need to be at least one unit with cost set per item!'
            ))));
          } // if
        } else {
          db_rollback();
          $this->smarty->assign('errors', $save);
        } // if
      } // if
    } // add

    /**
     * Update existing invoice
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if

      if(!$this->active_invoice->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if

      $invoice_data = $this->request->post('invoice');
      if(!is_array($invoice_data)) {
        $invoice_data = array(
          'number'          => $this->active_invoice->getNumber(),
          'due_on'          => $this->active_invoice->getDueOn(),
          'issued_on'       => $this->active_invoice->getIssuedOn(),
          'currency_id'     => $this->active_invoice->getCurrencyId(),
          'comment'         => $this->active_invoice->getComment(),
          'company_id'      => $this->active_invoice->getCompanyId(),
          'company_address' => $this->active_invoice->getCompanyAddress(),
          'project_id'      => $this->active_invoice->getProjectId(),
          'note'            => $this->active_invoice->getNote(),
          'language_id'     => $this->active_invoice->getLanguageId(),
        );
        if(is_foreachable($this->active_invoice->getItems())) {
          $invoice_data['items'] = array();
          foreach($this->active_invoice->getItems() as $item) {
            $invoice_data['items'][] = array(
              'description' => $item->getDescription(),
              'unit_cost'   => $item->getUnitCost(),
              'quantity'    => $item->getQuantity(),
              'tax_rate_id' => $item->getTaxRateId(),
              'total'       => $item->getTotal(),
              'subtotal'    => $item->getSubtotal(),
              'time_record_ids' => $item->getTimeRecordIds(),
            );
          } // foreach
        } // if
      } // if

      $invoice_notes = InvoiceNoteTemplates::findAll();
      $invoice_item_templates = InvoiceItemTemplates::findAll();
      
      $this->smarty->assign(array(
        'invoice_data' => $invoice_data,
        'invoice_item_templates' => $invoice_item_templates,
        'tax_rates' => TaxRates::findAll(),
        'invoice_notes' => $invoice_notes,
        'original_note' => $this->active_invoice->getNote(),
      ));
      
      $cleaned_notes = array();
      if (is_foreachable($invoice_notes)) {
        foreach ($invoice_notes as $invoice_note) {
        	$cleaned_notes[$invoice_note->getId()] = $invoice_note->getContent();
        } // foreach
      } // if
      js_assign('invoice_notes', $cleaned_notes);
      js_assign('original_note', $this->active_invoice->getNote());
      
      $cleaned_item_templates = array();
      if (is_foreachable($invoice_item_templates)) {
        foreach ($invoice_item_templates as $invoice_item_template) {
        	$cleaned_item_templates[$invoice_item_template->getId()] = array(
        	 'description' => $invoice_item_template->getDescription(),
        	 'unit_cost' => $invoice_item_template->getUnitCost(),
        	 'quantity' => $invoice_item_template->getQuantity(),
        	 'tax_rate_id' => $invoice_item_template->getTaxRateId(),
        	);
        } // foreach
      } // if
      js_assign('invoice_item_templates', $cleaned_item_templates);
      
      js_assign('company_details_url', assemble_url('invoice_company_details'));
      js_assign('move_icon_url', get_image_url('move.gif'));

      if($this->request->isSubmitted()) {
        $this->active_invoice->setAttributes($invoice_data);
        $save = $this->active_invoice->save();

        if($save && !is_error($save)) {
          InvoiceItems::deleteByInvoice($this->active_invoice);
          
          $counter = 1;
          if(is_foreachable($invoice_data['items'])) {
            foreach($invoice_data['items'] as $invoice_item_data) {
              $invoice_item = new InvoiceItem();

              $invoice_item->setAttributes($invoice_item_data);
              $invoice_item->setInvoiceId($this->active_invoice->getId());
              $invoice_item->setPosition($counter);

              $item_save = $invoice_item->save();
              if($item_save && !is_error($item_save)) {
                $invoice_item->setTimeRecordIds(array_var($invoice_item_data, 'time_record_ids', null));
                $counter++;
              } else {
                $this->smarty->assign('errors', new ValidationErrors(array(
                  'items' => lang('Invoice items data is not valid. All descriptions are required and there need to be at least one unit with cost set per item!'
                ))));
              } // if
            } // foreach

            flash_success('":number" has been updated', array('number' => $this->active_invoice->getName()));
            if ($this->active_invoice->isIssued()) {
              $this->redirectTo('invoice_notify', array('invoice_id' => $this->active_invoice->getId()));
            } else {
              $this->redirectToUrl($this->active_invoice->getViewUrl());  
            } // if
          } else {
            $this->smarty->assign('errors', $save);
          } // if
        } // if
      } // if
    } // edit
    
    /**
     * Issue invoice
     *
     * @param void
     * @return null
     */
    function issue() {
      $this->wireframe->print_button = false;
      
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_invoice->canIssue($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $company = $this->active_invoice->getCompany();
      if(!instance_of($company, 'Company')) {
        $this->httpError(HTTP_ERR_CONFLICT);
      } // if
      
      $issue_data = $this->request->post('issue');
      if(!is_array($issue_data)) {
        $issue_data = array(
          'issued_on' => new DateValue(),
          'due_on' => new DateValue('+7 days'),
        ); 
      } // if
      
      $this->smarty->assign(array(
        'users' => $company->getUsers(),
        'company' => $company,
        'issue_data' => $issue_data,
      ));
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $issued_on = isset($issue_data['issued_on']) ? new DateValue($issue_data['issued_on']) : new DateValue();
        $due_on = isset($issue_data['due_on']) ? new DateValue($issue_data['due_on']) : null;
        $this->active_invoice->setStatus(INVOICE_STATUS_ISSUED, $this->logged_user, $issued_on);
        if ($due_on) {
          $this->active_invoice->setDueOn($due_on);
        } // if
        
        $issue_to = null;
        $user_id = array_var($issue_data, 'user_id');
        if($user_id) {
          $user = Users::findById($user_id);
          if(instance_of($user, 'User')) {
            $this->active_invoice->setIssuedToId($user->getId());
            $issue_to = array($user);
            if($user->getId() != $this->logged_user->getId()) {
              $issue_to[] = $this->logged_user;
            } // if
          } // if
        } // if
        
        $autogenerated = false;
        if (!$this->active_invoice->getNumber()) {
          $autogenerated = true;
          $this->active_invoice->setNumber($this->active_invoice->generateInvoiceId());
        } // if
        
        $save = $this->active_invoice->save();
        if($save && !is_error($save)) {
          if ($autogenerated) {
            Invoices::incrementDateInvoiceCounters();  
          } // if
          
          if(isset($issue_data['send_emails']) && $issue_data['send_emails']) {
            if($issue_to) {
              $filename_name = 'invoice_'.$this->active_invoice->getId().'.pdf';
              $filename = WORK_PATH.'/' . $filename_name;
              
              require_once(INVOICING_MODULE_PATH.'/models/InvoicePdf.class.php');
              InvoicePDF::save($this->active_invoice, $filename);
              
              ApplicationMailer::send($issue_to, 'invoicing/issue', array(
                'issued_by_name' => $this->logged_user->getDisplayName(),
                'issued_by_url'  => $this->logged_user->getViewUrl(),
                'invoice_number' => $this->active_invoice->getNumber(), 
                'invoice_url'    => $this->active_invoice->getCompanyViewUrl(),
                'pdf_url'        => $this->active_invoice->getCompanyPdfUrl(),
              ), null, array(
                array('path' => $filename)
              ));
              
              @unlink($filename);
            } // if
          } // if
          
          db_commit();
          
          flash_success('Invoice has been issued');
          $this->redirectToUrl($this->active_invoice->getViewUrl());
        } else {
          db_rollback();
          $this->smarty->assign('errors', $issue);
        } // if
      } // if
    } // issue
    
    /**
     * Page is displayed when issued invoice is edited
     * 
     * @param void
     * @return null
     */
    function notify() {
      $this->wireframe->print_button = false;
      
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_invoice->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $company = $this->active_invoice->getCompany();
      if(!instance_of($company, 'Company')) {
        $this->httpError(HTTP_ERR_CONFLICT);
      } // if
      
      $notify_url = assemble_url('invoice_notify', array('invoice_id' => $this->active_invoice->getId()));
      $users = $company->getUsers();
      $this->smarty->assign(array(
        'users' => $users,
        'company' => $company,
        'notify_url' => $notify_url
      ));
      
      $issue_data = $this->request->post('issue');
      
      if ($this->request->isSubmitted()) {
        if(isset($issue_data['send_emails']) && $issue_data['send_emails']) {
          $issue_to = Users::findById($issue_data['user_id']);
          if (instance_of($issue_to, 'User')) {
            $filename_name = 'invoice_'.$this->active_invoice->getId().'.pdf';
            $filename = WORK_PATH.'/' . $filename_name;
            
            require_once(INVOICING_MODULE_PATH.'/models/InvoicePdf.class.php');
            InvoicePDF::save($this->active_invoice, $filename);
            
            ApplicationMailer::send($issue_to, 'invoicing/issue', array(
              'issued_by_name' => $this->logged_user->getDisplayName(),
              'issued_by_url'  => $this->logged_user->getViewUrl(),
              'invoice_number' => $this->active_invoice->getNumber(), 
              'invoice_url'    => $this->active_invoice->getCompanyViewUrl(),
              'pdf_url'        => $this->active_invoice->getCompanyPdfUrl(),
            ), null, array(
              array('path' => $filename)
            ));
            @unlink($filename);
            flash_success('Email sent successfully');
          } else {
            flash_error('User does not exists');
            $this->redirectToUrl($notify_url);
          } // if
        } // if
        $this->redirectToUrl($this->active_invoice->getViewUrl());
      } // if
      
    } // notify
    
    /**
     * Change invoice status to CANCELED
     *
     * @param void
     * @return null
     */
    function cancel() {
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_invoice->canCancel($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_invoice->setStatus(INVOICE_STATUS_CANCELED, $this->logged_user, new DateTimeValue());
        $save = $this->active_invoice->save();
        if($save && !is_error($save)) {
          db_commit();
          
          $issued_to_user = $this->active_invoice->getIssuedTo();
          if (instance_of($issued_to_user, 'User')) {
            $notify_users = array($issued_to_user);
            if ($issued_to_user->getId() != $this->logged_user->getId()) {
              $notify_users[] = $this->logged_user;
            } // if

            ApplicationMailer::send($notify_users, 'invoicing/cancel', array(
              'closed_by_name' => $this->logged_user->getDisplayName(),
              'closed_by_url'  => $this->logged_user->getViewUrl(),
              'invoice_number' => $this->active_invoice->getNumber(), 
              'invoice_url' => $this->active_invoice->getCompanyViewUrl(),
            ));            
          } // if
          flash_success('Invoice #:number has been canceled', array('number' => $this->active_invoice->getName($short)));
        } else {
          db_rollback();
          flash_error('Failed to cancel invoice #:number', array('number' => $this->active_invoice->getName($short)));
        } // if
        
        $this->redirectToUrl($this->active_invoice->getViewUrl());
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // cancel

    /**
     * Drop invoice
     * invoices shuld not be dropped, only drafts
     *
     * @param void
     * @return null
     */
    function delete() {
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if

      if(!$this->active_invoice->canDelete($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $delete = $this->active_invoice->delete();
        if($delete && !is_error($delete)) {
          db_commit();
          flash_success(':invoice has been deleted', array('invoice' => $this->active_invoice->getName()));
        } else {
          db_rollback();
          flash_error('Failed to delete :invoice', array('invoice' => $this->active_invoice->getName()));
        } // if
        
        $this->redirectTo('invoices');
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // delete
    
    /**
     * Show time attached to a particular invoice
     *
     * @param void
     * @return null
     */
    function time() {
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_invoice->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->smarty->assign(array(
        'time_records' => $this->active_invoice->getTimeRecords($this->logged_user->getVisibility()),
      ));
    } // time
    
    /**
     * Release all time records related to this invoice
     *
     * @param void
     * @return null
     */
    function time_release() {
      if($this->active_invoice->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_invoice->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isSubmitted()) {
        $release = $this->active_invoice->releaseTimeRecords();
        if($release && !is_error($release)) {
          flash_success('Releated time records have been releated');
        } else {
          flash_error('Failed to release related time records');
        } // if
        $this->redirectToUrl($this->active_invoice->getViewUrl());
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // time_release
    
    /**
     * Send client address details
     * 
     * @param void
     * @return void
     */
    function company_details() {
      if (!$this->request->isAsyncCall()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      $client_id = $this->request->get('company_id');
      $client_company = Companies::findById($client_id);
      if (!instance_of($client_company, 'Company')) {
        $this->httpError(HTTP_ERR_NOT_FOUND,' ');
      } // if
      
      $company_address = CompanyConfigOptions::getValue('office_address', $client_company);
      echo $company_address;
      die();
    } // if
  }

?>