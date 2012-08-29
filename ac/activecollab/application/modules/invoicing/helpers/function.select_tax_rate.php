<?php

  /**
   * Render select tax rate box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_tax_rate($params, &$smarty) {
    $value = array_var($params, 'value', null, true);
    $optional = (boolean) array_var($params, 'optional', false, true);

    $options = array();
    if($optional) {
      $options[] = option_tag('-- No Tax --', '');
    } // if

    $tax_rates = TaxRates::findAll();
    foreach($tax_rates as $tax_rate) {
      $option_attributes = $tax_rate->getId() == $value ? array('selected' => true) : null;
      $options[] = option_tag($tax_rate->getName() . ' (' . $tax_rate->getPercentage() . ')', $tax_rate->getId(), $option_attributes);
    } // foreach

    return select_box($options, $params);
  } // smarty_function_select_tax_rate

?>