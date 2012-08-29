<?php

  /**
   * Render select currency box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_currency($params, &$smarty) {
    $value = array_var($params, 'value', null, true);
    
    $options = array();
    
    $currencies = Currencies::findAll();
    foreach($currencies as $currency) {
      $option_attributes = array('code' => $currency->getCode());
      if($currency->getId() == $value) {
        $option_attributes['selected'] = true;
      } // if
      $options[] = option_tag($currency->getName() . ' (' . $currency->getCode() . ')', $currency->getId(), $option_attributes);
    } // foreach
    
    return select_box($options, $params);
  } // smarty_function_select_currency

?>