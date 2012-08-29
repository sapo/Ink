<?php

  /**
   * InvoiceItemTemplates class
   */
  class InvoiceItemTemplates extends BaseInvoiceItemTemplates {
  
    /**
     * Find all predefined invoice items and sort them by position
     *
     */
    function findAll() {
      return InvoiceItemTemplates::find(array(
        'order' => 'ISNULL(position) ASC, position, id ASC'
      ));
    } // findAll
  
  } // InvoiceItemTemplates

?>