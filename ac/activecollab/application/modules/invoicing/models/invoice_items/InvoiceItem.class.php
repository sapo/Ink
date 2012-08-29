<?php

  /**
   * InvoiceItem class
   * 
   * @package activeCollab.modules.invociing
   * @subpackage models
   */
  class InvoiceItem extends BaseInvoiceItem {
    
    /**
     * Cached tax rate instance
     *
     * @var TaxRate
     */
    var $tax_rate = false;
    
    /**
     * Return related tax rate
     *
     * @param void
     * @return TaxRate
     */
    function getTaxRate() {
      if($this->tax_rate === false) {
        $this->tax_rate = TaxRates::findById($this->getTaxRateId());
      } // if
      return $this->tax_rate;
    } // getTaxRate
    
    /**
     * Return tax rate name string
     *
     * @param void
     * @return string
     */
    function getTaxRateName() {
      $tax_rate = $this->getTaxRate();
      return instance_of($tax_rate, 'TaxRate') ? $tax_rate->getName() : '--';
    } // getTaxRateName
    
    /**
     * Return subtotal total cost of this item
     *
     * @param void
     * @return float
     */
    function getSubTotal() {
      return $this->getUnitCost() * $this->getQuantity();
    } // getSubTotal
    
    /**
     * Returns full price of the item
     * 
     * @param void
     * @return float
     */
    function getTotal() {
      return $this->getSubTotal() + $this->getTax();
    } // getTotal 
    
    /**
     * Return tax
     *
     * @param void
     * @return float
     */
    function getTax() {
      $tax_rate = $this->getTaxRate();
      return instance_of($tax_rate, 'TaxRate') ? $this->getSubTotal() * $tax_rate->getPercentage() / 100 : 0;
    } // getTax
    
    /**
     * Set ID-s of related time records
     *
     * @param array $ids
     * @return boolean
     */
    function setTimeRecordIds($ids) {
      db_begin_work();
      
      $execute = db_execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_time_records WHERE invoice_id = ? && item_id = ?', $this->getInvoiceId(), $this->getId());
      if($execute && !is_error($execute)) {
        if(is_foreachable($ids)) {
          $to_insert = array();
          $invoice_id = $this->getInvoiceId();
          $item_id = $this->getId();
          
          foreach($ids as $id) {
            $id = (integer) $id;
            if($id && !isset($to_insert[$id])) {
              $to_insert[$id] = "($invoice_id, $item_id, $id)";
            } // if
          } // foreach
          
          if(is_foreachable($to_insert)) {
            $execute = db_execute('INSERT INTO ' . TABLE_PREFIX . 'invoice_time_records (invoice_id, item_id, time_record_id) VALUES ' . implode(', ', $to_insert));
            if(!$execute || is_error($execute)) {
              db_rollback();
              return $execute;
            } // if
          } // if
        } // if
        
        db_commit();
        return true;
      } else {
        db_rollback();
        return $execute;
      } // if
    } // setTimeRecordIds
    
    /**
     * Retrieve TimeRecordIds
     * 
     * @param void
     * @return array
     */
    function getTimeRecordIds() {
      $execute = db_execute('SELECT `time_record_id` FROM ' . TABLE_PREFIX . 'invoice_time_records WHERE invoice_id = ? && item_id = ?', $this->getInvoiceId(), $this->getId());
      if ($execute && !is_error($execute)) {
        if (is_foreachable($execute)) {
          $time_record_ids = array();
          foreach ($execute as $time_record) {
          	$time_record_ids[] = $time_record['time_record_id'];
          } // foreach
          return $time_record_ids;
        } // if
        return null;
      } else {
        return $execute;
      } // if
    } // if
  
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
      if(!$this->validatePresenceOf('description')) {
        $errors->addError(lang('Item description is required'), 'name');
      } // if
      
      if(!$this->validatePresenceOf('quantity')) {
        $errors->addError(lang('Quantity is required'), 'quantity');
      } // if
      
      if (!$this->getUnitCost()) {
        $this->setUnitCost(0);
      } // if
      
      return parent::validate($errors);
    } // validate
  
  }

?>