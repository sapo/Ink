<?php

  /**
  * Invalid controller action error
  * 
  * @version 1.0
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class InvalidControllerActionError extends Error {
    
    /**
    * Controller name
    *
    * @var string
    */
    var $controller;
    
    /**
    * Action name
    *
    * @var string
    */
    var $action;
    
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
    * Construct the InvalidControllerActionError
    *
    * @param string $controller Controller name
    * @param string $action Controller action
    * @param string $message Error message, if NULL default will be used
    * @return InvalidControllerActionError
    */
    function __construct($controller, $action, $message = null) {
      if(is_null($message)) {
        $message = "Invalid controller action $controller::$action()";
      } // if
      
      $this->setController($controller);
      $this->setAction($action);
      
      parent::__construct($message);
    } // __construct
    
    /**
    * Return errors specific params...
    *
    * @param void
    * @return array
    */
    function getAdditionalParams() {
      return array(
        'controller' => $this->getController(),
        'action' => $this->getAction()
      ); // array
    } // getAdditionalParams
    
    // -------------------------------------------------------
    // Getters and setters
    // -------------------------------------------------------
    
    /**
    * Get controller
    *
    * @param null
    * @return string
    */
    function getController() {
      return $this->controller;
    } // getController
    
    /**
    * Set controller value
    *
    * @param string $value
    * @return null
    */
    function setController($value) {
      $this->controller = $value;
    } // setController
    
    /**
    * Get action
    *
    * @param null
    * @return string
    */
    function getAction() {
      return $this->action;
    } // getAction
    
    /**
    * Set action value
    *
    * @param string $value
    * @return null
    */
    function setAction($value) {
      $this->action = $value;
    } // setAction
  
  } // InvalidControllerActionError

?>