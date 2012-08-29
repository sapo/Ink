<?php

  /**
  * Render yes - no widget
  * 
  * Parameters:
  * 
  * - name - name used for radio group
  * - value - if TRUE Yes will be selected, No will be selected otherwise
  * - yes - yes lang, default is 'Yes'
  * - no - no lang, default is 'No'
  * - id - ID base, if not present script will generate one
  *
  * @param array $params
  * @param Smarty $smarty
  * @return string
  */
  function smarty_function_yes_no($params, &$smarty) {
    static $ids;
    
    $name = array_var($params, 'name');
    if(empty($name)) {
      return new InvalidParamError('name', $name, "'name' paremeter is required for 'yes_no' helper", true);
    } // if
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      if(!is_array($ids)) {
        $ids = array();
      } // if
      
      do {
        $id = 'yesNo' . rand();
      } while(in_array($id, $ids));
      $ids[] = $id;
    } // if
    
    $value = (boolean) array_var($params, 'value');
    
    $yes_input_attributes = $no_input_attributes = array(
      'name' => $name,
      'type' => 'radio',
      'class' => 'inline',
      'disabled' => (boolean) array_var($params, 'disabled')
    ); // array
       
    $yes_input_attributes['id'] = $id . 'YesInput';
    $yes_input_attributes['value'] = '1';
    
    $no_input_attributes['id']  = $id . 'NoInput';
    $no_input_attributes['value'] = '0';
    $no_input_attributes['class'] = 'inline';
    
    if($value) {
      $yes_input_attributes['checked'] = 'checked';
    } else {
      $no_input_attributes['checked'] = 'checked';
    } // if
    
    $onOff = (boolean) array_var($params, 'on_off');
    
    if ($onOff) {
        $yesDisplay = lang("On");
        $noDisplay = lang("Off");
    }
    else {
        $yesDisplay = lang("Yes");
        $noDisplay = lang("No");
    }
    
    
    $yes = open_html_tag('label', array('for' => $yes_input_attributes['id'], 'class' => 'inline')) . open_html_tag('input', $yes_input_attributes, true) . array_var($params, 'yes', $yesDisplay) . '</label>';
    $no = open_html_tag('label', array('for' => $no_input_attributes['id'], 'class' => 'inline')) . open_html_tag('input', $no_input_attributes, true) . array_var($params, 'no', $noDisplay) . '</label>';
    
    return "<span class=\"yes_no\">$yes $no</span>";
  } // smarty_function_yes_no

?>