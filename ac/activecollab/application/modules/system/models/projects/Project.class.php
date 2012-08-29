<?php

  /**
   * Project class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Project extends BaseProject {
    
    /**
     * Project leader
     *
     * @var User
     */
    var $leader = false;
    
    /**
     * Users that are assigned to this project
     *
     * @var array
     */
    var $users = false;
    
    /**
     * Cached number of project users
     *
     * @var integer
     */
    var $users_count = false;
    
    /**
     * Instance of user who created this project
     *
     * @var User
     */
    var $created_by = false;
    
    /**
     * List of protected fields (can't be set using setAttributes() method)
     *
     * @var array
     */
  	var $protect = array(
  	  'id', 
  	  'type', 
  	  'status', 
  	  'completed_on', 
  	  'completed_by_id', 
  	  'completed_by_name', 
  	  'completed_by_email', 
  	  'created_on', 
  	  'created_by_id', 
  	  'created_by_name', 
  	  'created_by_email', 
  	  'open_tasks_count', 
  	  'total_tasks_count'
  	);
    
    /**
     * Return project leader
     *
     * @param void
     * @return User
     */
    function getLeader() {
      if($this->leader === false) {
        if($this->getLeaderId()) {
          $this->leader = Users::findById($this->getLeaderId());
        } // if
        
        if(!instance_of($this->leader, 'User')) {
          $this->leader = new AnonymousUser($this->getLeaderName(), $this->getLeaderEmail());
        }
      } // if
      return $this->leader;
    } // getLeader
    
    /**
     * Set leader data
     *
     * @param User $leader
     * @return User
     */
    function setLeader($leader) {
      if(instance_of($leader, 'User')) {
        $this->setLeaderId($leader->getId());
        $this->setLeaderName($leader->getDisplayName());
        $this->setLeaderEmail($leader->getEmail());
      } elseif(instance_of($leader, 'AnonymousUser')) {
        $this->setLeaderId(0);
        $this->setLeaderName($leader->getName());
        $this->setLeaderEmail($leader->getEmail());
      } else {
        return new InvalidInstanceError('$leader', $leader, 'User', '$leader is expected to be an instance of User or AnonymousUser class');
      } // if
      
      $this->leader = false;
      return $leader;
    } // setLeader
    
    /**
     * Cached project group instance
     *
     * @var ProjectGroup
     */
    var $group = false;
    
    /**
     * Get parent project group
     *
     * @param void
     * @return ProjectGroup
     */
    function getGroup() {
      if($this->group === false) {
        if($this->getGroupId() == 0) {
          $this->group = null;
        } else {
          $this->group = ProjectGroups::findById($this->getGroupId());
        } // if
      } // if
      return $this->group;
    } // getGroup
    
    /**
     * Client company
     *
     * @var Company
     */
    var $company = false;
    
    /**
     * Return company instance
     *
     * @param void
     * @return Company
     */
    function getCompany() {
      if($this->company === false) {
        if($this->getCompanyId() == 0) {
          $this->company = null;
        } else {
          $this->company = Companies::findById($this->getCompanyId());
        } // if
      } // if
      return $this->company;
    } // getCompany
    
    /**
     * Return users that are assigned to this project
     *
     * @param void
     * @return array
     */
    function getUsers() {
      if($this->users === false) {
        $this->users = ProjectUsers::findUsersByProject($this);
      } // if
      return $this->users;
    } // getUsers
    
    /**
     * Return number of completed tasks in this project
     *
     * @param void
     * @return integer
     */
    function getCompletedTaskCount() {
      return $this->getTotalTasksCount() - $this->getOpenTasksCount();
    } // getCompletedTaskCount
    
    /**
     * Return number of percents this object is done
     *
     * @param void
     * @return integer
     */
    function getPercentsDone() {
      $total = $this->getTotalTasksCount();
      
      if($total) {
        $completed = $total - $this->getOpenTasksCount();
        return ceil($completed / $total * 100);
      } else {
        return 0;
      } // if
    } // getPercentsDone
    
    /**
     * Return verbose status
     *
     * @param void
     * @return string
     */
    function getVerboseStatus() {
      switch($this->getStatus()) {
        case PROJECT_STATUS_ACTIVE:
          return lang('Active');
        case PROJECT_STATUS_PAUSED:
          return lang('Paused');
        case PROJECT_STATUS_COMPLETED:
          return lang('Completed');
        case PROJECT_STATUS_CANCELED:
          return lang('Canceled');
      } // switch
    } // getVerboseStatus
    
    /**
     * Cached options per user
     *
     * @var array
     */
    var $options = array();
    
    /**
     * Return project options
     *
     * @param User $user
     * @return array
     */
    function getOptions($user) {
      if(!isset($this->options[$user->getId()])) {
        $options = new NamedList();
        
        if($this->canEdit($user)) {
          $options->add('edit', array(
            'url' => $this->getEditUrl(),
            'text' => lang('Edit'),
          ));
          $options->add('edit_status', array(
            'url' => $this->getEditStatusUrl(),
            'text' => lang('Change Status'),
          ));
          $options->add('edit_icon', array(
            'url' => $this->getEditIconUrl(),
            'text' => lang('Change Icon'),
          ));
        } // if
        if($this->canDelete($user)) {
          $options->add('delete', array(
            'text' => lang('Delete'),
            'url' => $this->getDeleteUrl(),
            'method' => 'post',
            'confirm' => lang('Are you sure that you want to delete this project and all related objects? This cannot be undone!'),
          ));
        } // if
        if(PinnedProjects::isPinned($this, $user)) {
          $options->add('pin_unpin', array(
            'text' => lang('Remove from Favorites'),
            'url' => $this->getUnpinUrl(),
            'method' => 'post',
          ));
        } else {
          $options->add('pin_unpin', array(
            'text' => lang('Add to Favorites'),
            'url' => $this->getPinUrl(),
            'method' => 'post',
          ));
        } // if
        
        event_trigger('on_project_options', array(&$options, &$this, &$user));
        $this->options[$user->getId()] = $options;
      } // if
      return $this->options[$user->getId()];
    } // getOptions
    
    /**
     * Mark this object as completed
     *
     * @param User $by
     * @param boolean $canceled
     * @param boolean $save
     * @return boolean
     */
    function complete($by, $canceled = false, $save = true) {
      $status = $canceled ? PROJECT_STATUS_CANCELED : PROJECT_STATUS_COMPLETED;
      
      $old_status = $this->getStatus();
      if($status == $old_status) {
        return true;
      } // if
      
      $this->setStatus($status);
      $this->setCompletedOn(new DateTimeValue());
      $this->setCompletedById($by->getId());
      $this->setCompletedByName($by->getDisplayName());
      $this->setCompletedByEmail($by->getEmail());
      
      if($save) {
        if($old_status == PROJECT_STATUS_ACTIVE || $old_status == PROJECT_STATUS_PAUSED) {
          PinnedProjects::deleteByProject($this);
          ActivityLogs::deleteByProject($this);
          
          event_trigger('on_project_completed', array($this, $by, $status));
        } // if
        
        return $this->save();
      } // if
      
      return true;
    } // complete
    
    /**
     * Mark this object as opened
     *
     * @param boolean $paused
     * @param boolean $save
     * @return boolean
     */
    function reopen($paused = false, $save = true) {
      $status = $paused ? PROJECT_STATUS_PAUSED : PROJECT_STATUS_ACTIVE;
      
      $old_status = $this->getStatus();
      if($old_status == $status) {
        return true;
      } // if
      
      $this->setStatus($status);
      $this->setCompletedOn(null);
      $this->setCompletedById(0);
      $this->setCompletedByName(null);
      $this->setCompletedByEmail(null);
      
      if($save) {
        if($old_status == PROJECT_STATUS_COMPLETED || $old_status == PROJECT_STATUS_CANCELED) {
          event_trigger('on_project_opened', array($this, $status));
        } // if
        
        return $this->save();
      } // if
      
      return true;
    } // reopen
    
    /**
     * Returns true if this project is completed or canceled
     *
     * @param void
     * @return boolean
     */
    function isCompleted() {
      return instance_of($this->getCompletedOn(), 'DateValue');
    } // isCompleted
    
    /**
     * Describe project
     *
     * @param User $user
     * @param array $additional
     * @return array
     */
    function describe($user, $additional = null) {
      $result = array(
        'id' => $this->getId(),
        'name' => $this->getName(),
        'overview' => $this->getOverview(),
        'status' => $this->getStatus(),
        'type' => $this->getType(),
        'permalink' => $this->getOverviewUrl(),
      );
      
      if(array_var($additional, 'describe_leader')) {
        $leader = $this->getLeader();
        if(instance_of($leader, 'User')) {
          $result['leader'] = $leader->describe($user);
        } // if
      } // if
      
      if(!array_key_exists('leader', $result)) {
        $result['leader_id'] = $this->getLeaderId();
      } // if
      
      if(array_var($additional, 'describe_company')) {
        $company = $this->getCompany();
        if(instance_of($company, 'Company')) {
          $result['company'] = $company->describe($user);
        } // if
      } // if
      
      if(!array_key_exists('company', $result)) {
        $result['company_id'] = $this->getCompanyId();
      } // if
      
      if(array_var($additional, 'describe_group')) {
        $group = $this->getGroup();
        if(instance_of($group, 'ProjectGroup')) {
          $result['group'] = $group->describe($user);
        } // if
      } // if
      
      if(!array_key_exists('group', $result)) {
        $result['group_id'] = $this->getGroupId();
      } // if
      
      if(array_var($additional, 'describe_permissions')) {
        $logged_user_permissions = array(
          'role' => null,
          'permissions' => array(),
        );
        
        $permissions = array_keys(Permissions::findProject());
        if($user->isAdministrator()) {
          $logged_user_permissions['role'] = 'administrator';
        } elseif($user->isProjectManager()) {
          $logged_user_permissions['role'] = 'project-manager';
        } elseif($user->isProjectLeader($this)) {
          $logged_user_permissions['role'] = 'project-leader';
        } // if
        
        if($logged_user_permissions['role'] === null) {
          $project_role = $user->getProjectRole($this);
          if(instance_of($project_role, 'Role')) {
            $logged_user_permissions['role'] = $project_role->getId();
          } else {
            $logged_user_permissions['role'] = 'custom';
          } // if
          
          foreach($permissions as $permission) {
            $logged_user_permissions['permissions'][$permission] = (integer) $user->getProjectPermission($permission, $this);
          } // foreach
        } else {
          foreach($permissions as $permission) {
            $logged_user_permissions['permissions'][$permission] = PROJECT_PERMISSION_MANAGE;
          } // foreach
        } // if
        
        $result['logged_user_permissions'] = $logged_user_permissions;
      } // if
      
      if(array_var($additional, 'describe_icon')) {
        $result['icon_url'] = $this->getIconUrl(true);
      } // if
      
      return $result;
    } // describe
    
    /**
     * Copy project items into a destination project
     *
     * @param Project $to
     * @return null
     */
    function copyItems(&$to) {
      
      // Prepare time diff
      $source_starts_on = $this->getStartsOn();
      if(!instance_of($source_starts_on, 'DateValue')) {
        $source_starts_on = $this->getCreatedOn();
      } // if
      
      $target_starts_on = $to->getStartsOn();
      if(!instance_of($target_starts_on, 'DateValue')) {
        $target_starts_on = $to->getCreatedOn();
      } // if
      
      $diff = $target_starts_on->getTimestamp() - $source_starts_on->getTimestamp();
      
      // Migrate project users
    	$project_users = ProjectUsers::findByProject($this);
    	if(is_foreachable($project_users)) {
    	  foreach($project_users as $project_user) {
    	    if($to->getLeaderId() != $project_user->getUserId()) {
    	      $user = $project_user->getUser();
    	      if(instance_of($user, 'User')) {
    	        $to->addUser($user, $project_user->getRole(), $project_user->getPermissions());
    	      } // if
    	    } // if
    	  } // foreach
    	} // if
      
      // We need to move milestones in order to get milestones map
      $milestones_map = null;
      $milestones = Milestones::findAllByProject($this, VISIBILITY_PRIVATE);
      if(is_foreachable($milestones)) {
        $milestones_map = array();
        foreach($milestones as $milestone) {
          $copied_milestone = $milestone->copyToProject($to);
          if(instance_of($copied_milestone, 'Milestone')) {            
            $copied_milestone->advance($diff, true);
            $milestones_map[$milestone->getId()] = $copied_milestone;
          } // if
        } // foreach
      } // if
      
      // Now move categories
      $categories_map  = null;
      
      $categories = Categories::findByProject($this);
      if(is_foreachable($categories)) {
        foreach($categories as $category) {
          $copied_category = $category->copyToProject($to, null, null, false);
          if(instance_of($copied_category, 'Category')) {
            $categories_map[$category->getId()] = $copied_category;
          } // if
        } // foreach
      } // if
      
      // Let the modules to their thing
      event_trigger('on_copy_project_items', array(&$this, &$to, $milestones_map, $categories_map));
      
      // Now, lets update due dates
      $completable_types = get_completable_project_object_types();
      if(is_foreachable($completable_types)) {
        foreach($completable_types as $k => $type) {
          if(strtolower($type) == 'milestone') {
            unset($completable_types[$k]);
          } // if
        } // foreach
        
        if(count($completable_types) > 0) {
          $rows = db_execute_all('SELECT id, due_on FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type IN (?) AND due_on IS NOT NULL', $to->getId(), $completable_types);
          if(is_foreachable($rows)) {
            foreach($rows as $row) {
              $id = (integer) $row['id'];
              $new_date = date(DATE_MYSQL, strtotime($row['due_on']) + $diff);
              
              db_execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET due_on = ? WHERE id = ?', $new_date, $id);
              cache_remove("acx_project_objects_id_$id");
            } // foreach
          } // if
        } // if
      } // if
      
      // Refresh tasks count, just in case...
      $to->refreshTasksCount();
    } // copyItems
    
    /**
     * Prepare project for display
     *
     * @param boolean $make_clickable
     * @return string
     */
    function getFormattedOverview($make_clickable = true) {
      $overview = $this->getOverview();
      if($overview) {
        if($make_clickable) {
          require_once SMARTY_PATH . '/plugins/modifier.clickable.php';
          $overview = smarty_modifier_clickable($overview);
        } // if
        
        return nl2br_pre($overview);
      } // if
      
      return $overview;
    } // getFormattedBody
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Check if $user can add new project
     *
     * @param User $user
     * @return boolean
     */
    function canAdd($user) {
      return $user->isAdministrator() || $user->isProjectManager() || $user->getSystemPermission('add_project');
    } // canAdd
    
    /**
     * Can edit project properties
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
      return $user->isProjectLeader($this) || $user->isProjectManager() || $user->isAdministrator();
    } // canEdit
    
    /**
     * Can use delete this object
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      return $user->isProjectManager() || $user->isAdministrator();
    } // canDelete
    
    // ---------------------------------------------------
    //  Icon
    // ---------------------------------------------------
    
    /**
     * Large icon URL
     *
     * @var boolean
     */
    var $large_icon_url = false;
    
    /**
     * Small icon URL
     *
     * @var boolean
     */
    var $small_icon_url = false;
    
    /**
     * Get Icon URL
     *
     * @param boolean $large
     * @return string
     */
    function getIconUrl($large = false) {
      if($this->large_icon_url === false || $this->small_icon_url === false) {
        list($this->large_icon_url, $this->small_icon_url) = get_project_icon_urls($this);
      } // if
      
      return $large ? $this->large_icon_url : $this->small_icon_url;
    } // getAvatarUrl
    
    /**
     * Get Icon Path 
     *
     * @param boolean $large
     * @return string
     */
    function getIconPath($large = false) {
      $size = $large ? '40x40' : '16x16';
      return ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/projects_icons/' . $this->getId() . ".$size.gif";
    } // getAvatarPath
    
    // ---------------------------------------------------
    //  Project users and permissions
    // ---------------------------------------------------
    
    /**
     * Add user to this project
     *
     * @param User $user
     * @param Role $role
     * @param array $permissions
     * @return null
     */
    function addUser($user, $role = null, $permissions = null) {
      $project_user = ProjectUsers::findById(array(
        'user_id' => $user->getId(),
        'project_id' => $this->getId(),
      ));
      
      if(!instance_of($project_user, 'ProjectUser')) {
        $project_user = new ProjectUser();
      } // if
      
      $project_user->setUserId($user->getId());
      $project_user->setProjectId($this->getId());
      
      if(instance_of($role, 'Role')) {
        $project_user->setRoleId($role->getId());
        $project_user->setPermissions(null);
      } else {
        $project_user->setRoleId(0);
        $project_user->setPermissions($permissions);
      } // if
      
      $save = $project_user->save();
      if($save && !is_error($save)) {
        clean_user_permissions_cache($user);
        event_trigger('on_project_user_added', array($this, $user, $role, $permissions));
        return true;
      } else {
        return $save;
      } // if
    } // addUser
    
    /**
     * Update user permissions
     *
     * @param User $user
     * @param Role $role
     * @param array $permissions
     * @return boolean
     */
    function updateUserPermissions($user, $role = null, $permissions = null) {
      $project_user = ProjectUsers::findById(array(
        'user_id'    => $user->getId(),
        'project_id' => $this->getId(),
      ));
      
      if(!instance_of($project_user, 'ProjectUser')) {
        return false;
      } // if
      
      db_begin_work();
      
      if(instance_of($role, 'Role')) {
        $project_user->setRoleId($role->getId());
        $project_user->setPermissions(null);
      } else {
        $project_user->setRoleId(0);
        $project_user->setPermissions($permissions);
      } // if
      
      $save = $project_user->save();
      if($save && !is_error($save)) {
        db_commit();
        
        clean_user_permissions_cache($user);
        event_trigger('on_project_user_updated', array($this, $user, $role, $permissions));
        return true;
      } else {
        db_rollback();
        return false;
      } // if
    } // updateUserPermissions
    
    /**
     * Remove given user from this project
     *
     * @param User $user
     * @return boolean
     */
    function removeUser($user) {
      $project_user = ProjectUsers::findById(array(
        'user_id'    => $user->getId(),
        'project_id' => $this->getId(),
      ));
      
      if(instance_of($project_user, 'ProjectUser')) {
        db_begin_work();
        
        $delete = $project_user->delete();
        if($delete && !is_error($delete)) {
          clean_user_permissions_cache($user);
          event_trigger('on_project_user_removed', array($this, $user));
          
          db_commit();
          return true;
        } else {
          db_rollback();
          return $delete;
        } // if
      } // if
      
      return true;
    } // removeUser
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return overview URL for this project
     *
     * @param void
     * @return string
     */
    function getOverviewUrl() {
      return assemble_url('project_overview', array('project_id' => $this->getId()));
    } // getOverviewUrl
    
    /**
     * Return edit project URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('project_edit', array('project_id' => $this->getId()));
    } // getEditUrl
    
    /**
     * Return delete project URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('project_delete', array('project_id' => $this->getId()));
    } // getDeleteUrl
    
    /**
     * Return people URL
     *
     * @param void
     * @return string
     */
    function getPeopleUrl() {
      return assemble_url('project_people', array('project_id' => $this->getId()));
    } // getPeopleUrl
    
    /**
     * Return change project status URL
     *
     * @param void
     * @return string
     */
    function getEditStatusUrl() {
      return assemble_url('project_edit_status', array('project_id' => $this->getId()));
    } // getEditStatusUrl
    
    /**
     * get edit user avatar URL
     *
     * @param void
     * @return string
     */
    function getEditIconUrl() {
      return assemble_url('project_edit_icon', array('project_id' => $this->getId()));
    } // getEditAvatarUrl
    
    /**
     * Get Delete Avatar URL
     *
     * @param void
     * @return string
     */
    function getDeleteIconUrl() {
    	return assemble_url('project_delete_icon', array('project_id' => $this->getId()));
    } // getDeleteAvatarUrl
    
    /**
     * Return add people URL
     *
     * @param void
     * @return string
     */
    function getAddPeopleUrl() {
      return assemble_url('project_people_add', array('project_id' => $this->getId()));
    } // getAddPeopleUrl
    
    /**
     * Return remove user URL
     *
     * @param User $user
     * @return string
     */
    function getRemoveUserUrl($user) {
      return assemble_url('project_remove_user', array(
        'project_id' => $this->getId(),
        'user_id' => $user->getId(),
      ));
    } // getRemoveUserUrl
    
    /**
     * Return URL of user permissions page
     *
     * @param User $user
     * @return string
     */
    function getUserPermissionsUrl($user) {
      return assemble_url('project_user_permissions', array(
        'project_id' => $this->getId(),
        'user_id' => $user->getId(),
      ));
    } // getUserPermissionsUrl
    
    /**
     * Return pin project URL
     *
     * @param void
     * @return string
     */
    function getPinUrl() {
      return assemble_url('project_pin', array('project_id' => $this->getId()));
    } // getPinUrl
    
    /**
     * Return unpin project URL
     *
     * @param void
     * @return string
     */
    function getUnpinUrl() {
      return assemble_url('project_unpin', array('project_id' => $this->getId()));
    } // getUnpinUrl
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Set field value
     * 
     * If we are setting body purifier will be included and value will be ran 
     * through it. Else we will simply inherit behavior
     *
     * @param string $field
     * @param mixed $value
     * @return string
     */
    function setFieldValue($field, $value) {
      if(!$this->is_loading && ($field == 'overview')) {
        $value = prepare_html($value, true);
      } // if
      
      return parent::setFieldValue($field, $value);
    } // setFieldValue
    
    /**
     * This function will refresh number of total / open tasks
     *
     * @param void
     * @return boolean
     */
    function refreshTasksCount() {
      $this->setTotalTasksCount(ProjectObjects::countCompletableByProject($this, STATE_VISIBLE, VISIBILITY_PRIVATE));
      $this->setOpenTasksCount(ProjectObjects::countOpenCompletableByProject($this, STATE_VISIBLE, VISIBILITY_PRIVATE));
      return $this->save();
    } // refreshTasksCount
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
  
    /**
     * Validate model object before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if(!$this->validatePresenceOf('leader_id') && !$this->validatePresenceOf('leader_name') && !$this->validatePresenceOf('leader_email')) {
        $errors->addError(lang('Project leader is required'), 'leader_id');
      } // if
      
      if(!$this->validatePresenceOf('name', 3)) {
        $errors->addError(lang('Project name is required. Min length is 3 letters'), 'name');
      } // if
    } // validate
    
    /**
     * Save project
     * 
     * $template is used when project is created to indicate wether project is 
     * being created from template or not
     *
     * @param Project $template
     * @return boolean
     */
    function save($template = null) {
      $modified_fields = $this->modified_fields;
      
      $is_new = $this->isNew();
      event_trigger('on_before_save_project', array('project' => &$this));
      
      $save = parent::save();
      if($save && !is_error($save)) {
        if($is_new) {
          // Add leader to project
          $project_user = new ProjectUser();
          $project_user->setProjectId($this->getId());
          $project_user->setUserId($this->getLeaderId());
          $project_user->save();
          
          clean_project_permissions_cache($this);
          event_trigger('on_project_created', array(&$this, &$template));
        } else {
          clean_project_permissions_cache($this);
          event_trigger('on_project_updated', array(&$this));
        } // if
        
        if(in_array('name', $modified_fields) || in_array('overview', $modified_fields)) {
          $content = $this->getName();
          if($overview = $this->getOverview()) {
            $content .= "\n\n" . $overview;
          } // if
          
          search_index_set($this->getId(), 'Project', $content);
        } // if
      } // if
      
      return $save;
    } // save
    
    /**
     * Delete project and all realted data
     *
     * @param void
     * @return null
     */
    function delete() {
      db_begin_work();
      
      $delete = parent::delete();
      if($delete && !is_error($delete)) {
        ProjectObjects::deleteByProject($this);
        ProjectUsers::deleteByProject($this);
        PinnedProjects::deleteByProject($this);
        
        search_index_remove($this->getId(), 'Project');
        
        clean_project_permissions_cache($this);
        event_trigger('on_project_deleted', array($this));
        
        db_commit();
      } else {
        db_rollback();
      } // if
      
      return $delete;
    } // delete
  
  }

?>