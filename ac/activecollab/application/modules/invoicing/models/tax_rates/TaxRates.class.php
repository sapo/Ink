<?php

  /**
   * TaxRates class
   */
  class TaxRates extends BaseTaxRates {

    /**
     * Return all tax rates sorted by name
     *
     * @param void
     * @return array
     */
    function findAll() {
      return TaxRates::find(array(
        'order' => 'name',
      ));
    } // findAll

  }

?>