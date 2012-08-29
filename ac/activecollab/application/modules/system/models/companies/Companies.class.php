<?php

  /**
   * Companies manager class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Companies extends BaseCompanies {
    
    /**
     * Return all companies
     *
     * @param void
     * @return array
     */
    function findAll() {
      return Companies::find(array(
        'order' => 'is_owner DESC, name',
      ));
    } // findAll
    
    /**
     * Paginate active categories
     *
     * @param User $user
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateActive($user, $page = 1, $per_page = 30) {
      $visible_ids = $user->visibleCompanyIds();
      if(is_foreachable($visible_ids)) {
        return Companies::paginate(array(
          'conditions' => array('(is_archived = ? OR id = ?) AND id IN (?)', false, $user->getCompanyId(), $visible_ids),
          'order' => 'is_owner DESC, name',
        ), $page, $per_page);
      } else {
        return array(null, new Pager(1, 0, $per_page));
      } // if
    } // paginateActive
    
    /**
     * Return list of archived companies
     *
     * @param User $user
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateArchived($user, $page = 1, $per_page = 30) {
      $visible_ids = $user->visibleCompanyIds();
      if(is_foreachable($visible_ids)) {
        return Companies::paginate(array(
          'conditions' => array('is_archived = ? AND id IN (?)', true, $visible_ids),
          'order' => 'is_owner DESC, name',
        ), $page, $per_page);
      } else {
        return array(null, new Pager(1, 0, $per_page));
      } // if
    } // paginateArchived
  
    /**
     * Return owner company from database
     *
     * @param void
     * @return Company
     */
    function findOwnerCompany() {
      return Companies::find(array(
        'conditions' => array('is_owner = ?', true),
        'one' => true,
      ));
    } // findOwnerCompany
    
    /**
     * Return client companies
     * 
     * Clients will be limited only to companies $user can see
     *
     * @param User $user
     * @return array
     */
    function findClients($user) {
      $company_ids = $user->visibleCompanyIds();
      
      if(is_foreachable($company_ids)) {
        $companies_table = TABLE_PREFIX . 'companies';
        $projects_table = TABLE_PREFIX . 'projects';
        
        return Companies::findBySQL("SELECT DISTINCT $companies_table.* FROM $companies_table, $projects_table WHERE $projects_table.company_id > ? AND $projects_table.company_id IN (?) AND $companies_table.id = $projects_table.company_id AND $companies_table.is_owner != ? ORDER BY $companies_table.name", array(0, $company_ids, true));
      } else {
        return null;
      } // if
    } // findClients
    
    /**
     * Return companies from an array of ID-s
     *
     * @param array $ids
     * @return array
     */
    function findByIds($ids) {
      if(is_foreachable($ids)) {
        foreach($ids as $k => $v) {
          $ids[$k] = (integer) $v;
        } // foreach
        
        $companies_table = TABLE_PREFIX . 'companies';
        
        return Companies::findBySQL("SELECT * FROM $companies_table WHERE id IN (" . implode(', ', $ids) . ") ORDER BY is_owner DESC, name");
      } // if
      return null;
    } // findByIds
    
    /**
     * Return ID => name map
     *
     * @param void
     * @return array
     */
    function getIdNameMap($ids = null) {
      
      // No ID-s
      if($ids === null) {
        $cached_value = cache_get('companies_id_name');
        if($cached_value) {
          return $cached_value;
        } else {
          $result = array();
          
          $rows = db_execute_all('SELECT id, name FROM ' . TABLE_PREFIX . 'companies ORDER BY is_owner DESC, name');
          if(is_foreachable($rows)) {
            foreach($rows as $row) {
              $result[(integer) $row['id']] = $row['name'];
            } // foreach
          } // if
          
          cache_set('companies_id_name', $result);
          return $result;
        } // if
        
      // We have ID-s
      } else {
        $result = array();
        
        if(is_foreachable($ids)) {
          $rows = db_execute_all('SELECT id, name FROM ' . TABLE_PREFIX . 'companies WHERE id IN (?) ORDER BY is_owner DESC, name', $ids);
          if(is_foreachable($rows)) {
            foreach($rows as $row) {
              $result[(integer) $row['id']] = $row['name'];
            } // foreach
          } // if
        } // if
        
        return $result;
      } // if
    } // getIdNameMap
    
    /**
     * Returns array of companies that are involved in project
     * @param Project $project
     * @return array
     */
    function findByProject($project) {
      $people = ProjectUsers::findUsersByProject($project);
      if (is_foreachable($people)) {
        $company_ids = array();
        foreach ($people as $person) {
        	if (!in_array($person->getCompanyId(),$company_ids)) {
        	  $company_ids[] = $person->getCompanyId();
        	} // if
        } // foreach
        return Companies::findByIds($company_ids);
      } else {
        return null;
      } // if
    } // findByProject
    
  }

?>