<?php

  /**
   * Render date picker
   *
   * Parameters:
   * 
   * - id - Control ID
   * - value - datetime value that is select, NULL means today
   * - start_date - datetime value of start date, NULL means no start date
   * - end_date - datetime value of last selectable day, NULL means no end date
   * - first_week_day - 7 for Sunday, 1 for Monday
   * - show_timezone - wether to show or hide timezone information
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_date($params, &$smarty) {
    static $counter = 0;
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $counter++;
      $id = 'select_date_' . $counter;
    } // if
    
    $params['id'] = $id;
    $params['type'] = 'text';
    
    $formatted_value = array_var($params, 'value', null, true);
    if(instance_of($formatted_value, 'DateValue')) {
      $formatted_value = date('Y/m/d', $formatted_value->getTimestamp());
    } // if
    
    $params['value'] = $formatted_value;
    
    $start_date = array_var($params, 'start_date', "2000/01/01", true);
    if(instance_of($start_date, 'DateValue')) {
      $start_date = date('Y/m/d', $start_date->getTimestamp());
    } // if
    
    $end_date = array_var($params, 'end_date', '2050/01/01', true);
    if(instance_of($end_date, 'DateValue')) {
      $end_date = date('Y/m/d', $end_date->getTimestamp());
    } // if
    
    $first_week_day = array_var($params, 'first_week_day', UserConfigOptions::getValue('time_first_week_day', $smarty->get_template_vars('logged_user')), true);
    
    $result = '<div class="select_date">' . open_html_tag('input', $params, true);
    if($formatted_value) {
      $result .= '<script type="text/javascript">Date.firstDayOfWeek = ' . $first_week_day . '; Date.format = \'yyyy/mm/dd\'; $("#' . $id . '").datePicker().dpSetStartDate("' . $start_date . '").dpSetEndDate("' . $end_date . '").dpSetSelected("' . $formatted_value . '");</script>';
    } else {
      $result .= '<script type="text/javascript">Date.firstDayOfWeek = ' . $first_week_day . '; Date.format = \'yyyy/mm/dd\'; $("#' . $id . '").datePicker().dpSetStartDate("' . $start_date . '").dpSetEndDate("' . $end_date . '");</script>';
    } // if
    
    return $result . '</div>';
  } // smarty_function_select_date

?>