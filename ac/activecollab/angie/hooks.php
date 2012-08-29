<?php

  /**
   * Set of system level hooks that can be overriden by application
   *
   * @package angie
   */
  
  if(!function_exists('handle_fatal_error')) {
  
    /**
    * This hook is called when system experience fatal error
    *
    * @param Error $error
    * @return null
    */
    function handle_fatal_error($error) {
      if(DEBUG >= DEBUG_DEVELOPMENT) {
        dump_error($error);
        die();
      } else {
        print '<p style="text-align: left; background: red; color: white; padding: 5px; font: 12px Verdana; font-weight: normal;">Fatal error: We failed to executed your request</p>';
        die();
      } // if
    } // handle_fatal_error
  
  } // if
  
  if(!function_exists('get_system_gmt_offset')) {

    /**
    * Return system GMT offset
    * 
    * Return number of seconds system is offset from GMT (timezone and DST are 
    * taken into calculation based on system settings)
    *
    * @param void
    * @return integer
    */
    function get_system_gmt_offset() {
      return 0;
    } // get_system_gmt_offset
    
  } // if
  
  if(!function_exists('get_user_gmt_offset')) {
  
    /**
    * Return user GMT offset
    * 
    * Return number of seconds that current user is away from the GMT. If user is 
    * not logged in this function should return system offset
    *
    * @param void
    * @return integer
    */
    function get_user_gmt_offset() {
      return get_system_gmt_offset();
    } // get_user_gmt_offset
  
  } // if
  
?>