<?php

  /**
   * Projects class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Projects extends BaseProjects {
    
    /**
     * Return projects by ID-s
     *
     * @param array $ids
     * @param string $order
     * @return array
     */
    function findByIds($ids, $order = 'name') {
      return Projects::find(array(
        'conditions' => array('id IN (?) AND type = ?', $ids, PROJECT_TYPE_NORMAL),
        'order' => $order,
      ));
    } // findByIds
    
    /**
     * Return all project records sorted by name
     *
     * @param void
     * @return array
     */
    function findAll() {
      return Projects::find(array(
        'order' => 'name'
      ));
    } // findAll
    
    /**
     * Return projects by user
     * 
     * $statuses is an array of allowed statuses. If NULL no status filtering 
     * will be done
     * 
     * If $all_for_admins_and_pms is set to true system will return all projects 
     * if user is administrator or project manager
     *
     * @param User $user
     * @param array $statuses
     * @param boolean $all_for_admins_and_pms
     * @return array
     */
    function findByUser($user, $statuses = null, $all_for_admins_and_pms = false) {
      $projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      if($all_for_admins_and_pms && ($user->isAdministrator() || $user->isProjectManager())) {
        if($statuses) {
          return Projects::findBySQL("SELECT * FROM $projects_table WHERE type = ? AND status IN (?) ORDER BY name", array(PROJECT_TYPE_NORMAL, $statuses));
        } else {
          return Projects::findBySQL("SELECT * FROM $projects_table WHERE type = ? ORDER BY name", array(PROJECT_TYPE_NORMAL));
        } // if
      } else {
        if($statuses) {
          return Projects::findBySQL("SELECT $projects_table.* FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.type = ? AND $projects_table.status IN (?) ORDER BY $projects_table.name", array($user->getId(), PROJECT_TYPE_NORMAL, $statuses));
        } else {
          return Projects::findBySQL("SELECT $projects_table.* FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.type = ? ORDER BY $projects_table.name", array($user->getId(), PROJECT_TYPE_NORMAL));
        } // if
      } // if
    } // findByUser
    
    /**
     * Return project ID-s that $user can see
     * 
     * If $all_for_admins_and_pms is set to true system will return all projects 
     * if user is administrator or project manager
     *
     * @param User $user
     * @param array $statuses
     * @param boolean $all_for_admins_and_pms
     * @return array
     */
    function findProjectIdsByUser($user, $statuses = null, $all_for_admins_and_pms = false) {
      $projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      if($all_for_admins_and_pms && ($user->isAdministrator() || $user->isProjectManager())) {
        if($statuses) {
          $rows = db_execute_all("SELECT id FROM $projects_table WHERE type = ? AND status IN (?) ORDER BY name", PROJECT_TYPE_NORMAL, $statuses);
        } else {
          $rows = db_execute_all("SELECT id FROM $projects_table WHERE type = ? ORDER BY name", PROJECT_TYPE_NORMAL);
        } // if
      } else {
        if($statuses) {
          $rows = db_execute_all("SELECT $projects_table.id FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.type = ? AND $projects_table.status IN (?) ORDER BY $projects_table.name", $user->getId(), PROJECT_TYPE_NORMAL, $statuses);
        } else {
          $rows = db_execute_all("SELECT $projects_table.id FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.type = ? ORDER BY $projects_table.name", $user->getId(), PROJECT_TYPE_NORMAL);
        } // if
      } // if
      
      $ids = array();
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $ids[] = (integer) $row['id'];
        } // foreach
      } // if
      return $ids;
    } // findProjectIdsByUser
    
    /**
     * Return project ID => project name map for a given user
     * 
     * If $all_for_admins_and_pms is set to true system will return all projects 
     * if user is administrator or project manager
     *
     * @param User $user
     * @param array $statuses
     * @param array $exclude_ids
     * @param boolean $all_for_admins_and_pms
     * @return null
     */
    function findNamesByUser($user, $statuses = null, $exclude_ids = null, $all_for_admins_and_pms = false) {
      $projects_table = TABLE_PREFIX . 'projects';
      $project_users_table = TABLE_PREFIX . 'project_users';
      
      $exclude_filter = null;
      if(is_foreachable($exclude_ids)) {
        $exclude_filter = " AND $projects_table.id NOT IN (" . implode(', ', $exclude_ids) . ") ";
      } // if
      
      if($all_for_admins_and_pms && ($user->isAdministrator() || $user->isProjectManager())) {
        if($statuses) {
          $rows = db_execute_all("SELECT $projects_table.id, $projects_table.name FROM $projects_table WHERE $projects_table.type = ? AND $projects_table.status IN (?) $exclude_filter ORDER BY $projects_table.name", PROJECT_TYPE_NORMAL, $statuses);
        } else {
          $rows = db_execute_all("SELECT $projects_table.id, $projects_table.name FROM $projects_table WHERE $projects_table.type = ? $exclude_filter ORDER BY $projects_table.name", PROJECT_TYPE_NORMAL);
        } // if
      } else {
        if($statuses) {
          $rows = db_execute_all("SELECT $projects_table.id, $projects_table.name FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.type = ? AND $projects_table.status IN (?) $exclude_filter ORDER BY $projects_table.name", $user->getId(), PROJECT_TYPE_NORMAL, $statuses);
        } else {
          $rows = db_execute_all("SELECT $projects_table.id, $projects_table.name FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.type = ? $exclude_filter ORDER BY $projects_table.name", $user->getId(), PROJECT_TYPE_NORMAL);
        } // if
      } // if
      
      $result = array();
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $result[(integer) $row['id']] = $row['name'];
        } // foreach
      } // if
      return $result;
    } // findNamesByUser
    
    /**
     * Find project by user alone
     * 
     * If $all_for_admins_and_pms is set to true system will return all projects 
     * if user is administrator or project manager
     *
     * @param User $user
     * @param array $statuses
     * @param integer $page
     * @param integer $per_page
     * @param boolean $all_for_admins_and_pms
     * @return array
     */
    function paginateByUser($user, $statuses = null, $page = 1, $per_page = 10, $all_for_admins_and_pms = false) {
      if(!instance_of($user, 'User')) {
        return null;
      } // if
      
      // Admin or project manager
      if($all_for_admins_and_pms && ($user->isProjectManager() || $user->isAdministrator())) {
        if($statuses) {
          return Projects::paginate(array(
            'conditions' => array('status IN (?)', $statuses),
            'order' => 'name'
          ), $page, $per_page);
        } else {
          return Projects::paginate(array(
            'order' => 'name'
          ), $page, $per_page);
        } // if
      } // if
      
      // Clients and other members
      $rows = db_execute_all('SELECT project_id FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ?', $user->getId());
      if(is_foreachable($rows)) {
        $project_ids = array();
        foreach($rows as $row) {
          $project_ids[] = (integer) $row['project_id'];
        } // if
      } else {
        return array(null, new Pager($page, 0, $per_page));
      } // if
      
      if($statuses) {
        return Projects::paginate(array(
          'conditions' => array('id IN (?) AND status IN (?)', $project_ids, $statuses),
          'order' => 'name'
        ), $page, $per_page);
      } else {
        return Projects::paginate(array(
          'conditions' => array('id IN (?)', $project_ids),
          'order' => 'name'
        ), $page, $per_page);
      } // if
    } // paginateByUser
    
    /**
     * Find project by user and group
     *
     * @param User $user
     * @param ProjectGroup $group
     * @param array $statuses
     * @param integer $page
     * @param integer $per_page
     * @param boolean $all_for_admins_and_pms
     * @return array
     */
    function paginateByUserAndGroup($user, $group, $statuses = null, $page = 1, $per_page = 10, $all_for_admins_and_pms = false) {
      if(!instance_of($user, 'User')) {
        return null;
      } // if
      
      if(!instance_of($group, 'ProjectGroup')) {
        return null;
      } // if
      
      // Admin or project manager
      if($all_for_admins_and_pms && ($user->isProjectManager() || $user->isAdministrator())) {
        if($statuses) {
          return Projects::paginate(array(
            'conditions' => array('group_id = ? AND status IN (?)', $group->getId(), $statuses),
            'order' => 'name'
          ), $page, $per_page);
        } else {
          return Projects::paginate(array(
            'conditions' => array('group_id = ?', $group->getId()),
            'order' => 'name'
          ), $page, $per_page);
        } // if
      } // if
      
      // Clients and other members
      $rows = db_execute_all('SELECT project_id FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ?', $user->getId());
      if(is_foreachable($rows)) {
        $project_ids = array();
        foreach($rows as $row) {
          $project_ids[] = (integer) $row['project_id'];
        } // if
      } else {
        return array(null, new Pager($page, 0, $per_page));
      } // if
      
      if($statuses) {
        return Projects::paginate(array(
          'conditions' => array('id IN (?) AND group_id = ? AND status IN (?)', $project_ids, $group->getId(), $statuses),
          'order' => 'name'
        ), $page, $per_page);
      } else {
        return Projects::paginate(array(
          'conditions' => array('id IN (?) AND group_id = ?', $project_ids, $group->getId()),
          'order' => 'name'
        ), $page, $per_page);
      } // if
    } // paginateByUserAndGroup
    
    /**
     * Paginate projects by user and company
     *
     * @param User $user
     * @param Company $company
     * @param array $statuses
     * @param integer $page
     * @param integer $per_page
     * @param boolean $all_for_admins_and_pms
     * @return array
     */
    function paginateByUserAndCompany($user, $company, $statuses = null, $page = 1, $per_page = 10, $all_for_admins_and_pms = false) {
      if(!instance_of($user, 'User')) {
        return null;
      } // if
      
      if(instance_of($company, 'Company')) {
        $company_id = $company->getIsOwner() ? array(0, $company->getId()) : $company->getId();
      } else {
        return null;
      } // if
      
      // Admin or project manager
      if($all_for_admins_and_pms && ($user->isProjectManager() || $user->isAdministrator())) {
        if($statuses) {
          return Projects::paginate(array(
            'conditions' => array('company_id IN (?) AND status IN (?)', $company_id, $statuses),
            'order' => 'name'
          ), $page, $per_page);
        } else {
          return Projects::paginate(array(
            'conditions' => array('company_id IN (?)', $company_id),
            'order' => 'name'
          ), $page, $per_page);
        } // if
      } // if
      
      // Clients and other members
      $rows = db_execute_all('SELECT project_id FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ?', $user->getId());
      if(is_foreachable($rows)) {
        $project_ids = array();
        foreach($rows as $row) {
          $project_ids[] = (integer) $row['project_id'];
        } // if
      } else {
        return array(null, new Pager($page, 0, $per_page));
      } // if
      
      if($statuses) {
        return Projects::paginate(array(
          'conditions' => array('id IN (?) AND company_id IN (?) AND status IN (?)', $project_ids, $company_id, $statuses),
          'order' => 'name'
        ), $page, $per_page);
      } else {
        return Projects::paginate(array(
          'conditions' => array('id IN (?) AND company_id IN (?)', $project_ids, $company_id),
          'order' => 'name'
        ), $page, $per_page);
      } // if
    } // paginateByUserAndCompany
  
    /**
     * Find Projects by Status
     * 
     * $status can be array of statuses or a single status
     *
     * @param string $status
     * @param string $order
     * @return Array
     */
    function findByStatus($status, $order = 'created_on DESC') {
      $status = (array) $status;
    	return Projects::find(array(
    	  'conditions' => array('status IN (?) AND type = ?', $status, PROJECT_TYPE_NORMAL),
    	  'order'      => $order,
    	));
    } // findByStatus
    
    /**
     * Return projects from a sepcific group
     *
     * @param ProjectGroup $group
     * @return array
     */
    function findByGroup($group) {
      return Projects::find(array(
        'conditions' => array('group_id = ? AND type = ?', $group->getId(), PROJECT_TYPE_NORMAL),
        'order_by' => 'created_on DESC'
      ));
    } // findByGroup
    
    /**
     * Paginate projects by group
     *
     * @param ProjectGroup $group
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateByGroup($group, $page = 1, $per_page = 10) {
      return Projects::paginate(array(
        'conditions' => array('group_id = ? AND type = ?', $group->getId(), PROJECT_TYPE_NORMAL),
        'order_by' => 'created_on DESC'
      ), $page, $per_page);
    } // paginateByGroup
    
    /**
     * Return number of projects in a specific group
     *
     * @param ProjectGroup $group
     * @return integer
     */
    function countByGroup($group) {
      return Projects::count(array('group_id = ? AND type = ?', $group->getId(), PROJECT_TYPE_NORMAL));
    } // countByGroup
    
    /**
     * Return projects visible for $user that have $company for client
     *
     * @param User $user
     * @param Company $company
     * @param boolean $all_for_admins_and_pms
     * @return array
     */
    function findByUserAndCompany($user, $company, $all_for_admins_and_pms = false) {
      if(!instance_of($user, 'User')) {
        return null;
      } // if
      
      if(instance_of($company, 'Company')) {
        $company_id = $company->getIsOwner() ? 0 : $company->getId();
      } else {
        return null;
      } // if
      
      // Admin or project manager
      if($all_for_admins_and_pms && ($user->isProjectManager() || $user->isAdministrator())) {
        return Projects::find(array(
          'conditions' => array('company_id = ?', $company_id),
          'order' => 'name'
        ));
      } // if
      
      // Clients and other members
      $rows = db_execute_all('SELECT project_id FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ?', $user->getId());
      if(is_foreachable($rows)) {
        $project_ids = array();
        foreach($rows as $row) {
          $project_ids[] = (integer) $row['project_id'];
        } // if
      } else {
        return null;
      } // if
      
      return Projects::find(array(
        'conditions' => array('id IN (?) AND company_id = ?', $project_ids, $company_id),
        'order' => 'name'
      ));
    } // findByUserAndCompany
    
    /**
     * Return client company ID
     * 
     * $project can be Project instance of project id (as integer)
     *
     * @param Project $project
     * @return integer
     */
    function findClientId($project) {
    	$project_id = instance_of($project, 'Project') ? $project->getId() : (integer) $project;
    	return array_var(db_execute_one('SELECT company_id FROM ' . TABLE_PREFIX . 'projects WHERE id = ?', $project_id), 'company_id', null);
    } // findClientId
    
    /**
     * Reset relations by company ID
     *
     * @param Company $company
     * @return boolean
     */
    function resetByCompany($company) {
      return db_execute('UPDATE ' . TABLE_PREFIX . "projects SET company_id = '0' WHERE company_id = ?", $company->getId());
    } // resetByCompany
    
  }

?>