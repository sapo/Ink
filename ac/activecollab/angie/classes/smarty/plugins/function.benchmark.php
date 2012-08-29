<?php

  /**
   * Show benchmark table
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_benchmark($params, &$smarty) {
    if(DEBUG < DEBUG_DEVELOPMENT) {
      return '';
    } // if
    
    $benchmark =& BenchmarkTimer::instance();
    
    $db =& DBConnection::instance();
    $result = array(
      'Executed in: ' . (float) number_format($benchmark->TimeElapsed(), 3) . 's',
      'SQL queries: ' . $db->query_counter
    );
    
    if(function_exists('memory_get_usage')) {
      $result[] = 'Memory usage: ' . number_format(memory_get_usage() / 1048576, 2, '.', ',') . 'MB';
    } // if
    
    return '<p id="benchmark">' . implode('. ', $result) . '</p>';
  } // smarty_function_benchmark

?>