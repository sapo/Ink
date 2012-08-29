<?php
  // we need admin controller
  use_controller('admin');

  /**
   * Invoice note templates controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class InvoiceNoteTemplatesAdminController extends AdminController {

    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'invoice_note_templates_admin';
    
    /**
     * Active Invoice note
     *
     * @var InvoiceNoteTemplate
     */
    var $active_note;
           
    /**
     * Contruct note templates settings controller
     *
     * @param Request $request
     * @return InvoiceNoteTemplatesAdminController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->wireframe->addBreadCrumb(lang('Invoicing'), assemble_url('admin'));
      $this->wireframe->addBreadCrumb(lang('Invoice Note Templates'), assemble_url('admin_invoicing_notes'));
      
      $this->active_note = InvoiceNoteTemplates::findById($this->request->get('note_id'));
      if (!instance_of($this->active_note, 'InvoiceNoteTemplate')) {
        $this->active_note = new InvoiceNoteTemplate();
      } // if
            
      $this->smarty->assign(array(
        'active_note' => $this->active_note,
        'add_note_url' => assemble_url('admin_invoicing_note_add'),
      ));
    } // __construct
    
    
    /**
     * Predefined items main page
     * 
     * @param void
     * @return null
     */
    function index() {
      $this->wireframe->addPageAction(lang('New Note Template'), assemble_url('admin_invoicing_note_add'));
      $this->smarty->assign(array(
        'invoice_note_templates' => InvoiceNoteTemplates::findAll(),
        'reorder_note_templates_url' => assemble_url('admin_invoicing_notes_reorder'),
      ));
    } // index
    
    /**
     * Add Note Page
     *
     * @param void
     * @return void
     */
    function add() {
      $note_data = $this->request->post('note');
      if (!is_foreachable($note_data)) {
        $note_data = array();
      } // if
      
      $this->smarty->assign(array(
        'note_data' => $note_data
      ));
      
      if ($this->request->isSubmitted()) {
        $this->active_note->setAttributes($note_data);
        $save = $this->active_note->save();

        if ($save && !is_error($save)) {
          flash_success('Note template added succesfully');
          $this->redirectToUrl($this->active_note->getViewUrl());
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
      if ($this->active_note->isNew()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      $note_data = $this->request->post('note');
      if (!is_foreachable($note_data)) {
        $note_data = array(
          'name' => $this->active_note->getName(),
          'content' => $this->active_note->getContent()
        );
      } // if
      
      $this->smarty->assign(array(
        'note_data' => $note_data
      ));
      
      if ($this->request->isSubmitted()) {
        $this->active_note->setAttributes($note_data);
        
        $save = $this->active_note->save();
        if ($save && !is_error($save)) {
          flash_success ('Note template edited');
          $this->redirectToUrl($this->active_note->getViewUrl());
        } else {
          $this->smarty->assign(array(
            'errors' => $save
          ));
        } // if
      } // if
    } // edit_note
    
    /**
     * Delete Note Page
     * 
     * @param void
     * @return void
     */
    function delete() {
      if (!$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      if ($this->active_note->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $delete = $this->active_note->delete();
      if ($delete && !is_error($delete)) {
        flash_success('Note template successfully deleted');
        $this->redirectTo('admin_invoicing_notes');
      } else {
        flash_error('Failed to delete note template');
        $this->redirectTo('admin_invoicing_notes');
      } // if
    } // delete_note
    
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
          $sql = 'UPDATE `'.TABLE_PREFIX.'invoice_note_templates` SET `position`=\''.($x+1).'\' WHERE `id`='.$template_ids[$x];
          db_execute($sql);
        } // for
      } // if
      
      $this->httpOk();
    } // reorder
    
  }