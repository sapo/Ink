<?php

  // Extend settings controller
  use_controller('admin', SYSTEM_MODULE);

  /**
   * Roles administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class RolesAdminController extends AdminController {
    
    /**
     * Name of this controller (underscore)
     *
     * @var string
     */
    var $controller_name = 'roles_admin';
    
    /**
     * Selected role
     *
     * @var Role
     */
    var $active_role;
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return RolesAdminController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->wireframe->addBreadCrumb(lang('Roles'), assemble_url('admin_roles'));
      
      $role_id = $this->request->getId('role_id');
      if($role_id) {
        $this->active_role = Roles::findById($role_id);
      } // if
      
      if(instance_of($this->active_role, 'Role')) {
        $this->wireframe->addBreadCrumb($this->active_role->getName(), $this->active_role->getViewUrl());
      } else {
        $this->active_role = new Role();
      } // if
      
      if($this->request->getAction() == 'index') {
        $this->wireframe->addPageAction(lang('New System Role'), assemble_url('admin_roles_add_system'));
        $this->wireframe->addPageAction(lang('New Project Role'), assemble_url('admin_roles_add_project'));
      } // if
      
      $this->smarty->assign(array(
        'active_role' => $this->active_role
      ));
    } // __construct
    
    /**
     * List all roles
     *
     * @param void
     * @return null
     */
    function index() {
      $system_roles = array();
      $project_roles = array();
      
      $all_roles = Roles::findAll();
      if(is_foreachable($all_roles)) {
        foreach($all_roles as $role) {
          if($role->getType() == ROLE_TYPE_PROJECT) {
            $project_roles[] = $role;
          } else {
            $system_roles[] = $role;
          } // if
        } // foreach
      } // if
      
      $this->smarty->assign(array(
        'system_roles' => $system_roles,
        'project_roles' => $project_roles,
        'default_role_id' => ConfigOptions::getValue('default_role'),
      ));
    } // index
    
    /**
     * View role
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_role->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $this->wireframe->addPageAction(lang('Change Permissions'), $this->active_role->getEditUrl());
      
      $this->smarty->assign('users', $this->active_role->getUsers());
    } // view
    
    /**
     * Add project role
     *
     * @param void
     * @return null
     */
    function add_project() {
    	$role_data = $this->request->post('role');
    	$this->smarty->assign(array(
    	  'role_data' => $role_data,
    	));
    	
    	if($this->request->isSubmitted()) {
    	  $permission_values = array_var($role_data, 'permissions');
    	  if(!is_array($permission_values)) {
    	    $permission_values = array();
    	  } // if
    	  
    	  $this->active_role = new Role();
    	  $this->active_role->setName(array_var($role_data, 'name'));
    	  $this->active_role->setType(ROLE_TYPE_PROJECT);
    	  $this->active_role->setPermissions($permission_values);
    	  
    	  $save = $this->active_role->save();
    	  if($save && !is_error($save)) {
    	    flash_success("Project role ':name' has been created", array('name' => $this->active_role->getName()));
    	    $this->redirectTo('admin_roles');
    	  } else {
    	    $this->smarty->assign('errors', $save);
    	  }
    	} // if
    } // add_project
    
    /**
     * Add system role
     *
     * @param void
     * @return null
     */
    function add_system() {
    	$permissions = Permissions::findSystem();
      
    	$role_data = $this->request->post('role');
    	$this->smarty->assign(array(
    	  'role_data' => $role_data,
    	  'permissions' => $permissions,
    	));
    	
    	if($this->request->isSubmitted()) {
    	  $permission_values = array_var($role_data, 'permissions');
    	  if(!is_array($permission_values)) {
    	    $permission_values = array();
    	  } // if
    	  
    	  $this->active_role = new Role();
    	  $this->active_role->setName(array_var($role_data, 'name'));
    	  $this->active_role->setType(ROLE_TYPE_SYSTEM);
    	  $this->active_role->setPermissions($permission_values);
    	  
    	  $save = $this->active_role->save();
    	  if($save && !is_error($save)) {
    	    flash_success("System role ':name' has been created", array('name' => $this->active_role->getName()));
    	    $this->redirectTo('admin_roles');
    	  } else {
    	    $this->smarty->assign('errors', $save);
    	  }
    	} // if
    } // add_system
    
    /**
     * Update role
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->active_role->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->active_role->getType() == ROLE_TYPE_PROJECT) {
        $permissions = array_keys(Permissions::findProject());
      } else {
        $permissions = Permissions::findSystem();
      } // if
      
      $role_data = $this->request->post('role');
      if(!is_array($role_data)) {
        $role_data = array(
          'name' => $this->active_role->getName(),
          'permissions' => $this->active_role->getPermissions(),
        );
      } // if
      
      // if it's admin role and if the user editing it is the only administrator in the system
      // we need to protect removing system or admin access from that role
      $protect_admin_role = $this->logged_user->isOnlyAdministrator() && ($this->logged_user->getRoleId() == $this->active_role->getId());
      
      $this->smarty->assign(array(
        'role_data' => $role_data,
        'permissions' => $permissions,
        'protect_admin_role' => $protect_admin_role
      ));
      
      if($this->request->isSubmitted()) {
        $old_name = $this->active_role->getName();
        
        $permission_values = array_var($role_data, 'permissions');
    	  if(!is_array($permission_values)) {
    	    $permission_values = array();
    	  } // if
    	  
    	  if ($protect_admin_role) { // in case that someone removes "disabled" attribute, use brute force!
    	    $permission_values['admin_access'] = 1;
    	    $permission_values['system_access'] = 1;
    	  } // if
        
        $this->active_role->setName(array_var($role_data, 'name'));
        $this->active_role->setPermissions($permission_values);
        
        $save = $this->active_role->save();
        if($save && !is_error($save)) {
          clean_permissions_cache();
          
          flash_success("Role ':name' has been updated", array('name' => $old_name));
          $this->redirectTo('admin_roles');
        } else {
          $this->smarty->assign('errors', $save);
        } // if
      } // if
    } // edit
    
    /**
     * Drop role
     *
     * @param void
     * @return null
     */
    function delete() {
      if($this->active_role->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->request->isSubmitted()) {
        $delete = $this->active_role->delete();
        
        if($delete && !is_error($delete)) {
          clean_permissions_cache();
          
          flash_success("Role ':name' has been deleted", array('name' => $this->active_role->getName()));
        } else {
          flash_error("Failed to delete role ':name'", array('name' => $this->active_role->getName()));
        } // if
        $this->redirectTo('admin_roles');
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // delete
    
    /**
     * Set specific role as default role
     *
     * @param void
     * @return null
     */
    function set_as_default() {
    	if($this->request->isSubmitted()) {
    	  if($this->active_role->isNew()) {
    	    $this->httpError(HTTP_ERR_NOT_FOUND);
    	  } // if
    	  
    	  if($this->active_role->getType() == ROLE_TYPE_PROJECT) {
    	    $this->httpError(HTTP_ERR_INVALID_PROPERTIES);
    	  } // if
    	  
    	  ConfigOptions::setValue('default_role', $this->active_role->getId());
    	  
    	  flash_success(':name role has been set as default', array('name' => $this->active_role->getName()));
    	  $this->redirectTo('admin_roles');
    	} else {
    	  $this->httpError(HTTP_ERR_BAD_REQUEST);
    	} // if
    } // set_as_default
    
    /**
     * Set permission value
     *
     * @param void
     * @return null
     */
    function set_permission_value() {
      if($this->active_role->isNew()) {
  	    $this->httpError(HTTP_ERR_NOT_FOUND);
  	  } // if
  	  
  	  if($this->active_role->getType() == ROLE_TYPE_PROJECT) {
  	    $this->httpError(HTTP_ERR_INVALID_PROPERTIES);
  	  } // if
      
      if($this->request->isSubmitted() && $this->request->isAsyncCall()) {
        $permission_name = $this->request->get('permission_name');
        if($permission_name) {
          $this->active_role->setPermissionValue($permission_name, $this->request->post('value'));
          $save = $this->active_role->save();
          if($save && !is_error($save)) {
            $this->httpOk();
          } // if
        } // if
        
        $this->httpError(HTTP_ERR_OPERATION_FAILED);
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // set_permission_value
  
  }

?>