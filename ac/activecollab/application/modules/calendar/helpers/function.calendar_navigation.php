<?php

  /**
   * Render calendar navigation
   * 
   * Parameteres:
   * 
   * - month
   * - year
   * - URL pattern
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_calendar_navigation($params, &$smarty) {
    $month = (integer) array_var($params, 'month');
    $year = (integer) array_var($params, 'year');
    $pattern = array_var($params, 'pattern');
    
    if($month == 1) {
      $prev_month = array(
        'label' => $year - 1 . '/12',
        'url' => Calendar::getDashboardMonthUrl($year - 1, 12),
        'url' => str_replace(array('-YEAR-', '-MONTH-'), array($year - 1, 12), $pattern),
      );
    } elseif($month == 12) {
      $next_month = array(
        'label' => $year + 1 . '/1',
        'url' => str_replace(array('-YEAR-', '-MONTH-'), array($year + 1, 1), $pattern),
      );
    } // if
    
    if(!isset($prev_month)) {
      $prev_month = array(
        'label' => $year . '/' . ($month - 1),
        'url' => str_replace(array('-YEAR-', '-MONTH-'), array($year, $month - 1), $pattern),
      );
    } // if
    
    if(!isset($next_month)) {
      $next_month = array(
        'label' => $year . '/' . ($month + 1),
        'url' => str_replace(array('-YEAR-', '-MONTH-'), array($year, $month + 1), $pattern),
      );
    }
    
    $smarty->assign(array(
      '_prev_month'    => $prev_month,
      '_next_month'    => $next_month,
      '_current_month' => $month,
      '_current_year'  => $year,
    ));
    
    return $smarty->fetch(get_template_path('_calendar_navigation', null, CALENDAR_MODULE));
  } // smarty_function_calendar_navigation

?>