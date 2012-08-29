<?php

  /**
   * Base controller class
   * 
   * This class is inherited by all script controllers. All methods of this class 
   * are reserved - there can't be actions with that names (for instance, there 
   * can't be execute actions in real controllers).
   *
   * @package angie.library.controller
   */
  class Controller extends AngieObject {
    
    /**
     * Name of this controller (underscore)
     *
     * @var string
     */
    var $controller_name;
    
    /**
     * Action that was (or need to be) executed
     *
     * @var string
     */
    var $action;
    
    /**
     * System controller class
     * 
     * System controller class is class withch methods are reserved (can't be 
     * called). Basic system controllers are Controller and PageController 
     * classes
     *
     * @var string
     */
    var $system_controller_class;
    
    /**
     * Time when controller is constructed
     *
     * @var DateTimeValue
     */
    var $request_time;
    
    /**
     * Contruct controller and set controller name
     *
     * @param void
     * @return null
     */
    function __construct() {
      parent::__construct();
      
      // Allow programmer to specify controller name based. PHP4 will always 
      // return lowercased class name so we need an option to set our own value
      if($this->controller_name == '') {
        $this->setControllerName(get_controller_name(get_class($this)));
      } // if
      
      $this->setSystemControllerClass('Controller');
      $this->request_time = new DateTimeValue();
    } // __construct
    
    /**
     * Execute specific controller action
     *
     * @param string $action
     * @return InvalidControllerActionError if action name is not valid or true
     */
    function execute($action) {
      $action = trim(strtolower($action));
      
      $valid = $this->validAction($action);
      if(is_error($valid)) {
        return $valid;
      } // if
      
      if($valid) {
        $this->setAction($action);
        $this->$action();
        return true;
      } else {
        use_error('InvalidControllerActionError');
        return new InvalidControllerActionError($this->getControllerName(), $action);
      } // if
    } // execute
    
    /**
     * Check if specific $action is valid controller action (method exists and it is not reserved)
     *
     * @param string $action
     * @return boolean or Error
     */
    function validAction($action) {
      $reserved_names = Controller::getReservedActionNames();
      if(is_error($reserved_names)) {
        return $reserved_names;
      } // if
      if(is_array($reserved_names) && in_array($action, $reserved_names)) {
        return false;
      } // if
      
      $methods = get_class_methods(get_class($this));
      if(!in_array($action, $methods)) {
        return false;
      } // if
      
      return true;
    } // validAction
    
    // -------------------------------------------------------
    // Getters and setters
    // -------------------------------------------------------
    
    /**
     * Get controller_name
     *
     * @param null
     * @return string
     */
    function getControllerName() {
      return $this->controller_name;
    } // getControllerName
    
    /**
     * Set controller_name value
     *
     * @param string $value
     * @return null
     */
    function setControllerName($value) {
      $this->controller_name = $value;
    } // setControllerName
    
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
    
    /**
     * Get system_controller_class
     *
     * @param null
     * @return string
     */
    function getSystemControllerClass() {
      return $this->system_controller_class;
    } // getSystemControllerClass
    
    /**
     * Set system_controller_class value
     *
     * @param string $value
     * @return null
     */
    function setSystemControllerClass($value) {
      $this->system_controller_class = $value;
    } // setSystemControllerClass
    
    /**
     * Return reserved action names (methods of controller class)
     *
     * @param void
     * @return arrays
     */
    function getReservedActionNames() {
      static $names;
      
      $controller_class = $this->getSystemControllerClass();
      if(!class_exists($controller_class)) {
        return new Error("Controller class '$controller_class' does not exists");
      } // if
      
      if(is_null($names)) {
        $names = get_class_methods($controller_class);
        foreach($names as $k => $v) {
          $names[$k] = strtolower($v);
        } // foreach
      } // if
      
      return $names;
    } // getReservedActionNames
  
  } // Controller

?>