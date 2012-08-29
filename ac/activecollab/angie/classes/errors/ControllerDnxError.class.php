<?php

  /**
   * Controller does not exists error, thrown when controller is missing
   */
  class ControllerDnxError extends Error {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller;
    
    /**
     * Fatal error
     * 
     * On fatal error script stops execution and handle_fatal_error hook is 
     * called
     *
     * @var boolean
     */
    var $is_fatal = true;
  
    /**
     * Construct the ControllerDnxError
     *
     * @access public
     * @param void
     * @return ControllerDnxError
     */
    function __construct($controller, $message = null) {
      if(is_null($message)) {
        $message = "Controller '$controller' is missing";
      } // if
      
      $this->setController($controller);
      
      parent::__construct($message, true);
    } // __construct
    
    /**
     * Return errors specific params...
     *
     * @access public
     * @param void
     * @return array
     */
    function getAdditionalParams() {
      return array(
        'controller' => $this->getController()
      ); // array
    } // getAdditionalParams
    
    /**
     * Get controller
     *
     * @access public
     * @param null
     * @return string
     */
    function getController() {
      return $this->controller;
    } // getController
    
    /**
     * Set controller value
     *
     * @access public
     * @param string $value
     * @return null
     */
    function setController($value) {
      $this->controller = $value;
    } // setController
  
  }

?>