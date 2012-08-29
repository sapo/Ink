<?php

  /**
   * calendar helper
   *
   * @package activeCollab.modules.calendar
   * @subpackage helpers
   */
  
  /**
   * Render calendar
   * 
   * Params:
   * 
   * - data  - Calendar data
   * - first_week_day - Value for first day in week from settings
   * @param array $params
   * @return string
   */
  function smarty_function_calendar($params, &$smarty) {
    $first_week_day = 0;
    if(isset($params['first_week_day'])) {
      $first_week_day = (integer) array_var($params, 'first_week_day');
      unset($params['first_week_day']);
    } // if
    
    $data = array();
    if(isset($params['data'])) {
    	$data = array_var($params, 'data');
    	unset($params['first_week_day']);
    } // if
    
    $days = array(
      0 => lang('Sunday'),
      1 => lang('Monday'),
      2 => lang('Tuesday '),
      3 => lang('Wednesday'),
      4 => lang('Thursday'),
      5 => lang('Friday'),
      6 => lang('Saturday'),
    );
    
    $data_keys = array_keys($data);
    $date = $data_keys[0];
    $date = explode('-', $date);
    
    $year = $date[0]; // year to render
    $month = $date[1]; // month to render
    
    $begining_of_month = DateTimeValue::beginningOfMonth($month, $year);
    $end_of_month = DateTimeValue::endOfMonth($month, $year);
    $first_month_day = date('w', $begining_of_month->getTimestamp());

    $calendar = "<div id=\"calendar\">\n<table>\n";
    // header
    $calendar .= "\t<thead>\n";
    $from =  $first_week_day;
    $start_point = null;
    for ($i=0; $i < 7; $i++){
      $day = ($from > 6) ? $days[$from - 7] : $days[$from];
      $calendar .= "\t\t<th>$day</th>\n";      
      $from++;
      if($day == $days[$first_month_day]) {
      	$start_point = $i;
      } // if
    }
    $calendar .= "\t</thead>\n";
    
    // data
    $calendar .= "\t<tr>\n";
    $curr_day = 1;
    for ($i=1; $i<36; $i++){
      if(($i-1) >= $start_point) {
        
	      if($curr_day <= $end_of_month->getDay()) {
	      	$calendar .= "\t\t<td><span>$curr_day</span>"; 
	      	
	      	$items = $data[$year . '-' . $month . '-' . $curr_day]['items'];
	        $counts = $data[$year . '-' . $month . '-' . $curr_day]['counts'];
	      	// milestones
          foreach($items as $milestone) {
          	if(instance_of($milestone, 'Milestone')) {
          		$calendar .= "<p>".$milestone->getName()."</p>";
          	}
          } // foreach
          
          if($counts) {
          	if($counts['Ticket']) {
          	  $calendar .= "<p>(" . $counts['Ticket'] . ')' . lang('Tickets') . "</p>";
          	} // if
          	if($counts['Task']) {
          	  $calendar .= "<p>(" . $counts['Task'] . ')' . lang('Tasks') . "</p>";
          	} // if
          } // if
          
	      	$calendar .= "</td>\n"; 
	        $curr_day++;
	      } else {
	      	$calendar .= "\t\t<td></td>\n";
	      } // if
      } else {
      	$calendar .= "\t\t<td></td>\n";  
      }
      $calendar .= ($i%7 == 0 && $i<35) ? "\t</tr>\n\t<tr>\n" : null;     
    }
    $calendar .= "\t</tr>\n";
    $calendar .= "</table>\n</div>\n";
    
    return $calendar;
  } // smarty_function_calendar

?>