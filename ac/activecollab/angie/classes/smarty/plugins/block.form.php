<?php

  /**
   * Render form block
   * 
   * Parameters:
   * 
   * - All FORM element attributes
   * - block_labels - Use block or inline labels
   * - autofocus - Automatically focus first field, true by default
   * - ask_on_leave - Ask users confirmation when form data is changed and user 
   *   tries to navigate of the page. This setting is off by default
   * - disable_submit - If true Submit button will be disabled until all values 
   *   in the form are valid. Off by default...
   * - show_errors - Display errors
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_form($params, $content, &$smarty, &$repeat) {
    static $counter = 0;
    
    $action = array_var($params, 'action');
    if(str_starts_with($action, '?')) {
      $params['action'] = assemble_from_string($action);
    } // if
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $counter++;
      $id = 'system_form_' . $counter;
    } // if
    $params['id'] = $id;
    
    $is_uni = true;
    if(isset($params['uni'])) {
      $is_uni = (boolean) $params['uni'];
      unset($params['uni']);
    } // if
    
    $autofocus = false;
    if(isset($params['autofocus'])) {
      $autofocus = (boolean) $params['autofocus'];
      unset($params['autofocus']);
    } // if
    
    $ask_on_leave = false;
    if(isset($params['ask_on_leave'])) {
      $ask_on_leave = (boolean) $params['ask_on_leave'];
      unset($params['ask_on_leave']);
    } // if
    
    $disable_submit = false;
    if(isset($params['disable_submit'])) {
      $disable_submit = (boolean) $params['disable_submit'];
      unset($params['disable_submit']);
    } // if
    
    $show_errors = true;
    if(isset($params['show_errors'])) {
      $show_errors = (boolean) $params['show_errors'];
      unset($params['show_errors']);
    } // if
    
    $classes = array();
    if(isset($params['class'])) {
      $classes = explode(' ', $params['class']);
    } // if
    
    if($is_uni && !in_array('uniForm', $classes)) {
      $classes[] = 'uniForm';
    } // if
    
    if($autofocus && !in_array('focusFirstField', $classes)) {
      $classes[] = 'focusFirstField';
    } // if
    
    if($ask_on_leave && !in_array('askOnLeave', $classes)) {
      $classes[] = 'askOnLeave';
    } // if
    
    if($disable_submit && !in_array('disableSubmit', $classes)) {
      $classes[] = 'disableSubmit';
    } // if
    
    if($show_errors && !in_array('showErrors', $classes)) {
      $classes[] = 'showErrors';
    } // if
    
    $params['class'] = implode(' ', $classes);
    
    $block_labels = (boolean) array_var($params, 'block_labels', true);
    $class_for_labels = $block_labels ? 'blockLabels' : 'inlineLabels';
    
    $errors_code = "";
    $errors = array_var($params, 'errors');
    if(empty($errors)) {
      $errors = $smarty->get_template_vars('errors');
      if(instance_of($errors, 'ValidationErrors') && is_foreachable($errors->getFieldErrors(ANY_FIELD))) {
        $errors_code = '<div id="errorMsg"><h3>' . lang('Oops! We found some errors that need to be corrected before we can proceed') . '</h3><ol>';
        foreach($errors->getFieldErrors(ANY_FIELD) as $error_message) {
          $errors_code .= '<li>' . clean($error_message) . '</li>';
        } // foreach
        $errors_code .= '</ol></div>';
      } // if
    } // if
    
    $return = open_html_tag('form', $params) . "\n<div class=\"$class_for_labels\">\n$errors_code\n$content\n<input type=\"hidden\" name=\"submitted\" value=\"submitted\" style=\"display: none\" />\n</div>\n</form>\n";
    if($is_uni) {
      $return .= "<script type=\"text/javascript\">$('#$id').uniform();</script>\n";
    } // if
    
    return $return;
  } // smarty_block_form

?>