<?php

  /**
   * TaxRate class
   * 
   * @package activeCollab.modules.invocing
   * @subpackage models
   */
  class TaxRate extends BaseTaxRate {
    
    /**
     * Return verbose percentage
     *
     * @param void
     * @return string
     */
    function getVerbosePercentage() {
      $verbose = (float) number_format($this->getPercentage(), 2, '.', '');
      return "$verbose%";
    } // getVerbosePercentage
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can update this rate
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
      return $user->isAdministrator();
    } // canEdit
    
    /**
     * Returns true if $user can delete this tax rate
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      return $user->isAdministrator() && InvoiceItems::countByTaxRate($this) == 0;
    } // canDelete

    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Return edit currency URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('admin_tax_rate_edit', array('tax_rate_id' => $this->getId()));
    } // getEditUrl

    /**
     * Return delete currency URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('admin_tax_rate_delete', array('tax_rate_id' => $this->getId()));
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
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Tax Rate name is required'), 'name');
      } // if
    } // validate

  }

?>