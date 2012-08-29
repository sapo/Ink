<?php

  /**
  * Smarty error
  *
  * This error is thrown when Smarty encounters some sort of error. Errors 
  * (general, core, compile and user errors) are considered fatal
  * 
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class SmartyError extends Error {
  
    /**
    * Constructor
    *
    * @param string $message
    * @param integer $level
    * @return SmartyError
    */
    function __construct($message, $level) {
      parent::__construct($message, in_array($level, array(
        E_ERROR, 
        E_CORE_ERROR, 
        E_COMPILE_ERROR, 
        E_USER_ERROR
      )));
    } // __construct
  
  } // SmartyError

?>