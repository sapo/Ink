<?php

  /**
    * Events library initialization file
    *
    * @package angie.library.events
    */
  
  define('EVENTS_LIB_PATH', ANGIE_PATH . '/classes/events');
  
  require EVENTS_LIB_PATH . '/EventsManager.class.php';
  
  /**
   * Subscribe $callback to an $event
   * 
   * $events can be an array of events or single even name
   *
   * @param array $events
   * @param string $callback
   * @param string $module
   * @return null
   */
  function event_listen($events, $callback, $module = null) {
    static $instance = false;
    
    if($instance === false) {
      $instance =& EventsManager::instance();
    } // if
    $events = (array) $events;
    
    foreach($events as $event) {
      $instance->listen($event, $module . '_handle_' . $callback);
    } // foreach
  } // event_listen
  
  /**
   * Trigger specific event with a given parameters
   * 
   * $result is start value of result. It determines how data returned from 
   * callback functions will be handled. If $result is:
   * 
   * - array - values will be added as new elements
   * - integer or float - values will be added to the $result
   * - string - values will be appended to current value
   * - null - values returned from callback functions are ignored
   * 
   * If callback function returns FALSE executen is stopped and result made to 
   * that point is retuned
   * 
   * WARNING: $result is not passed by reference
   *
   * @param string $event
   * @param array $params
   * @param mixed $result
   * @return mixed
   */
  function event_trigger($event, $params = array(), $result = null) {
    static $instance = false;
    if($instance === false) {
      $instance =& EventsManager::instance();
    } // if
    return $instance->trigger($event, $params, $result);
  } // event_trigger

?>