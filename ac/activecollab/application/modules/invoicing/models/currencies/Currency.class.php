<?php

  /**
   * Currency class
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class Currency extends BaseCurrency {
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Check if $user can edit this currency
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
      return $user->isAdministrator();
    } // canEdit
    
    /**
     * Returns true if $user can delete this currency
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      return !$this->getIsDefault() && $user->isAdministrator() && Invoices::countByCurrency($this) == 0;
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
      return assemble_url('admin_currency_edit', array('currency_id' => $this->getId()));
    } // getEditUrl
    
    /**
     * Return set as default currency URL
     *
     * @param void
     * @return string
     */
    function getSetAsDefaultUrl() {
      return assemble_url('admin_currency_set_as_default', array('currency_id' => $this->getId()));
    } // getSetAsDefaultUrl
    
    /**
     * Return delete currency URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('admin_currency_delete', array('currency_id' => $this->getId()));
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
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name')) {
          $errors->addError(lang('Currency name needs to be unqiue'), 'name');
        } // if
      } else {
        $errors->addError(lang('Currency name is required'), 'name');
      } // if
      
      if($this->validatePresenceOf('code')) {
        if(!$this->validateUniquenessOf('code')) {
          $errors->addError(lang('Currency code needs to be unqiue'), 'code');
        } // if
      } else {
        $errors->addError(lang('Currency code is required'), 'code');
      } // if
    } // validate
  
  }

?>