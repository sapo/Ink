<?php

  /**
   * AngieObject class
   */
  class AngieObject {
    
    /**
     * Object constructor
     *
     * @param void
     * @return Object
     */
    function AngieObject() {
      $args = func_get_args();
      
      // Call constructor, with or without args
      if(is_array($args)) {
        call_user_func_array(array(&$this, '__construct'), $args); 
      } else {
        $this->__construct();
      } // if
    } // AngieObject
  
    /**
     * Construct the AngieObject
     *
     * @param void
     * @return AngieObject
     */
    function __construct() { 
      
    } // __construct
  
  }

?>