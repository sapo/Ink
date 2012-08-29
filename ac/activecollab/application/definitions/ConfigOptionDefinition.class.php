<?php

  /**
   * Config option definition
   */
  class ConfigOptionDefinition {
  
    /**
     * Permission name
     *
     * @var string
     */
    var $name;
    
    /**
     * Module that defined this permission
     *
     * @var string
     */
    var $module;
    
    /**
     * Config option type, can be system, project, user, company
     *
     * @var string
     */
    var $type = 'system';
    
    /**
     * Value
     *
     * @var mixed
     */
    var $value;
    
    /**
     * Constructor
     *
     * @param string $name
     * @param string $module
     * @param string $type
     * @param string $value
     * @param string $default_value
     * @return ConfigOptionDefinition
     */
    function __construct($name, $module, $type = 'system', $value = '') {
      $this->name = $name;
      $this->module = $module;
      $this->type = $type;
      $this->value = $value;
    } // __construct
  
  } // ConfigOptionDefinition

?>