<?php

  /**
   * ProjectUsers class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectUsers extends BaseProjectUsers {
    
    /**
     * Return type filter for $user in all active projects based on user's 
     * permissions
     * 
     * If $only_types is present system will include only types listed in that 
     * array and ignore other no matter the permissions
     *
     * @param User $user
     * @param array $project_statuses
     * @param array $only_types
     * @param boolean $use_cache
     * @return string
     */
    function getVisibleTypesFilter($user, $project_statuses = null, $only_types = null, $use_cache = true) {
      $cache_id = 'visible_types_filter_for_' . $user->getId();
      
      if($project_statuses === null) {
        $project_statuses = array(PROJECT_STATUS_ACTIVE);
      } elseif(!is_array($project_statuses)) {
        $project_statuses = array($project_statuses);
      } // if
      
      $statuses_cache_key = implode('-', $project_statuses);
      
      if($only_types) {
        $types_cache_key = implode('-', $only_types);
      } else {
        $types_cache_key = 'all_types';
      } // if
      
      // Get and prepare cached value
      $cached_value = cache_get($cache_id);
      
      if(is_array($cached_value)) {
        if(!isset($cached_value[$statuses_cache_key])) {
          $cached_value[$statuses_cache_key] = array();
        } // if
      } else {
        $cached_value = array(
          $statuses_cache_key => array(),
        );
      } // if
      
      if($use_cache && isset($cached_value[$statuses_cache_key]) && isset($cached_value[$statuses_cache_key][$types_cache_key])) {
        return $cached_value[$statuses_cache_key][$types_cache_key];
      } // if
      
      $projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      $rows = db_execute_all("SELECT $project_users_table.project_id, $project_users_table.role_id, $project_users_table.permissions, $projects_table.leader_id FROM $project_users_table, $projects_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.status IN (?)", $user->getId(), $project_statuses);
      if(is_foreachable($rows)) {
        $escaped_only_types = $only_types !== null ? db_escape($only_types) : '';
        
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        
        // If we have administrators or project managers lets skip all the dirty 
        // work with roles and permissions
        if($user->isAdministrator() || $user->isProjectManager()) {
          $result = array();
          foreach($rows as $row) {
            $project_id = (integer) $row['project_id'];
            
            if($only_types === null) {
              $result[] = "($project_objects_table.project_id = $project_id)";
            } else {
              $result[] = "($project_objects_table.project_id = $project_id AND type IN ($escaped_only_types))";
            } // if
          } // if
          
          $cached_value[$statuses_cache_key][$types_cache_key] = '(' . implode(' OR ', $result) . ')';
          cache_set($cache_id, $cached_value);
          
          return $cached_value[$statuses_cache_key][$types_cache_key];
        } // if
        
        // Load roles data
        $role_ids = array();
        foreach($rows as $row) {
          $role_id = (integer) $row['role_id'];
          if($role_id && !in_array($role_id, $role_ids)) {
            $role_ids[] = $role_id;
          } // if
        } // foreach
        
        if(is_foreachable($role_ids)) {
          $roles = Roles::findIndexedByIds($role_ids);
        } else {
          $roles = array();
        } // if
        
        $result = array();
        foreach($rows as $row) {
          
          // We have a project leader
          if($user->getId() == $row['leader_id']) {
            $project_id = (integer) $row['project_id'];
            
            if($only_types === null) {
              $result[] = "($project_objects_table.project_id = $project_id)";
            } else {
              $result[] = "($project_objects_table.project_id = $project_id AND type IN ($escaped_only_types))";
            } // if
            
          // Regular user
          } else {
            
            // Role or custom permissions
            if($row['role_id']) {
              $role = array_var($roles, $row['role_id']);
              if(instance_of($role, 'Role')) {
                $permissions = $role->getPermissions();
              } else {
                $permissions = array();
              } // if
            } else {
              $permissions = $row['permissions'] ? unserialize($row['permissions']) : array();
            } // if
            
            // Get types and prepare result parts
            if(!empty($permissions)) {
              $types = array();
              foreach($permissions as $permission_name => $permission_value) {
                if($permission_value >= PROJECT_PERMISSION_ACCESS) {
                  if($only_types !== null && !in_array($permission_name, $only_types)) {
                    continue;
                  } // if
                  
                  $types[] = $permission_name;
                } // if
              } // foreach
              
              if(!empty($types)) {
                $project_id = (integer) $row['project_id'];
          	    $escaped_types = db_escape($types);
          	    
          	    $result[] = "($project_objects_table.project_id = '$project_id' AND ($project_objects_table.type IN ($escaped_types) OR ($project_objects_table.type IN ('Attachment', 'Task', 'Comment') AND $project_objects_table.parent_type IN ($escaped_types))))";
              } // if
            } // if
          } // if
        } // foreach
        
        $cached_value[$statuses_cache_key][$types_cache_key] = empty($result) ? '' : '(' . implode(' OR ', $result) . ')';
        cache_set($cache_id, $cached_value);
        
        return $cached_value[$statuses_cache_key][$types_cache_key];
      } // if
      
      $cached_value[$statuses_cache_key][$types_cache_key] = '';
      cache_set($cache_id, $cached_value);
      
      return '';
    } // getVisbleTypesFilter
    
    /**
     * Return visible types filter by project
     *
     * @param User $user
     * @param Project $project
     * @param array $only_types
     * @param boolean $use_cache
     * @return string
     */
    function getVisibleTypesFilterByProject($user, $project, $only_types = null, $use_cache = true) {
      $project_id = $project->getId();
      $cache_id = 'visible_project_types_filter_for_' . $user->getId();
      
      if($only_types) {
        $cache_key = implode('-', $only_types);
      } else {
        $cache_key = 'all_types';
      } // if
      
      // Get and prepare cached value
      $cached_value = cache_get($cache_id);
      if(is_array($cached_value)) {
        if(!isset($cached_value[$project_id])) {
          $cached_value[$project_id] = array();
        } // if
      } else {
        $cached_value = array(
          $project->getId() => array()
        );
      } // if
      
      // From cache?
      if($use_cache && isset($cached_value[$cache_key])) {
        return $cached_value[$cache_key];
      } // if
      
      // Nope, load...
      $project_objects_table = TABLE_PREFIX . 'project_objects';
    
      if($only_types !== null) {
        $escaped_only_types = db_escape($only_types);
      } // if
      
      if($user->isAdministrator() || $user->isProjectManager() || $user->isProjectLeader($project)) {
        if($only_types === null) {
          $filter = "($project_objects_table.project_id = $project_id)";
        } else {
          $filter = "($project_objects_table.project_id = $project_id AND type IN ($escaped_only_types))";
        } // if
        
        // Add to cache and return
        $cached_value[$project_id][$cache_key] = $filter;
        cache_set($cache_id, $cached_value);
        
        return $filter;
      } // if
      
    	$types = ProjectUsers::getVisibleTypesByProject($user, $project);
    	if($only_types !== null) {
      	foreach($types as $k => $v) {
      	  if(!in_array($v, $only_types)) {
      	    unset($types[$k]);
      	  } // if
      	} // foreach
    	} // if
    	
    	if(is_foreachable($types)) {
    	  $project_id = $project->getId();
        $escaped_types = db_escape($types);
        
        $filter = "($project_objects_table.project_id = '$project_id' AND ($project_objects_table.type IN ($escaped_types) OR ($project_objects_table.type IN ('Attachment', 'Task', 'Comment') AND $project_objects_table.parent_type IN ($escaped_types))))";
        
        $cached_value[$project_id][$cache_key] = $filter;
        cache_set($cache_id, $cached_value);
    	  
    	  return $filter;
    	} else {
    	  $cached_value[$project_id][$cache_key] = '';
        cache_set($cache_id, $cached_value);
    	  
    	  return '';
    	} // if
    } // getVisibleTypesFilterByProject
    
    /**
     * Return top level types user can see in $project
     *
     * @param User $user
     * @param Project $project
     * @param boolean $use_cache
     * @return array
     */
    function getVisibleTypesByProject($user, $project, $use_cache = true) {
      $project_id = $project->getId();
      $cache_id = 'visible_project_types_for_' . $user->getId();
      
      $cached_value = cache_get($cache_id);
      if(!is_array($cached_value)) {
        $cached_value = array();
      } // if
      
      if($use_cache && isset($cached_value[$project_id])) {
        return $cached_value[$project_id];
      } // if
      
      if($user->isAdministrator() || $user->isProjectManager() || $user->isProjectLeader($project)) {
        $cached_value[$project_id] = array_keys(Permissions::findProject());
        cache_set($cache_id, $cached_value);
        
        return $cached_value[$project_id];
      } // if
      
      $project_user = ProjectUsers::findById(array(
        'user_id' => $user->getId(),
        'project_id' => $project->getId(),
      ));
      
      if(instance_of($project_user, 'ProjectUser')) {
        $role = $project_user->getRole();
        if(instance_of($role, 'Role')) {
          $permissions = $role->getPermissions();
        } else {
          $permissions = $project_user->getPermissions();
        } // if
        
        if(is_array($permissions)) {
          $types = array();
          foreach($permissions as $permission_name => $permission_value) {
            if($permission_value >= PROJECT_PERMISSION_ACCESS) {
              $types[] = $permission_name;
            } // if
          } // foreach
          
          $cached_value[$project_id] = $types;
          cache_set($cache_id, $cached_value);
          
          return $cached_value[$project_id];
        } // if
      } // if
      
      $cached_value[$project_id] = array();
      cache_set($cache_id, $cached_value);
      
      return array();
    } // getVisibleTypesByProject
    
    /**
     * Return project roles map for a given user
     * 
     * Returns associative array where key is project ID and value is array of 
     * permissions user has in a given project. System permissions like 
     * administrator, project manager etc are ignored
     *
     * @param User $user
     * @param array $statuses
     * @return array
     */
    function getProjectRolesMap($user, $statuses = null) {
      $project_users_table = TABLE_PREFIX . 'project_users';
      $projects_table = TABLE_PREFIX . 'projects';
      
      if($statuses === null) {
      	$rows = db_execute_all("SELECT $project_users_table.*, $projects_table.name AS 'project_name', $projects_table.leader_id AS 'project_leader' FROM $projects_table, $project_users_table WHERE $projects_table.id = $project_users_table.project_id AND $projects_table.type = ? AND $project_users_table.user_id = ? ORDER BY $projects_table.name", PROJECT_TYPE_NORMAL, $user->getId());
      } else {
        $rows = db_execute_all("SELECT $project_users_table.*, $projects_table.name AS 'project_name', $projects_table.leader_id AS 'project_leader' FROM $projects_table, $project_users_table WHERE $projects_table.id = $project_users_table.project_id AND $projects_table.type = ? AND $projects_table.status IN (?) AND $project_users_table.user_id = ? ORDER BY $projects_table.name", PROJECT_TYPE_NORMAL,  $statuses, $user->getId());
      } // if
      
    	if(is_foreachable($rows)) {
    	  $result = array();
    	  $roles = array();
    	  
    	  foreach($rows as $row) {
    	    $project_id = (integer) $row['project_id'];
    	    $role_id = (integer) $row['role_id'];
    	    
    	    // From role
    	    if($role_id) {
    	      if(!isset($roles[$role_id])) {
    	        $role_row = db_execute_one('SELECT permissions FROM ' . TABLE_PREFIX . 'roles WHERE id = ?', $role_id);
    	        if($role_row && isset($role_row['permissions'])) {
    	          $roles[$role_id] = $role_row['permissions'] ? unserialize($role_row['permissions']) : array();
    	        } else {
    	          $roles[$role_id] = array();
    	        } // if
    	      } // if
    	      $result[$project_id] = array(
    	        'name' => $row['project_name'],
    	        'leader' => $row['project_leader'],
    	        'permissions' => $roles[$role_id],
    	      );
    	      
    	    // From permissions
    	    } else {
    	      $result[$project_id] = array(
    	        'name' => $row['project_name'],
    	        'leader' => $row['project_leader'],
    	        'permissions' => $row['permissions'] ? unserialize($row['permissions']) : array()
    	      );
    	    } // if
    	  } // foreach
    	  
    	  return $result;
    	} // if
    	return null;
    } // getProjectRolesMap
    
    /**
     * Returns true if $user is member of $project
     *
     * @param User $user
     * @param Project $project
     * @param boolean $use_cache
     * @return boolean
     */
    function isProjectMember($user, $project, $use_cache = true) {
      static $cache = array();
      
    	$user_id = $user->getId();
    	$project_id = $project->getId();
    	
    	if($use_cache && isset($cache[$project_id]) && isset($cache[$project_id][$user_id])) {
    	  return $cache[$project_id][$user_id];
    	} // if
    	
    	if(!isset($cache[$project_id])) {
    	  $cache[$project_id] = array();
    	} // if
    	
    	if(!isset($cache[$project_id][$user_id])) {
    	  $cache[$project_id][$user_id] = array();
    	} // if
    	
    	if($user->isAdministrator() || $user->isProjectManager()) {
    	  $cache[$project_id][$user_id] = true;
    	} else {
    	  $cache[$project_id][$user_id] = (boolean) ProjectUsers::count(array('user_id = ? AND project_id = ?', $user_id, $project_id));
    	} // if
    	
    	return $cache[$project_id][$user_id];
    } // isProjectMember
    
    /**
     * Return project users by project
     *
     * @param Project $project
     * @return array
     */
    function findByProject($project) {
    	return ProjectUsers::find(array(
    	  'conditions' => array('project_id = ?', $project->getId()),
    	));
    } // findByProject
  
    /**
     * Return all users that are assgined to this project
     *
     * @param Project $project
     * @return array
     */
    function findUsersByProject($project) {
      $users_table = TABLE_PREFIX . 'users';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      return Users::findBySQL("SELECT $users_table.* FROM $users_table, $project_users_table WHERE $project_users_table.project_id = ? AND $project_users_table.user_id = $users_table.id", array($project->getId()));
    } // findUsersByProject
    
    /**
     * Return ID-s of users assigned to a project
     *
     * @param Project $project
     * @return array
     */
    function findUserIdsByProject($project) {
      $result = array();
      
      $rows = db_execute_all("SELECT user_id FROM " . TABLE_PREFIX . "project_users WHERE project_id = ?", $project->getId());
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $result[] = (integer) $row['user_id'];
        } // foreach
      } // if
      
      return $result;
    } // findUserIdsByProject
    
    /**
     * Return members of given company that are part of a specific project
     *
     * @param Project $project
     * @param Company $company
     * @return array
     */
    function findUsersByProjectAndCompany($project, $company) {
      $users_table = TABLE_PREFIX . 'users';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      return Users::findBySQL("SELECT $users_table.* FROM $users_table, $project_users_table WHERE $users_table.company_id = ? AND $project_users_table.project_id = ? AND $project_users_table.user_id = $users_table.id", array($company->getId(), $project->getId()));
    } // findUsersByProjectAndCompany
    
    /**
     * Return users by role, groupped by project
     *
     * @param Role $role
     * @return array
     */
    function findByRole($role) {
      $project_users_table = TABLE_PREFIX . 'project_users';
      $projects_table = TABLE_PREFIX . 'projects';
      
    	$rows = db_execute_all("SELECT DISTINCT $project_users_table.project_id, $project_users_table.user_id FROM $project_users_table, $projects_table WHERE role_id = ? ORDER BY $projects_table.created_on", $role->getId());
    	if(is_foreachable($rows)) {
    	  $result = array();
    	  
    	  foreach($rows as $row) {
    	    $project_id = (integer) $row['project_id'];
    	    $user_id = (integer) $row['user_id'];
    	    
    	    if(!isset($result[$project_id])) {
    	      $project = Projects::findById($project_id);
    	      if(!instance_of($project, 'Project')) {
    	        continue;
    	      } // if
    	      
    	      $result[$project_id] = array(
    	        'project' => $project,
    	        'users' => array(),
    	      );
    	    } // if
    	    
    	    $user = Users::findById($user_id);
    	    if(instance_of($user, 'User')) {
    	      $result[$project_id]['users'][] = $user;
    	    } // if
    	  } // foreach
    	  
    	  return $result;
    	} // if
    	return null;
    } // findByRole
    
    /**
     * Return number of users using a specific role
     *
     * @param Role $role
     * @return integer
     */
    function countByRole($role) {
    	return ProjectUsers::count(array('role_id = ?', $role->getId()));
    } // countByRole
    
    /**
     * Delete permissions by project
     *
     * @param Project $project
     * @return boolean
     */
    function deleteByProject($project) {
      return ProjectUsers::delete(array('project_id = ?', $project->getId()));
    } // deleteByProject
    
    /**
     * Delete relations by $user
     *
     * @param User $user
     * @return boolean
     */
    function deleteByUser($user) {
      return ProjectUsers::delete(array('user_id = ?', $user->getId()));
    } // deleteByUser
  
  }

?>