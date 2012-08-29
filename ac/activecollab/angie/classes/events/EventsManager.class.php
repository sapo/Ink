<?php

  /**
   * Events manager
   * 
   * @package angie.library.events
   */
  class EventsManager {
  
    /**
     * Array of event definitions
     *
     * @var array
     */
    var $events = array();
    
    /**
     * Current module
     * 
     * Used by loadByModules function to remember the name of current module. 
     * When this value is present, by no 'module' is not set, liste() method 
     * will use this value
     *
     * @var string
     */
    var $current_module = null;
    
    /**
     * Load event handlers from a list of modules
     *
     * @param array $modules
     * @return null
     */
    function loadByModules($modules) {
      foreach($modules as $module) {
        $this->current_module = $module->getName();
        $module->defineHandlers($this);
      } // foreach
    } // loadByModules
    
    /**
     * Subscribe $callback function to $event
     *
     * @param string $event
     * @param string $callback
     * @param string $module
     * @return null
     */
    function listen($event, $callback, $module = null) {
      if($module === null) {
        $module = $this->current_module;
      } // if
      
      if(is_array($event)) {
        foreach($event as $single_event) {
          $this->listen($single_event, $callback, $module);
        } // foreach
      } else {
        $handler = array(
          $module . '_handle_' . $callback, 
          APPLICATION_PATH . '/modules/' . $module . '/handlers/' . $callback . '.php'
        );
        
        if(isset($this->events[$event])) {
          if(!in_array($callback, $this->events[$event])) {
            $this->events[$event][] = $handler;
          } // if
        } else {
          $this->events[$event] = array($handler);
        } // if
      } // if
    } // listen
    
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
    function trigger($event, $params, $result = null) {
      log_message("Event '$event' triggered", LOG_LEVEL_INFO, 'events');
      if(isset($this->events[$event])) {
        if(is_foreachable($this->events[$event])) {
          foreach($this->events[$event] as $handler) {
            
            // Extract callback function name and expected location
            list($callback, $location) = $handler;
            
            // If handler function is not defined include file
            if(!function_exists($callback)) {
              require_once $location;
            } // if
            
            // Go baby go...
            $callback_result = call_user_func_array($callback, $params);            
            log_message("Callback '$callback' called for '$event'. Execution result: " . var_export($callback_result, true), LOG_LEVEL_INFO, 'events');
            
            if($callback_result === false) {
              return $result; // break here if we get FALSE
            } // if
            
            if(is_array($result)) {
              $result[] = $callback_result;
            } elseif(is_string($result)) {
              $result .= $callback_result;
            } elseif(is_int($result) || is_float($result)) {
              $result += $callback_result;
            } // if
          } // foreach
        } // if
        return $result;
      } // if
    } // trigger
    
    /**
     * Return manager instance
     *
     * @param void
     * @return EventsManager
     */
    function &instance() {
      static $instance = null;
      if($instance === null) {
        $instance = new EventsManager();
      } // if
      return $instance;
    } // instance
  
  } // EventsManager

?>