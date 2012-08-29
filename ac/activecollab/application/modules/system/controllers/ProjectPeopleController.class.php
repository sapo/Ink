<?php

  // Use project controller
  use_controller('project', SYSTEM_MODULE);

  /**
   * Project people controller
   * 
   * This controller implements project people and permission relateed pages and 
   * actions
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectPeopleController extends ProjectController {
    
    /**
     * Controller name
     * 
     * @var string
     */
    var $controller_name = 'project_people';
    
    /**
     * Actions available as API methods
     *
     * @var array
     */
    var $api_actions = array('index', 'add_people', 'user_permissions', 'remove_user');
    
    /**
     * Construct project_people controller
     *
     * @param Request $request
     * @return ProjectPeopleController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->wireframe->addBreadCrumb(lang('People'), $this->active_project->getPeopleUrl());
      
      if($this->active_project->canEdit($this->logged_user)) {
        $this->wireframe->addPageAction(lang('Add People'), $this->active_project->getAddPeopleUrl());
      } // if
      
      $this->smarty->assign('page_tab', 'people');
    } // __construct
    
    /**
     * Show people page
     *
     * @param void
     * @return null
     */
    function index() {
      if($this->active_project->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $users = $this->active_project->getUsers();
      
      // API
      if($this->request->isApiCall()) {
        $project_users_data = array();
        
        if(is_foreachable($users)) {
          foreach($users as $user) {
            $user_data = array(
              'user_id' => $user->getId(),
              'role' => null,
              'permissions' => array(),
              'permalink' => $user->getViewUrl(), 
            );
            
            $permissions = array_keys(Permissions::findProject());
            if($user->isAdministrator()) {
              $user_data['role'] = 'administrator';
            } elseif($user->isProjectManager()) {
              $user_data['role'] = 'project-manager';
            } elseif($user->isProjectLeader($this->active_project)) {
              $user_data['role'] = 'project-leader';
            } // if
            
            if($user_data['role'] === null) {
              $project_role = $user->getProjectRole($this->active_project);
              if(instance_of($project_role, 'Role')) {
                $user_data['role'] = $project_role->getId();
              } else {
                $user_data['role'] = 'custom';
              } // if
              
              foreach($permissions as $permission) {
                $user_data['permissions'][$permission] = (integer) $user->getProjectPermission($permission, $this->active_project);
              } // foreach
            } else {
              foreach($permissions as $permission) {
                $user_data['permissions'][$permission] = PROJECT_PERMISSION_MANAGE;
              } // foreach
            } // if
            
            $project_users_data[] = $user_data;
          } // foreach
        } // if
        
        $this->serveData($project_users_data, 'project_users');
        
      // Regular interface
      } else {
        if(is_foreachable($users)) {
          $people = array();
          $grouped_users = array();
          
          foreach($users as $user) {
            $company_id = $user->getCompanyId();
            if(!isset($people[$company_id])) {
              $people[$company_id] = array(
                'users' => null,
                'company' => null,
              );
            } // if
            $people[$company_id]['users'][] = $user;
          } // foreach
          
          $companies = Companies::findByIds(array_keys($people));
          foreach($companies as $company) {
            $people[$company->getId()]['company'] = $company;
          } // foreach
          
          $this->smarty->assign('people', $people);
        } else {
          $this->smarty->assign('people', null);
        } // if
      } // if
    } // index
    
    /**
     * Add people to the project
     *
     * @param void
     * @return null
     */
    function add_people() {
      if(!$this->active_project->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $project_users = $this->active_project->getUsers();
      if(is_foreachable($project_users)) {
        $exclude_users = objects_array_extract($project_users, 'getId');
      } else {
        $exclude_users = null;
      } // if
      
      $this->smarty->assign(array(
        'exclude_users' => $exclude_users,
      ));
      
      if($this->request->isSubmitted()) {
        $user_ids = $this->request->post('users');
        if(!is_foreachable($user_ids)) {
          flash_error('No users selected');
          $this->redirectToUrl($this->active_project->getViewUrl());
        } // if
        
        $users = Users::findByIds($user_ids);
        
        $project_permissions = $this->request->post('project_permissions');
        
        $role = null;
        $role_id = (integer) array_var($project_permissions, 'role_id');
        
        if($role_id) {
          $role = Roles::findById($role_id);
        } // if
        
        if(instance_of($role, 'Role') && $role->getType() == ROLE_TYPE_PROJECT) {
          $permissions = null;
        } else {
          $permissions = array_var($project_permissions, 'permissions');
          if(!is_array($permissions)) {
            $permissions = null;
          } // if
        } // if
        
        if(is_foreachable($users)) {
          db_begin_work();
          
          $added = array();
          foreach($users as $user) {
            $add = $this->active_project->addUser($user, $role, $permissions);
            if($add && !is_error($add)) {
              $added[] = $user->getDisplayName();
            } else {
              db_rollback();
              
              flash_error('Failed to add ":user" to ":project" project', array('user' => $user->getDisplayName(), 'project' => $this->active_project->getName()));
              $this->redirectToUrl($this->active_project->getAddPeopleUrl());
            } // if
          } // foreach
          
          db_commit();
          
          if($this->request->isApiCall()) {
            $this->httpOk();
          } else {
            require_once SMARTY_PATH . '/plugins/function.join.php';
            
            flash_success(':users added to :project project', array('users' => smarty_function_join(array('items' => $added)), 'project' => $this->active_project->getName()));
            $this->redirectToUrl($this->active_project->getPeopleUrl());
          } // if
        } // if
      } else {
        if($this->request->isApiCall()) {
          $this->httpError(HTTP_ERR_BAD_REQUEST);
        } // if
      } // if
    } // add_people
    
    /**
     * Show and process user permissions page
     *
     * @param void
     * @return null
     */
    function user_permissions() {
      if(!$this->active_project->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $user = Users::findById($this->request->getId('user_id'));
      if(!instance_of($user, 'User')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($user->isProjectManager() || $user->isProjectLeader($this->active_project)) {
        flash_error(':user has all permissions in this project', array('user' => $user->getDisplayName()));
        $this->redirectToReferer($this->active_project->getPeopleUrl());
      } // if
      
      $project_user = ProjectUsers::findById(array(
        'user_id'    => $user->getId(),
        'project_id' => $this->active_project->getId(),
      ));
      
      if(!instance_of($project_user, 'ProjectUser')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->logged_user->canChangeProjectPermissions($user, $this->active_project)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->smarty->assign(array(
        'active_user' => $user,
        'project_user' => $project_user,
      ));
      
      if($this->request->isSubmitted()) {
        $project_permissions = $this->request->post('project_permissions');
        
        $role = null;
        $role_id = (integer) array_var($project_permissions, 'role_id');
        if($role_id) {
          $role = Roles::findById($role_id);
        } // if
        
        if(instance_of($role, 'Role') && $role->getType() == ROLE_TYPE_PROJECT) {
          $permissions = null;
        } else {
          $role = null;
          $permissions = array_var($project_permissions, 'permissions');
          if(!is_array($permissions)) {
            $permissions = null;
          } // if
        } // if
        
        $update = $this->active_project->updateUserPermissions($user, $role, $permissions);
        if($update && !is_error($update)) {
          if($this->request->isApiCall()) {
            $this->httpOk();
          } else {
            flash_success('Permissions have been updated successfully');
          } // if
        } else {
          if($this->request->isApiCall()) {
            $this->serveData($update);
          } else {
            flash_error('Failed to update permissions');
          } // if
        } // if
        
        $this->redirectToUrl($this->active_project->getPeopleUrl());
      } else {
        if($this->request->isApiCall()) {
          $this->httpError(HTTP_ERR_BAD_REQUEST);
        } // if
      } // if
    } // user_permission
    
    /**
     * Remove user from this project
     *
     * @param void
     * @return null
     */
    function remove_user() {
      if($this->request->isSubmitted()) {
        $user = Users::findById($this->request->getId('user_id'));
        if(!instance_of($user, 'User')) {
          $this->httpError(HTTP_ERR_NOT_FOUND);
        } // if
        
        if(!$this->logged_user->canRemoveFromProject($user, $this->active_project)) {
          $this->httpError(HTTP_ERR_FORBIDDEN);
        } // if
        
        $remove = $this->active_project->removeUser($user);
        if($remove && !is_error($remove)) {
          if($this->request->isApiCall()) {
            $this->httpOk();
          } else {
            flash_success(':user has been removed from :project project', array('user' => $user->getDisplayName(), 'project' => $this->active_project->getName()));
            $this->redirectToReferer($this->active_project->getPeopleUrl());
          } // if
        } else {
          if($this->request->isApiCall()) {
            $this->serveData($remove);
          } else {
            flash_error('Failed to remove :user from :project project', array('user' => $user->getDisplayName(), 'project' => $this->active_project->getName()));
            $this->redirectToReferer($this->active_project->getPeopleUrl());
          } // if
        } // if
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // remove_user
    
  }

?>