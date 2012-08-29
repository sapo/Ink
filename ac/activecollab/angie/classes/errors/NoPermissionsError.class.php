<?php

  /**
   * No permissions error
   *
   * Thrown when user tryes to perform an accion that he or she does not have 
   * permissions to perform
   * 
   * @author Ilija Studen <ilija.studen@gmail.com>
   */
  class NoPermissionsError extends Error {
  
    /**
     * Constructor
     *
     * @param string $message
     * @return NoPermissionsError
     */
    function __construct($message = null) {
      if($message === null) {
        $message = 'You don\'t have permissions to access this page / execute this action';
      } // if
      
      parent::__construct($message, false);
    } // __construct
  
  } // NoPermissionsError

?>