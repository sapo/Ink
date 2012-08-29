<?php
  // we need admin controller
  use_controller('admin');

  /**
   * Invoice item templates controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class InvoiceItemTemplatesAdminController extends AdminController {

    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'invoice_item_templates_admin';
    
    /**
     * Currently active predefined invoice item
     *
     * @var InvoiceItemTemplate
     */
    var $active_item_template = false;
    
    /**
     * Contruct predefined invoice items admin controler
     *
     * @param Request $request
     * @return InvoiceItemTemplatesAdminController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->wireframe->addBreadCrumb(lang('Invoicing'), assemble_url('admin'));
      $this->wireframe->addBreadCrumb(lang('Invoice Item Templates'), assemble_url('admin_invoicing_items'));
      
      $this->active_item_template = InvoiceItemTemplates::findById($this->request->get('item_id'));
      if (!instance_of($this->active_item_template, 'InvoiceItemTemplate')) {
        $this->active_item_template = new InvoiceItemTemplate();
      } // if
            
      $this->smarty->assign(array(
        'active_item_template' => $this->active_item_template,
        'add_template_url' => assemble_url('admin_invoicing_item_add'),
      ));
    } // __construct
    
    /**
     * Predefined items main page
     * 
     * @param void
     * @return null
     */
    function index() {
      $this->wireframe->addPageAction(lang('New Invoice Item'), assemble_url('admin_invoicing_item_add'));
      $this->smarty->assign(array(
        'invoice_item_templates' => InvoiceItemTemplates::findAll(),
        'reorder_item_templates_url' => assemble_url('admin_invoicing_items_reorder'),
      ));
    } // index
    
    /**
     * Add Note Page
     *
     * @param void
     * @return void
     */
    function add() {
      $item_data = $this->request->post('item');
      if (!is_foreachable($item_data)) {
        $item_data = array(
          'quantity' => 1,
          'unit_cost' => 1
        );
      } // if
      
      $this->smarty->assign(array(
        'item_data' => $item_data
      ));
      
      if ($this->request->isSubmitted()) {
        $this->active_item_template->setAttributes($item_data);
        
        $save = $this->active_item_template->save();
        if ($save && !is_error($save)) {
          flash_success('New item template is successfully created');
          $this->redirectTo('admin_invoicing_items');
        } else {
          $this->smarty->assign(array(
            'errors' => $save
          ));
        } // if
      } // if
    } // add_note
    
    /**
     * Edit Note Page
     * 
     * @param void
     * @return void
     */
    function edit() {
      if ($this->active_item_template->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $this->wireframe->addBreadCrumb(clean($this->active_item_template->getDescription()), $this->active_item_template->getViewUrl());
      
      $item_data = $this->request->post('item');
      if (!is_foreachable($item_data)) {
        $item_data = array(
          'description' => $this->active_item_template->getDescription(),
          'unit_cost' => $this->active_item_template->getUnitCost(),
          'quantity' => $this->active_item_template->getQuantity(),
          'tax_rate_id' => $this->active_item_template->getTaxRateId(),
        );
      } // if
      
      $this->smarty->assign(array(
        'item_data' => $item_data,
      ));
      
      if ($this->request->isSubmitted()) {
        $this->active_item_template->setAttributes($item_data);
        
        $save = $this->active_item_template->save();
        if ($save && !is_error($save)) {
          flash_success('Item Template is successfully edited');
          $this->redirectTo('admin_invoicing_items');
        } else {
          $this->smarty->assign(array(
            'errors' => $save
          ));
        } // if
      } // if
      
    } // edit_note
    
    /**
     * Delete Invoice Item Template
     * 
     * @param void
     * @return void
     */
    function delete() {
      if (!$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      if ($this->active_item_template->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $delete = $this->active_item_template->delete();
      if ($delete && !is_error($delete)) {
        flash_success('Item template successfully removed');
        $this->redirectTo('admin_invoicing_items');
      }  else {
        flash_success('Failed to remove item template');
        $this->redirectTo('admin_invoicing_items');        
      } // if
    } // delete
    
    /**
     * Reorder Invoice Item Templates
     * 
     * @param void
     * @return null
     */
    function reorder() {
      $this->skip_layout = true;
      
      if (!$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      $template_ids = $this->request->post('reorder');
      if (is_foreachable($template_ids)) {
        for ($x=0; $x < count($template_ids); $x++) {
          $sql = 'UPDATE `'.TABLE_PREFIX.'invoice_item_templates` SET `position`=\''.($x+1).'\' WHERE `id`='.$template_ids[$x];
          db_execute($sql);
        } // for
      } // if
      
      $this->httpOk();
    } // reorder
    
  } // InvoiceItemTemplatesAdminController