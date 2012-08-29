<?php

  /**
   * InvoiceItemTemplate class
   */
  class InvoiceItemTemplate extends BaseInvoiceItemTemplate {
    
    // getters and setters
    
    /**
     * cached value of tax
     * 
     * @var TaxRate
     */
    var $tax_rate = false;
    
    /**
     * Return tax rate
     * 
     * @param null
     * @var TaxRate
     */
    function getTaxRate() {
      if ($this->tax_rate === false) {
        $this->tax_rate = TaxRates::findById($this->getTaxRateId());
      } // if
      return $this->tax_rate;
    } // getTaxRate
    
    // validate
    
    /**
     * Validate model
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if (!$this->validatePresenceOf('description')) {
        $errors->addError(lang('Description is required'), 'description');
      } // if
      
      if (!$this->getUnitCost()) {
        $this->setUnitCost(0);
      } // if
      
      if (!$this->validatePresenceOf('quantity')) {
        $errors->addError(lang('Quantity is required'), 'quantity');
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
      return assemble_url('admin_invoicing_items').'#Item_template_'.$this->getId();
    } // getViewUrl
    
    /**
     * Get edit url
     * 
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('admin_invoicing_item_edit', array(
        'item_id' => $this->getId(),
      ));
    } // getEditUrl
    
    /**
     * Get delete url
     * 
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('admin_invoicing_item_delete', array(
        'item_id' => $this->getId(),
      ));
    } // getDeleteUrl
    
  } // InvoiceItemTemplate

?>