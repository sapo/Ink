<?php

  /**
   * ProjectObjects class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectObjects extends BaseProjectObjects {
    
    /**
     * Find project objects by list of ID-s
     *
     * @param array $ids
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findByIds($ids, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('id IN (?) AND state >= ? AND visibility >= ?', $ids, $min_state, $min_visibility),
        'order' => 'created_on DESC',
      ));
    } // findByIds
    
    /**
     * Paginate objects by object ID-s
     *
     * @param array $ids
     * @return array
     */
    function paginateByIds($ids, $page = 1, $per_page = 10, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::paginate(array(
        'conditions' => array('id IN (?) AND state >= ? AND visibility >= ?', $ids, $min_state, $min_visibility),
        'order' => 'created_on DESC',
      ), $page, $per_page);
    } // paginateByIds
    
    /**
     * Find subitems by parent object, first level only
     *
     * @param ProjectObject $parent
     * @param mixed $additional_conditions
     * @param string $order
     * @return array
     */
    function findByParent($parent, $additional_conditions = null, $order = null) {
      return ProjectObjects::find(array(
        'conditions' => DataManager::combineConditions(array('parent_id = ?', $parent->getId()), $additional_conditions),
        'order' => $order,
      ));
    } // findByParent
    
    /**
     * Return number of subitems for parent object, first level only
     *
     * @param ProjectObject $parent
     * @param mixed $additional_conditions
     * @return integer
     */
    function countByParent($parent, $additional_conditions = null) {
      return ProjectObjects::count(DataManager::combineConditions(array('parent_id = ?', $parent->getId()), $additional_conditions));
    } // countByParent
    
    // ---------------------------------------------------
    //  New stuff
    // ---------------------------------------------------
    
    /**
     * Paginate new and updated objects
     *
     * @param User $user
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateNew($user, $page = 1, $per_page = 30) {
      $type_filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE, PROJECT_STATUS_PAUSED, PROJECT_STATUS_CANCELED, PROJECT_STATUS_COMPLETED));
      if($type_filter) {
        $exclude = ProjectObjectViews::findViewedObjectIds($this->logged_user);
        
        $last_visit_on = $user->getLastVisitOn();
        if(!instance_of($last_visit_on, 'DateTimeValue')) {
          $last_visit_on = new DateTimeValue(filemtime(ENVIRONMENT_PATH . '/config/config.php'));
        } // if
        $month_ago = new DateTimeValue('-30 days');
        
        // Last visit or last month
        $since = $last_visit_on->getTimestamp() > $month_ago->getTimestamp() ? $last_visit_on : $month_ago;
        
        if($exclude) {
          return ProjectObjects::paginate(array(
            'conditions' => array($type_filter . ' AND state >= ? AND visibility >= ? AND created_on >= ? AND created_by_id != ? AND id NOT IN (?)', STATE_VISIBLE, $user->getVisibility(), $since, $user->getId(), $exclude),
            'order' => 'created_on DESC'
          ), $page, $per_page);
        } else {
          return ProjectObjects::paginate(array(
            'conditions' => array($type_filter . ' AND state >= ? AND visibility >= ? AND created_on >= ? AND created_by_id != ?', STATE_VISIBLE, $user->getVisibility(), $since, $user->getId()),
            'order' => 'created_on DESC'
          ), $page, $per_page);
        }
      } else {
        return array(null, new Pager(1, 0, $per_page));
      } // if
    } // paginateNew
    
    /**
     * Return number of new objects in $project_ids since $since
     *
     * @param DateTimeValue $since
     * @param array $project_ids
     * @param array $exclude
     * @param integer $min_state
     * @param integer $min_visibility
     * @return integer
     */
    function countNew($user) {
      $type_filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE, PROJECT_STATUS_PAUSED, PROJECT_STATUS_CANCELED, PROJECT_STATUS_COMPLETED));
      if($type_filter) {
        $exclude = ProjectObjectViews::findViewedObjectIds($user);
        
        $last_visit_on = $user->getLastVisitOn();
        if(!instance_of($last_visit_on, 'DateTimeValue')) {
          $last_visit_on = new DateTimeValue(filemtime(ENVIRONMENT_PATH . '/config/config.php'));
        } // if
        $month_ago = new DateTimeValue('-30 days');
        
        // Last visit or last month
        $since = $last_visit_on->getTimestamp() > $month_ago->getTimestamp() ? $last_visit_on : $month_ago;
        
        if($exclude) {
          return ProjectObjects::count(array($type_filter . ' AND state >= ? AND visibility >= ? AND created_on >= ? AND created_by_id != ? AND id NOT IN (?)', STATE_VISIBLE, $user->getVisibility(), $since, $user->getId(), $exclude));
        } else {
          return ProjectObjects::count(array($type_filter . ' AND state >= ? AND visibility >= ? AND created_on >= ? AND created_by_id != ? ', STATE_VISIBLE, $user->getVisibility(), $since, $user->getid()));
        } // if
      } else {
        return 0;
      } // if
    } // countNew
    
    // ---------------------------------------------------
    //  Late, today, upcoming
    // ---------------------------------------------------
    
    /**
     * Return late and today object
     *
     * @param User $user
     * @param Project $project
     * @param array $types
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function findLateAndToday($user, $project = null, $types = null, $page = null, $per_page = null) {
      if(instance_of($project, 'Project')) {
        $type_filter = ProjectUsers::getVisibleTypesFilterByProject($user, $project, $types);
      } else {
        $type_filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE), $types);
      }
      if($type_filter) {
        $today = new DateValue(time() + get_user_gmt_offset());
        
        $conditions = array($type_filter . ' AND due_on <= ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $today, STATE_VISIBLE, $user->getVisibility());
        
        if($page !== null && $per_page !== null) {
          return ProjectObjects::paginate(array(
            'conditions' => $conditions,
            'order' => 'due_on, priority DESC',
          ), $page, $per_page);
        } else {
          return ProjectObjects::find(array(
            'conditions' => $conditions,
            'order' => 'due_on, priority DESC',
          ));
        } // if
      } // if
      
      return null;
    } // findLateAndTodayByProject
    
    /**
     * Return number of specific object in a list of project that are late or shceduled for today
     *
     * @param User $user
     * @param Project $project
     * @param array $types
     * @return integer
     */
    function countLateAndToday($user, $project = null, $types = null) {
      $type_filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE), $types);
      if($type_filter) {
        return ProjectObjects::count(array($type_filter . ' AND due_on <= ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', new DateValue(time() + get_user_gmt_offset()), STATE_VISIBLE, $user->getVisibility()));
      } // if
      
      return 0;
    } // countLateAndToday
    
    /**
     * Return upcoming objects in a given projects
     *
     * @param User $user
     * @param Project $project
     * @param array $types
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function findUpcoming($user, $project = null, $types = null, $page = null, $per_page = null) {
      if(instance_of($project, 'Project')) {
        $type_filter = ProjectUsers::getVisibleTypesFilterByProject($user, $project, $types);
      } else {
        $type_filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE), $types);
      }
      if($type_filter) {
        $today = new DateTimeValue();
        $today->advance(get_user_gmt_offset());
        
        $newer_than = $today->endOfDay();
        
        $conditions = array($type_filter . ' AND due_on > ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $newer_than, STATE_VISIBLE, $user->getVisibility());
        
        if($page !== null && $per_page !== null) {
          return ProjectObjects::paginate(array(
            'conditions' => $conditions,
            'order' => 'due_on, priority DESC',
          ), $page, $per_page);
        } else {
          return ProjectObjects::find(array(
            'conditions' => $conditions,
            'order' => 'due_on, priority DESC',
          ));
        } // if
      } // if
      
      return null;
    } // findUpcoming
    
    /**
     * Return completed subitems by parent
     *
     * @param ProjectObject $parent
     * @param string $order
     * @return array
     */
    function getCompletedByParent($parent, $additional_conditions = null, $order = null) {
      return ProjectObjects::find(array(
        'conditions' => DataManager::combineConditions(array('parent_id = ? AND completed_on IS NOT NULL', $parent->getId()), $additional_conditions),
        'order' => $order,
      ));
    } // getCompletedByParent
    
    /**
     * Return number of completed items by $parent, first level only
     *
     * @param ProjectObject $parent
     * @return integer
     */
    function countCompletedByParent($parent, $additional_conditions = null) {
      return ProjectObjects::count(DataManager::combineConditions(array('parent_id = ? AND completed_on IS NOT NULL', $parent->getId()), $additional_conditions));
    } // countCompletedByParent
    
    /**
     * Return open subitems by parent
     *
     * @param ProjectObject $parent
     * @param string $order
     * @return array
     */
    function findOpenByParent($parent, $additional_conditions = null, $order = null) {
      return ProjectObjects::find(array(
        'conditions' => DataManager::combineConditions(array('parent_id = ? AND completed_on IS NULL', $parent->getId()), $additional_conditions),
        'order' => $order,
      ));
    } // findOpenByParent
    
    /**
     * Return number of open items by $parent, first level only
     *
     * @param ProjectObject $parent
     * @return integer
     */
    function countOpenByParent($parent, $additional_conditions = null) {
      return ProjectObjects::count(DataManager::combineConditions(array('parent_id = ? AND completed_on IS NULL', $parent->getId()), $additional_conditions));
    } // countOpenByParent
    
    // ---------------------------------------------------
    //  Trash
    // ---------------------------------------------------
    
    /**
     * Find trashed project objects
     *
     * @param User $user
     * @return null
     */
    function findTrashed($user) {
      $type_filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE, PROJECT_STATUS_PAUSED, PROJECT_STATUS_CANCELED, PROJECT_STATUS_COMPLETED));
      if($type_filter) {
      	return ProjectObjects::find(array(
      	  'conditions' => array($type_filter . ' AND state = ? AND visibility >= ?', STATE_DELETED, $user->getVisibility()),
      	  'order'      => 'updated_on'
      	));
      } else {
        return null;
      }
    } // findTrashed
    
    /**
     * Paginate trashed objects
     *
     * @param User $user
     * @param integer $page
     * @param integer $per_page
     * @return null
     */
    function paginateTrashed($user, $page = 1, $per_page = 30) {
      if($user->isAdministrator() || $user->isProjectManager()) {
        return ProjectObjects::paginate(array(
      	  'conditions' => array("state = ? AND visibility >= ?", STATE_DELETED, $user->getVisibility()),
      	  'order'      => 'updated_on'
      	), $page, $per_page);
      } else {
        $type_filter = ProjectUsers::getVisibleTypesFilter($user, array(PROJECT_STATUS_ACTIVE, PROJECT_STATUS_PAUSED, PROJECT_STATUS_CANCELED, PROJECT_STATUS_COMPLETED));
        if($type_filter) {
          return ProjectObjects::paginate(array(
        	  'conditions' => array($type_filter . ' AND state = ? AND visibility >= ?', STATE_DELETED, $user->getVisibility()),
        	  'order'      => 'updated_on'
        	), $page, $per_page);
        } else {
          return array(null, new Pager(1, 0, $per_page));
        } // if
      } // if
    } // paginateTrashed
    
    /**
     * Delete all object by project
     *
     * @param Project $project
     * @return boolean
     */
    function deleteByProject($project) {
      $ids = array();
      $result = db_execute_all('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ?', $project->getId());
      if(is_foreachable($result)) {
        foreach($result as $row) {
          $ids[] = (integer) $row['id'];
        } // foreach
      } // if
      
      return ProjectObjects::cleanUpByIds($ids);
    } // deleteByProject
    
    /**
     * Delete project objects by module
     *
     * @param string $name
     * @return boolean
     */
    function deleteByModule($name) {
      $ids = array();
      
      $result = db_execute_all('SELECT DISTINCT id FROM ' . TABLE_PREFIX . 'project_objects WHERE module = ?', $name);
      if(is_foreachable($result)) {
        foreach($result as $row) {
          $ids[] = (integer) $row['id'];
        } // foreach
      } // if
      
      return ProjectObjects::cleanUpByIds($ids);
    } // deleteByModule
    
    /**
     * Clean up system by object ID-s
     * 
     * This function cleans up project objects recursively. It is also infinite 
     * loop safe because it will filter out ID-s that are already removed
     *
     * @param array $ids
     * @return null
     */
    function cleanUpByIds($ids) {
      static $cleaned_ids = array();
      
      // Remove objects that are already cleaned
      if(is_foreachable($ids)) {
        foreach($ids as $k => $id) {
          if(isset($cleaned_ids[$id]) && $cleaned_ids[$id]) {
            unset($ids[$k]);
          } else {
            $cleaned_ids[$id] = false;
          } // if
        } // foreach
      } // if
      
      if(is_foreachable($ids)) {
        db_begin_work();
        
        Attachments::deleteByProjectObjectIds($ids);
        Subscriptions::deleteByObjectIds($ids);
        Assignments::deleteByObjectIds($ids);
        ActivityLogs::deleteByObjectIds($ids);
        StarredObjects::deleteByObjectIds($ids);
        Reminders::deleteByObjectIds($ids);
        
        search_index_remove($ids, 'ProjectObject');
        
        $rows = db_execute_all('SELECT DISTINCT id FROM ' . TABLE_PREFIX . 'project_objects WHERE parent_id IN (?)', $ids);
        if(is_foreachable($rows)) {
          $subobject_ids = array();
          foreach($rows as $row) {
            $subobject_ids[] = (integer) $row['id'];
          } // foreach
          ProjectObjects::cleanUpByIds($subobject_ids);
        } // if
        
        ProjectObjects::delete(array('id IN (?)', $ids));
        
        foreach($ids as $id) {
          $cleaned_ids[$id] = true;
        } // if
        
        db_commit();
      } // if
      return true;
    } // cleanupByIds
    
    // ---------------------------------------------------
    //  Completable
    // ---------------------------------------------------
    
    /**
     * Return total number of completable objects in a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return integer
     */
    function countCompletableByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      $completable = get_completable_project_object_types();
      if(is_array($completable)) {
        return ProjectObjects::count(array('project_id = ? AND type IN (?) AND state >= ? AND visibility >= ?', $project->getId(), $completable, $min_state, $min_visibility));
      } else {
        return 0;
      } // if
    } // countCompletableByProject
    
    /**
     * Return number of open completable objects in a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return integer
     */
    function countOpenCompletableByProject($project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      $completable = get_completable_project_object_types();
      if(is_array($completable)) {
        return ProjectObjects::count(array('project_id = ? AND type IN (?) AND state >= ? AND visibility >= ? AND completed_on IS NULL', $project->getId(), $completable, $min_state, $min_visibility));
      } else {
        return 0;
      } // if
    } // countOpenCompletableByProject
    
    // ---------------------------------------------------
    //  Group
    // ---------------------------------------------------
    
    /**
     * Group objects by project
     *
     * @param array $objects
     * @return array
     */
    function groupByProject($objects) {
      $result = array();
      
      if(is_foreachable($objects)) {
        $project_ids = objects_array_extract($objects, 'getProjectId');
        if(is_foreachable($project_ids)) {
          $projects = Projects::findByIds($project_ids);
          if(is_foreachable($projects)) {
            foreach($projects as $project) {
              $result[$project->getId()] = array(
                'project' => $project,
                'objects' => array(),
              );
            } // foreach
          } // if
        } // if
        
        foreach($objects as $object) {
          if(isset($result[$object->getProjectId()])) {
            $result[$object->getProjectId()]['objects'][] = $object;
          } // if
        } // foreach
      } // if
      
      return $result;
    } // groupByProject
    
    /**
     * Group objects by milestone
     * 
     * This function will group any group of object by milestone
     *
     * @param array $objects
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function groupByMilestone($objects, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      $result = array();
      
      if(is_foreachable($objects)) {
        $milestone_ids = objects_array_extract($objects, 'getMilestoneId');
        if(is_foreachable($milestone_ids)) {
          $milestones = Milestones::findByIds($milestone_ids, $min_state, $min_visibility);
          if(is_foreachable($milestones)) {
            foreach($milestones as $milestone) {
              $result[$milestone->getId()] = array(
                'milestone' => $milestone,
                'objects' => array(),
              );
            } // foreach
          } // if
        } // if
        
        // For unknown milestone objects
        $result[0] = array(
          'milestone' => null,
          'objects' => array(),
        );
        
        foreach($objects as $object) {
          if(isset($result[$object->getMilestoneId()])) {
            $result[$object->getMilestoneId()]['objects'][] = $object;
          } else {
            $result[0]['objects'][] = $object;
          } // if
        } // foreach
      } // if
      
      return $result;
    } // groupByMilestone
    
    /**
     * Update field properties for child objects by parent
     * 
     * $properties is an array where key is setter name and value is new value
     *
     * @param ProjectObject $parent
     * @param array $properties
     * @param array $types
     * @return boolean
     */
    function updatePropertiesByParent($parent, $properties, $types) {
      if(is_foreachable($properties) && is_foreachable($types)) {
        $objects = ProjectObjects::findBySQL('SELECT * FROM ' . TABLE_PREFIX . 'project_objects WHERE parent_id = ? AND type IN (?)', array($parent->getId(), $types));
        if(is_foreachable($objects)) {
          db_begin_work();
          foreach($objects as $object) {
            foreach($properties as $setter => $value) {
              $object->$setter($value);
            } // if
            $object->save();
          } // foreach
          db_commit();
        } // if
      } // if
      
      return true;
    } // updatePropertiesByParent
    
    /**
     * Returns ids and names of all objects in $project which are type specified in $type arrray
     *
     * @param Project $project
     * @param array $types
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function getIdNameMapForProject($project, $types, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return db_execute('SELECT id, name, type FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type IN (?) AND state >= ? AND visibility >= ? ORDER BY name', $project->getId(), $types, $min_state, $min_visibility);
    } // getIdNameMapForProject
    
    // ---------------------------------------------------
    //  Override BaseProjectObjects methods
    // ---------------------------------------------------
    
    /**
     * Do a SELECT query over database with specified arguments
     * 
     * This function can return single instance or array of instances that match 
     * requirements provided in $arguments associative array
     *
     * @param array $arguments
     * @return mixed
     * @throws DBQueryError
     */
    function find($arguments = null) {
      return Projectobjects::findBySQL(DataManager::prepareSelectFromArguments($arguments, TABLE_PREFIX . 'project_objects'), null, array_var($arguments, 'one'));
    } // find
    
    /**
     * Return paginated set of project objects
     *
     * @param array $arguments
     * @param itneger $page
     * @param integer $per_page
     * @return array
     */
    function paginate($arguments = null, $page = 1, $per_page = 10) {
      if(!is_array($arguments)) {
        $arguments = array();
      } // if
      
      $arguments['limit'] = $per_page;
      $arguments['offset'] = ($page - 1) * $per_page;
      
      $items = ProjectObjects::findBySQL(DataManager::prepareSelectFromArguments($arguments, TABLE_PREFIX . 'project_objects'), null, array_var($arguments, 'one'));
      $total_items = ProjectObjects::count(array_var($arguments, 'conditions'));
      
      return array(
        $items,
        new Pager($page, $total_items, $per_page)
      );
    } // paginate
    
    /**
     * Return object of a specific class by SQL
     *
     * @param string $sql
     * @param array $arguments
     * @param boolean $one
     * @param string $table_name
     * @return array
     */
    function findBySQL($sql, $arguments = null, $one = false) {
      if($arguments !== null) {
        $sql = db_prepare_string($sql, $arguments);
      } // if
      
      $rows = db_execute_all($sql);
      
      if(is_error($rows)) {
        return $rows;
      } // if
      
      if(!is_foreachable($rows)) {
        return null;
      } // if
      
      if($one) {
        $row = $rows[0];
        $item_class = array_var($row, 'type');
        
        $item = new $item_class();
        $item->loadFromRow($row);
        return $item;
      } else {
        $items = array();
        
        foreach($rows as $row) {
          $item_class = array_var($row, 'type');
          
          $item = new $item_class();
          $item->loadFromRow($row);
          $items[] = $item;
        } // foreach
        
        return count($items) ? $items : null;
      } // if
    } // findBySQL
  
    /**
     * Find and return a specific project object by ID
     * 
     * This function will make sure that we ruturn instance of proper class (it 
     * will read the type of the object and construct object of that class)
     *
     * @param mixed $id
     * @return ProjectObject
     */
    function findById($id) {
      if(empty($id)) {
        return null;
      } // if
      
      $cache_id = TABLE_PREFIX . 'project_objects_id_' . $id;
      $row = cache_get($cache_id);
      
      if($row) {
        $object_class = $row['type'];
        
        $object = new $object_class();
        $object->loadFromRow($row);
        
        return $object;
      } else {
        $row = db_execute_one("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE id = ? LIMIT 0, 1", $id);
      
        if(is_array($row)) {
          $object_class = $row['type'];
          
          $object = new $object_class();
          $object->loadFromRow($row, true);
          
          return $object;
        } // if
      } // if
      
      return null;
    } // findById
    
  }

?>