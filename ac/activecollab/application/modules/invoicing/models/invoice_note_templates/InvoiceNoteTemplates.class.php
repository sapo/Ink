<?php

  /**
   * InvoiceNoteTemplates class
   */
  class InvoiceNoteTemplates extends BaseInvoiceNoteTemplates {
  
  
    /**
     * Find all predefined invoice notes and sort them by position
     * 
     * @param void
     * @return null
     */
    function findAll() {
      return InvoiceNoteTemplates::find(array(
        'order' => 'ISNULL(position) ASC, position, id ASC'
      ));
    } // findAll
  
  }

?>