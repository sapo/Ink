<?php

  /**
   * Role definition class
   */
  class RoleDefinition {
    
    /**
     * Role name
     *
     * @var string
     */
    var $name;
    
    /**
     * Permissions for this role (associative array)
     *
     * @var array
     */
    var $permissions;
  
    /**
     * Constructor
     *
     * @param string $name
     * @param array $permissions
     * @return RoleDefinition
     */
    function __construct($name, $permissions = null) {
      $this->name = $name;
      $this->permissions = $permissions;
    } // __construct
  
  } // RoleDefinition

?>