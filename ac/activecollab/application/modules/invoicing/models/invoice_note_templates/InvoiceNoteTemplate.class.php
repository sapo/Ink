<?php

  /**
   * InvoiceNoteTemplate class
   */
  class InvoiceNoteTemplate extends BaseInvoiceNoteTemplate {
    
    // validate
    
    /**
     * Validate model
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if (!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Note name is required'), 'description');
      } // if
      
      if (!$this->validatePresenceOf('content')) {
        $errors->addError(lang('Note content is required'), 'unit_cost');
      } // if
      
      return parent::validate($errors);
    } // validate
    
    // URL-s
    
    /**
     * Get view url
     * 
     * @param void
     * @return string
     */
    function getViewUrl() {
      return assemble_url('admin_invoicing_notes').'#Item_note_'.$this->getId();
    } // getViewUrl
    
    /**
     * Get edit url
     * 
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('admin_invoicing_note_edit', array(
        'note_id' => $this->getId(),
      ));
    } // getEditUrl
    
    /**
     * Get delete url
     * 
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('admin_invoicing_note_delete', array(
        'note_id' => $this->getId(),
      ));
    } // getDeleteUrl
  
  }

?>