<?php

  /**
   * API specific calls controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ApiController extends ApplicationController {
    
    /**
     * Name of this controller
     *
     * @var string
     */
    var $controller_name = 'api';
    
    /**
     * Actions that are available through API
     *
     * @var array
     */
    var $api_actions = array('info', 'system_roles', 'project_roles', 'role');
    
    /**
     * Construct API controller
     *
     * @param Request $request
     * @return ApiController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if(!$this->request->isApiCall()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // __construct
    
    /**
     * Show application info (available only through API)
     *
     * @param void
     * @return null
     */
    function info() {
      $this->serveData(array(
        'api_version'    => $this->application->api_version,
        'system_version' => $this->application->version,
        'logged_user'    => $this->logged_user->getViewUrl(),
        'read_only'      => (integer) (API_STATUS <= API_READ_ONLY),
      ), 'info');
    } // info
    
    /**
     * List all available system roles
     *
     * @param void
     * @return null
     */
    function system_roles() {
      $default_role_id = ConfigOptions::getValue('default_role');
      if($this->logged_user->isAdministrator() || $this->logged_user->isProjectManager()) {
        $roles_data = array();
        $system_permissions = Permissions::findSystem();
        
        $roles = Roles::findSystemRoles();
        if(is_foreachable($roles)) {
          foreach($roles as $role) {
            $role_details = array(
              'id' => $role->getId(),
              'name' => $role->getName(),
              'is_default' => $role->getId() == $default_role_id,
              'permissions' => array(),
            );
            
            foreach($system_permissions as $permission) {
              $role_details['permissions'][$permission] = (boolean) $role->getPermissionValue($permission, false);
            } // foreach
            
            $roles_data[] = $role_details;
          } // foreach
        } // if
        
        $this->serveData($roles_data, 'system_roles');
      } else {
        $this->serveData($default_role_id, 'default_role_id');
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
    } // system_roles
    
    /**
     * Show all available project roles
     *
     * @param void
     * @return null
     */
    function project_roles() {
      $roles_data = array();
      $project_permissions = array_keys(Permissions::findProject());
      
      $roles = Roles::findProjectRoles();
      if(is_foreachable($roles)) {
        foreach($roles as $role) {
          $role_details = array(
            'id' => $role->getId(),
            'name' => $role->getName(),
            'permissions' => array(),
          );
          
          foreach($project_permissions as $permission) {
            $role_details['permissions'][$permission] = (integer) $role->getPermissionValue($permission, 0);
          } // foreach
          
          $roles_data[] = $role_details;
        } // foreach
      } // if
      
      $this->serveData($roles_data, 'project_roles');
    } // project_roles
    
    /**
     * Show role details
     *
     * @param void
     * @return null
     */
    function role() {
      $role_id = $this->request->getId('role_id');
      if($role_id) {
        $role = Roles::findById($role_id);
        if(instance_of($role, 'Role')) {
          if($role->getType() == ROLE_TYPE_SYSTEM) {
            $default_role_id = ConfigOptions::getValue('default_role');
            
            $serve_as = 'system_role';
            $role_data = array(
              'id' => $role->getId(),
              'name' => $role->getName(),
              'is_default' => $role->getId() == $default_role_id,
              'permissions' => array(),
            );
            
            $system_permissions = Permissions::findSystem();
            foreach($system_permissions as $permission) {
              $role_data['permissions'][$permission] = (boolean) $role->getPermissionValue($permission, false);
            } // foreach
          } else {
            $serve_as = 'project_role';
            $role_data = array(
              'id' => $role->getId(),
              'name' => $role->getName(),
              'permissions' => array(),
            );
            
            foreach(array_keys(Permissions::findProject()) as $permission) {
              $role_data['permissions'][$permission] = (integer) $role->getPermissionValue($permission, 0);
            } // foreach
          } // if
          
          $this->serveData($role_data, $serve_as);
        } // if
      } // if
      $this->httpError(HTTP_ERR_NOT_FOUND);
    } // role
    
  }

?>