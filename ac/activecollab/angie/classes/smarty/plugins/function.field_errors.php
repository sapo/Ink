<?php

  /**
  * Render all field errors as a list
  * 
  * Properties:
  * 
  * - field - field name
  * - errors - errors container (ValidationErrors instance)
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_field_errors($params, &$smarty) {
    $field = array_var($params, 'field');
    if(empty($field)) {
      return new InvalidParamError('field', $field, "'field' property is required for 'field_errors' helper", true);
    } // if
    
    $errors = array_var($params, 'errors');
    if(empty($errors)) {
      return new InvalidParamError('errors', $field, "'errors' property is require for 'field_errors' helper", true);
    } // if
    
    if(instance_of($errors, 'ValidationErrors') && $errors->hasError($field)) {
      $result = '<p class="errorField">';
      
      $messages = array();
      foreach($errors->getFieldErrors($field) as $error_message) {
        $messages[] = "<strong>$error_message</strong>";
      } // if
      
      return $result . implode("<br />\n", $messages) . "\n</p>";
    } // if
    
    return '';
  } // smarty_function_field_errors

?>