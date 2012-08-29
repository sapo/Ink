<?php

  /**
   * Select language helper definition
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select language box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_language($params, &$smarty) {
    $default_language_id = ConfigOptions::getValue('language');
    
  	$value = $default_language_id;
  	if(array_key_exists('value', $params)) {
  	  $value = $params['value'];
  	  unset($params['value']);
  	} // if
  	
  	$optional = false;
  	if(array_key_exists('optional', $params)) {
  	  $optional = (boolean) $params['optional'];
  	  unset($params['optional']);
  	}
  	
  	$default_language = null;
  	$languages = Languages::findAll();
  	if(is_foreachable($languages)) {
  	  foreach($languages as $language) {
  	    if($language->getId() == $default_language_id) {
  	      $default_language = $language;
  	    } // if
  	  } // foreach
  	} // if
  	
  	$options = array();
  	if($optional) {
  	  if(instance_of($default_language, 'Language')) {
  	    $options[] = option_tag(lang('-- System Default (:value) --', array('value' => $default_language->getName())), '');
  	  } else {
  	    $options[] = option_tag(lang('-- None --'), '');
  	  } // if
  	  $options[] = option_tag('', '');
  	} // if
  	
  	if(is_foreachable($languages)) {
  	  foreach($languages as $language) {
  	    $option_attributes = $language->getId() == $value ? array('selected' => true) : null;
  	    $options[] = option_tag($language->getName(), $language->getId(), $option_attributes);
  	  } // foreach
  	} // if
  	
  	return select_box($options, $params);
  } // smarty_function_select_language

?>