<?php

  /**
   * select_datetime_format helper defintiion
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select datetime format widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_datetime_format($params, &$smarty) {
    $e = DIRECTORY_SEPARATOR == '\\' ? '%d' : '%e'; // Windows does not support %e
    
    $presets = array(
      'date' => array(
        "%b $e. %Y",
        "%a, %b $e. %Y",
        "$e %b %Y",
        "%Y/%m/$e",
        "%m/$e/%Y",
      ),
      'time' => array(
        '%I:%M %p',
        '%H:%M',
      )
    );
    
  	$mode = 'date';
  	if(array_key_exists('mode', $params)) {
  	  $mode = $params['mode'];
  	  unset($params['mode']);
  	} // if
  	
  	$value = null;
  	if(array_key_exists('value', $params)) {
  	  $value = $params['value'];
  	  unset($params['value']);
  	} // if
  	
  	$optional = false;
  	if(array_key_exists('optional', $params)) {
  	  $optional = (boolean) $params['optional'];
  	  unset($params['optional']);
  	} // if
  	
  	$reference_time = new DateTimeValue('2007-11-21 20:45:15');
  	
  	$options = array();
  	if($optional) {
  	  $default_format = ConfigOptions::getValue("format_$mode");
  	  $default_value = strftime($default_format, $reference_time->getTimestamp());
  	  
  	  $options[] = option_tag(lang('-- System Default (:value) --', array('value' => $default_value)), '');
  	  $options[] = option_tag('', '');
  	} // if
  	
  	foreach($presets[$mode] as $v) {
  	  $option_attributes = $v === $value ? array('selected' => true) : null;
  	  $options[] = option_tag(strftime($v, $reference_time->getTimestamp()), $v, $option_attributes);
  	} // foreach
  	
  	return select_box($options, $params);
  } // smarty_function_select_datetime_format

?>