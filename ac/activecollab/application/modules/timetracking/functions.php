<?php

  /**
   * Timetracking module functions
   *
   * @package activeCollab.modules.timetracking
   */
  
  /**
   * Convert Time to Float Value
   *
   * @param mixed $time
   * @return float
   */
  function time_to_float($time) {
    if(strpos($time, ':') !== false) {
      $time_arr = explode(':', $time);
    	
    	if(count($time_arr) < 2) {
    	  $float_time = round($time, 2);
    	} else {
    	  $minutes = ($time_arr[1] > 60) ? 60 : $time_arr[1];
    		$float_time = round($time_arr[0] + ($minutes/60), 2);
    	} // if
    	
    	$time = round($float_time, 2);
    } // if
    
    if(strpos($time, ',') !== false) {
      $time = str_replace(',', '.', $time);
    } // if
    
    return (float) $time;
  } // ConvTimeToFloat 
  
  /**
   * Convert Float Value to Time
   *
   * @param float $time
   * @return string
   */
  function float_to_time($time) {
    if (is_float($time)) {
    	$time_dec = $time - floor($time);
    	
    	$hours = floor($time);
    	$minutes = round($time_dec*60);
    	
    	return $hours.':'.$minutes;
    }else {
    	return $time;
    } // if
  } // convFloatToTime
  
?>