<?php

  /**
   * BaseUsers class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Users extends BaseUsers {
    
    /**
     * Return all users
     *
     * @param void
     * @return null
     */
    function findAll() {
      return Users::find(array(
        'order' => 'company_id',
      ));
    } // findAll
    
    /**
     * Return users by ID-s
     *
     * @param array $ids
     * @return array
     */
    function findByIds($ids) {
      return Users::find(array(
        'conditions' => array('id IN (?)', $ids),
        'order' => 'CONCAT(first_name, last_name, email)',
      ));
    } // findByIds
    
    /**
     * Return users by company
     *
     * @param Company $company
     * @return array
     */
    function findByCompany($company) {
      return Users::find(array(
        'conditions' => array('company_id = ?', $company->getId()),
        'order' => 'CONCAT(first_name, last_name, email)',
      ));
    } // findByCompany
    
    /**
     * Return user ID-s by company
     *
     * @param Company $company
     * @return array
     */
    function findUserIdsByCompany($company) {
    	$rows = db_execute_all('SELECT id FROM ' . TABLE_PREFIX . 'users WHERE company_id = ? ORDER BY CONCAT(first_name, last_name, email)', $company->getId());
    	if(is_foreachable($rows)) {
    	  $result = array();
    	  foreach($rows as $row) {
    	    $result[] = (integer) $row['id'];
    	  } // foreach
    	  return $result;
    	} // if
    	return null;
    } // findUserIdsByCompany
    
    /**
     * Return users that match a specific company and ID-s list
     *
     * @param Company $company
     * @param array $ids
     * @return array
     */
    function findByCompanyAndIds($company, $ids) {
      return Users::find(array(
        'conditions' => array('company_id = ? AND id IN (?)', $company->getId(), $ids),
        'order' => 'CONCAT(first_name, last_name, email)',
      ));
    } // findByCompanyAndIds
    
    /**
     * Use array of users and organize them by category
     *
     * @param array $users
     * @return array
     */
    function groupByCompany($users) {
      $result = array();
      
      if(is_foreachable($users)) {
        $company_ids = array_unique(objects_array_extract($users, 'getCompanyId'));
        if(is_foreachable($company_ids)) {
          $companies = Companies::findByIds($company_ids);
          foreach($companies as $company) {
            $result[$company->getId()] = array(
              'company' => $company,
              'users' => array(),
            );
          } // foreach
          
          foreach($users as $user) {
            $result[$user->getCompanyId()]['users'][] = $user;
          } // foreach
        } // if
      } // if
      
      return $result;
    } // groupByCompany
  
    /**
     * Return user by email address
     *
     * @param string $email
     * @return User
     */
    function findByEmail($email) {
      return Users::find(array(
        'conditions' => 'email = ' . db_escape($email),
        'one' => true,
      ));
    } // findByEmail
    
    /**
     * Get user by session ID
     *
     * @param string $session_id
     * @param string $session_key
     * @return User
     */
    function findBySessionId($session_id, $session_key) {
      $users_table = TABLE_PREFIX . 'users';
      $user_sessions_table = TABLE_PREFIX . 'user_sessions';
      
      return Users::findBySQL("SELECT $users_table.* FROM $users_table, $user_sessions_table WHERE $users_table.id = $user_sessions_table.user_id AND $user_sessions_table.id = ? AND $user_sessions_table.session_key = ?", array($session_id, $session_key), true);
    } // findBySessionId
    
    /**
     * Return user by token
     *
     * @param string $token
     * @return User
     */
    function findByToken($token) {
      return Users::find(array(
        'conditions' => 'token = ' . db_escape($token),
        'one' => true,
      ));
    } // findByToken
    
    /**
     * Return users by role
     *
     * @param Role $role
     * @return array
     */
    function findByRole($role) {
      return Users::find(array(
        'conditions' => array('role_id = ?', $role->getId()),
        'order' => 'CONCAT(first_name, last_name, email)',
      ));
    } // findByRole
    
    /**
     * Return number of users with a given role
     *
     * @param Role $role
     * @return integer
     */
    function countByRole($role) {
      return Users::count(array('role_id = ?', $role->getId()));
    } // countByRole
    
    /**
     * Return number of administrators
     *
     * @param void
     * @return integer
     */
    function countAdministrators() {
      $administrators_count = 0;

      $system_roles = Roles::findSystemRoles();
      foreach($system_roles as $system_role) {
        if($system_role->isAdministrator()) {
          $administrators_count += $system_role->getUsersCount();;
        } // if
      } // foreach
      
      return $administrators_count;
    } // countAdministrators
    
    /**
     * Return array of users with autoassign permissions
     *
     * @param void
     * @return array
     */
    function findAutoAssignUsers() {
      return Users::find(array(
        'conditions' => array('auto_assign = ?', true),
        'order' => 'CONCAT(first_name, last_name, email)',
      ));
    } // findAutoAssignUsers
    
    /**
     * Return ID-s of user accounts $user can see
     *
     * @param User $user
     * @return array
     */
    function findVisibleUserIds($user) {
      
      // Admins can see all users in the database
      if($user->isAdministrator() || $user->isPeopleManager()) {
        $rows = db_execute_all('SELECT id FROM ' . TABLE_PREFIX . 'users');
        
        $result = array();
        if(is_foreachable($rows)) {
          foreach($rows as $row) {
            $result[] = (integer) $row['id'];
          } // foreach
        } // if
        
        return $result;
      } // if
      
      // First load all the project $user is involved with
      $project_ids = array();
      
      $rows = db_execute_all('SELECT DISTINCT project_id FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ?', $user->getId());
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $project_ids[] = (integer) $row['project_id'];
        } // foreach
      } // if
      
      if(count($project_ids) < 1) {
        $rows = db_execute_all("SELECT id FROM " . TABLE_PREFIX . 'users WHERE company_id = ?', $user->getCompanyId());
      } else {
        $rows = db_execute_all('(SELECT id FROM ' . TABLE_PREFIX . "users WHERE company_id = ?) UNION (SELECT user_id AS 'id' FROM " . TABLE_PREFIX . "project_users WHERE project_id IN (?))", $user->getCompanyId(), $project_ids);
      } // if
      
      $result = array();
      if(count($rows)) {
        foreach($rows as $row) {
          $result[] = (integer) $row['id'];
        } // foreach
      } // if
      
      return array_unique($result);
    } // findVisibleUserIds
    
    /**
     * Return ID-s of companies $user can see
     *
     * @param User $user
     * @return array
     */
    function findVisibleCompanyIds($user) {
      
      // Admins can see all companies in the database
      if($user->isAdministrator() || $user->isPeopleManager()) {
        $rows = db_execute_all('SELECT id FROM ' . TABLE_PREFIX . 'companies ORDER BY name');
        
        $result = array();
        if(is_foreachable($rows)) {
          foreach($rows as $row) {
            $result[] = (integer) $row['id'];
          } // foreach
        } // if
        
        return $result;
      } // if
      
      $visible_user_ids = $user->visibleUserIds();
      
      if(is_foreachable($visible_user_ids)) {
        $users_table = TABLE_PREFIX . 'users';
        $companies_table = TABLE_PREFIX . 'companies';
        
        $rows = db_execute_all("SELECT DISTINCT(company_id) FROM $users_table, $companies_table WHERE $users_table.id IN (?) ORDER BY $companies_table.is_owner DESC, $companies_table.name", $visible_user_ids);
        
        $result = array();
        if(is_foreachable($rows)) {
          foreach($rows as $row) {
            $result[] = (integer) $row['company_id'];
          } // foreach
        } // if
        
        if(!in_array($user->getCompanyId(), $result)) {
          $result[] = $user->getCompanyId();
        } // if
        
        $projects_table = TABLE_PREFIX . 'projects';
        $project_users_table = TABLE_PREFIX . 'project_users';
        
        $rows = db_execute_all("SELECT DISTINCT $projects_table.company_id AS 'company_id' FROM $projects_table, $project_users_table WHERE $projects_table.id = $project_users_table.project_id AND $project_users_table.user_id = ? AND $projects_table.company_id > 0 AND $projects_table.company_id NOT IN (?)", $user->getId(), $result);
        if(is_foreachable($rows)) {
          foreach($rows as $row) {
            $result[] = (integer) $row['company_id'];
          } // foreach
        } // if
        
        return $result;
      } else {
        return array($user->getCompanyId());
      } // if
    } // findVisibleCompanyIds
    
    /**
     * Return users who were online in the past $minutes minutes
     *
     * @param User $user
     * @param integer $minutes
     * @return array
     */
    function findWhoIsOnline($user, $minutes = 15) {
      $visible_user_ids = Users::findVisibleUserIds($user);
      if(is_foreachable($visible_user_ids)) {
        $users_table = TABLE_PREFIX . 'users';
        $reference = new DateTimeValue("-$minutes minutes");
        
        return Users::findBySQL("SELECT * FROM $users_table WHERE id IN (?) AND last_activity_on > ? ORDER BY CONCAT(first_name, last_name, email)", array($visible_user_ids, $reference));
      } // if
      return null;
    } // findWhoIsOnline
    
    /**
     * Return number of users who were online in the past $minutes minutes
     *
     * @param User $user
     * @param integer $minutes
     * @return array
     */
    function countWhoIsOnline($user, $minutes = 15) {
      $visible_user_ids = Users::findVisibleUserIds($user);
      if(is_foreachable($visible_user_ids)) {
        $users_table = TABLE_PREFIX . 'users';
        $reference = new DateTimeValue("-$minutes minutes");
        
        return array_var(db_execute_one("SELECT COUNT(id) AS 'row_count' FROM $users_table WHERE id IN (?) AND last_activity_on > ?", $visible_user_ids, $reference), 'row_count');
      } // if
      return 0;
    } // countWhoIsOnline
    
    /**
     * Find users and return them in form that is good for select user controls
     * 
     * Users will be returned in an array grouped by company name
     *
     * @param Company $company
     * @param Project $project
     * @param array $exclude_ids
     * @return array
     */
    function findForSelect($company = null, $project = null, $exclude_ids = null) {
      $users_table = TABLE_PREFIX . 'users';
      $companies_table = TABLE_PREFIX . 'companies';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      $exclude_filter = null;
      if($exclude_ids) {
        if(!is_array($exclude_ids)) {
          $exclude_ids = array($exclude_ids);
        } // if
        
        foreach($exclude_ids as $k => $v) {
          $exclude_ids[$k] = (integer) $v;
        } // foreach
        
        $exclude_filter = " AND $users_table.id NOT IN (" . implode(', ', $exclude_ids) . ") ";
      } // if
      
      $users = array();
      if(instance_of($project, 'Project') && instance_of($company, 'Company')) {
        $users = db_execute_all("SELECT $users_table.id, TRIM(CONCAT($users_table.first_name, ' ', $users_table.last_name)) AS 'display_name', $users_table.email, $users_table.company_id FROM $users_table, $project_users_table WHERE $users_table.id = $project_users_table.user_id AND $users_table.company_id = ? AND $project_users_table.project_id = ? $exclude_filter ORDER BY display_name, $users_table.email", $company->getId(), $project->getId());
      } elseif(instance_of($project, 'Project')) {
        $users = db_execute_all("SELECT $users_table.id, TRIM(CONCAT($users_table.first_name, ' ', $users_table.last_name)) AS 'display_name', $users_table.email, $users_table.company_id FROM $users_table, $project_users_table WHERE $users_table.id = $project_users_table.user_id AND $project_users_table.project_id = ? $exclude_filter ORDER BY display_name, $users_table.email", $project->getId());
      } elseif(instance_of($company, 'Company')) {
        $users = db_execute_all("SELECT $users_table.id, TRIM(CONCAT($users_table.first_name, ' ', $users_table.last_name)) AS 'display_name', $users_table.email, $users_table.company_id FROM $users_table WHERE company_id = ? $exclude_filter ORDER BY display_name, $users_table.email", $company->getId());
      } else {
        if($exclude_filter) {
          $users = db_execute_all("SELECT $users_table.id, TRIM(CONCAT($users_table.first_name, ' ', $users_table.last_name)) AS 'display_name', $users_table.email, $users_table.company_id FROM $users_table WHERE $users_table.id NOT IN (" . implode(', ', $exclude_ids) . ") ORDER BY display_name, $users_table.email");
        } else {
          $users = db_execute_all("SELECT $users_table.id, TRIM(CONCAT($users_table.first_name, ' ', $users_table.last_name)) AS 'display_name', $users_table.email, $users_table.company_id FROM $users_table ORDER BY display_name, $users_table.email");
        } // if
      } // if
      
      if(is_foreachable($users)) {
        $companies = Companies::getIdNameMap();
        
        // Create a result array and make sure that owner company is the first 
        // element in it
        $result = array(
          first($companies) => array()
        );
        
        foreach($users as $user) {
          if(empty($user['display_name'])) {
            $user['display_name'] = $user['email'];
          } // if
          
          $company_name = array_var($companies, $user['company_id']);
          if($company_name) {
            if(isset($result[$company_name])) {
              $result[$company_name][] = $user;
            } else {
              $result[$company_name] = array($user);
            } // if
          } // if
        } // foreach
        
        $first = array();
        foreach($result as $k => $v) {
          $first[$k] = $v;
          unset($result[$k]);
          break;
        } // foreach
        
        ksort($result);
        
        return array_merge($first, $result);
      } else {
        return null;
      } // if
    } // findForSelect
    
    /**
     * Find users by specified id-s and return them in form that is convinient for select controls
     *
     * @param array $user_ids
     * @return array
     */
    function findForSelectByIds($user_ids = null) {
      if (!is_foreachable($user_ids)) {
        return null;  
      } // if
      
      $users_table = TABLE_PREFIX . 'users';
      $users = db_execute_all("SELECT $users_table.id, TRIM(CONCAT($users_table.first_name, ' ', $users_table.last_name)) AS 'display_name', $users_table.email, $users_table.company_id FROM $users_table WHERE $users_table.id IN (?) ORDER BY display_name, $users_table.email", $user_ids);
      
      if(!is_foreachable($users)) {
        return null;
      } //if 
      
      $companies = Companies::getIdNameMap();
      
      // Create a result array and make sure that owner company is the first 
      // element in it
      $result = array(
        first($companies) => array()
      );
      
      foreach($users as $user) {
        if(empty($user['display_name'])) {
          $user['display_name'] = $user['email'];
        } // if
        
        $company_name = array_var($companies, $user['company_id']);
        if($company_name) {
          if(isset($result[$company_name])) {
            $result[$company_name][] = $user;
          } else {
            $result[$company_name] = array($user);
          } // if
        } // if
      } // foreach
      
      $first = array();
      foreach($result as $k => $v) {
        $first[$k] = $v;
        unset($result[$k]);
        break;
      } // foreach
      
      ksort($result);
      
      return array_merge($first, $result);
    } // findForSelectByIds
    
    /**
     * Delete users by company
     *
     * @param Company $company
     * @return boolean
     */
    function deleteByCompany($company) {
      return Users::delete(array('company_id = ?', $company->getId()));
    } // deleteByCompany
    
    /**
     * Fetch user details from database for provided user id-s
     *
     * @param array $user_ids
     * @return array
     */
    function findUsersDetails($user_ids) {
      if (!is_foreachable($user_ids)) {
        return false;
      } // if
      
      $users_table = TABLE_PREFIX . 'users';
      
      $users = array();
      $users = db_execute_all("SELECT $users_table.id, $users_table.first_name, $users_table.email, $users_table.last_name, $users_table.company_id FROM $users_table WHERE $users_table.id IN (?)", $user_ids);
           
      if (!is_foreachable($users)) {
        return false;
      } // if
      
      $companies = Companies::getIdNameMap();
      
      // Create a result array and make sure that owner company is the first 
      // element in it
      $result = array(
        first($companies) => array()
      );
      
      foreach ($users as $user) {
        if($user['first_name'] && $user['last_name']) {
          $user['display_name'] = $user['first_name'] . ' ' . $user['last_name'];
        } elseif($user['first_name']) {
          $user['display_name'] = $user['first_name'];
        } elseif($user['last_name']) {
          $user['display_name'] = $user['last_name'];
        } else {
          $user['display_name'] = $user['email'];
        } // if
        
        $company_name = array_var($companies, $user['company_id']);
        if($company_name) {
          if(isset($result[$company_name])) {
            $result[$company_name][] = $user;
          } else {
            $result[$company_name] = array($user);
          } // if
        } // if
      } // if
      
      ksort($result);
      
      return $result; 
    } // findUsersDetails
  
  }

?>