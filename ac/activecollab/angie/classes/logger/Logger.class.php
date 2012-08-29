<?php

  /**
   * Logger class
   *
   * @package angie.library.logger
   */
  class Logger extends AngieObject {
  
    /**
    * Logger messages
    * 
    * @var array
    */
    var $messages = array();
    
    /**
    * Grouped messages
    * 
    * @var array
    */
    var $grouped_messages = array();
    
    /**
    * Add message to log
    *
    * @param string $message
    * @param integer $level
    * @param sting $group
    * @return null
    */
    function logMessage($message, $level = LOG_LEVEL_INFO, $group = null) {
      $log_entry = array($message, $level);
      
      $this->messages[] = $log_entry;
      
      if(trim($group)) {
        if(!isset($this->grouped_messages[$group])) {
          $this->grouped_messages[$group] = array();
        } // if
        
        $this->grouped_messages[$group][] = $log_entry;
      } // if
    } // logMessage
    
    /**
    * Log entries to file
    *
    * @param string $file_path
    * @return boolean
    */
    function logToFile($path) {
      $result = "Logged on: " . date(DATE_COOKIE) . "\nAvailable groups: " . implode(', ', array_merge(array('all'), array_keys($this->grouped_messages))) . "\n\nall:\n\n";
      
      $counter = 1;
      foreach($this->messages as $entry) {
        list($message, $level) = $entry;
        $result .= $this->prepareMessageForFile($message, $level, $counter);
        $counter++;
      } // foreach
      
      foreach($this->grouped_messages as $group => $messages) {
        $result .= "\n$group\n\n";
        
        $counter = 1;
        foreach($messages as $entry) {
          list($message, $level) = $entry;
          $result .= $this->prepareMessageForFile($message, $level, $counter);
          $counter++;
        } // foreach
      } // foreach
      
      $result .= "\n======================================================\n\n";
      $result .= file_exists($path) ? file_get_contents($path) : '';
      
      return file_put_contents($path, $result);
    } // logToFile
    
    /**
    * Format single message to be saved into file
    *
    * @param string $message
    * @param integer $level
    * @param string $group
    * @return string
    */
    function prepareMessageForFile($message, $level, $counter) {
      $level_string = '<unknown>';
      switch($level) {
        case LOG_LEVEL_INFO:
          $level_string = 'info';
          break;
        case LOG_LEVEL_NOTICE:
          $level_string = 'notice';
          break;
        case LOG_LEVEL_WARNING:
          $level_string = 'warning';
          break;
        case LOG_LEVEL_ERROR:
          $level_string = 'error';
          break;
      } // switch
      
      return "#$counter - $level_string - $message\n";
    } // prepareMessageForFile
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Return global logger instance
    *
    * @param void
    * @return Logger
    */
    function &instance() {
      static $instance = null;
      if($instance === null) {
        $instance = new Logger();
      } // if
      return $instance;
    } // instance
  
  } // Logger

?>