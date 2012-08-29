<?php

  /**
  * Wrap form field into a DIV
  * 
  * Properties:
  * 
  * - field - field name
  * - errors - errors container (ValidationErrors instance)
  * - show_errors - show errors list inside of field wrapper
  *
  * @param array $params
  * @param string $content
  * @param Smarty $smarty
  * @param boolean $repeat
  * @return null
  */
  function smarty_block_wrap($params, $content, &$smarty, &$repeat) {
    $field = array_var($params, 'field');
    if(empty($field)) {
      return new InvalidParamError('field', $field, "'field' property is required for 'wrap_field' helper", true);
    } // if
    
    $classes = array();
    if(isset($params['class'])) {
      $classes = explode(' ', $params['class']);
      unset($params['class']);
    } // if
    
    if(!in_array('ctrlHolder', $classes)) {
      $classes[] = 'ctrlHolder';
    } // if
    
    $error_messages = null;
    if(isset($params['errors'])) {
      $errors = $params['errors'];
    } else {
      $errors = $smarty->get_template_vars('errors');
    } // if
    if(instance_of($errors, 'ValidationErrors')) {
      if($errors->hasError($field)) {
        $classes[] = 'error';
        $error_messages = $errors->getFieldErrors($field);
      } // if
    } // if
    
    $show_errors = array_var($params, 'show_errors', true);
    
    $listed_errors = '';
    if(is_foreachable($error_messages) && $show_errors) {
      require_once $smarty->_get_plugin_filepath('function','field_errors');
      $listed_errors = smarty_function_field_errors(array(
        'field' => $field,
        'errors' => $errors
      ), $smarty);
    } // if
    
    $aid = array_var($params, 'aid');
    if($aid) {
      $aid = '<p class="aid">' . clean(lang($aid)) . '</p>';
    } // if
    
    // Unset helper properties, we need this for attributes
    unset($params['field']);
    unset($params['errors']);
    unset($params['show_errors']);
    unset($params['aid']);
    
    $params['class'] = implode(' ', $classes);
    return open_html_tag('div', $params) . "\n$listed_errors\n" . $content . $aid . "\n</div>";
  } // smarty_block_wrap

?>