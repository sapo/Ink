<?php

  /**
   * Currencies class
   */
  class Currencies extends BaseCurrencies {
  
    /**
     * Return all currencies sorted by name
     *
     * @param void
     * @return array
     */
    function findAll() {
      return Currencies::find(array(
        'order' => 'name',
      ));
    } // findAll
    
    /**
     * Return default currency
     *
     * @param void
     * @return Currency
     */
    function findDefault() {
      return Currencies::find(array(
        'order' => 'is_default DESC',
        'one' => true,
      ));
    } // findDefault
    
    /**
     * Set $currency as default
     *
     * @param Currency $currency
     * @return boolean
     */
    function setDefault($currency) {
      if($currency->getIsDefault()) {
        return true;
      } // if
      
      db_begin_work();
      
      $currency->setIsDefault(true);
      $update = $currency->save();
      if($update && !is_error($update)) {
        $update = db_execute('UPDATE ' . TABLE_PREFIX . 'currencies SET is_default = ? WHERE id != ?', false, $currency->getId());
        cache_remove_by_pattern(TABLE_PREFIX . 'currencies_id_*');
        
        if($update && !is_error($update)) {
          db_commit();
          return true;
        } // if
      } // if
      
      db_rollback();
      return $update;
    } // setDefault
  
  }

?>