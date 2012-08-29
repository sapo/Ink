<?php

  /**
   * Join items
   * 
   * array('Peter', 'Joe', 'Adam') will be join like:
   * 
   * 'Peter, Joe and Adam
   * 
   * where separators can be defined as paremeters.
   * 
   * Parements:
   * 
   * - items - array of items that need to be join
   * - separator - used to separate all elements except the last one. ', ' by 
   *   default
   * - final_separator - used to separate last element from the rest of the 
   *   string. ' and ' by default
   *
   * @param array $params
   * @return string
   */
  function smarty_function_join($params, &$smarty) {
    $items = array_var($params, 'items');
    $separator = array_var($params, 'separator', ', ');
    $final_separator = array_var($params, 'final_separator', lang(' and '));
    if(is_foreachable($items)) {
      $result = '';
      
      $items_count = count($items);
      $counter = 0;
      foreach($items as $item) {
        $counter++;
        
        if($counter < $items_count - 1) {
          $result .= $item . $separator;
        } elseif($counter == $items_count - 1) {
          $result .= $item . $final_separator;
        } else {
          $result .= $item;
        } // if
      } // if
      
      return $result;
    } else {
      return $items;
    } // if
  } // smarty_function_join

?>