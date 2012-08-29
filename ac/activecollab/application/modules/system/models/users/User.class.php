<?php

  /**
   * User class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class User extends BaseUser {
    
    /**
     * List of protected fields (can't be set using setAttributes() method)
     *
     * @var array
     */
  	var $protect = array(
  	  'session_id', 
  	  'token', 
  	  'last_login_on', 
  	  'last_visit_on', 
  	  'last_activity_on', 
  	  'auto_assign', 
  	  'auto_assign_role_id', 
  	  'auto_assign_permissions', 
  	  'password_reset_key', 
  	  'password_reset_on'
  	);
    
    /**
     * Return users display name
     *
     * @param boolean $short
     * @return string
     */
    function getName($short = false) {
      return $this->getDisplayName($short);
    } // getName
    
    /**
     * Return first name
     * 
     * If $force_value is true and first name value is not present, system will 
     * use email address part before @domain.tld 
     *
     * @param boolean $force_value
     * @return string
     */
    function getFirstName($force_value = false) {
      $result = parent::getFirstName();
      if(empty($result) && $force_value) {
        $email = $this->getEmail();
        return substr_utf($email, 0, strpos_utf($email, '@'));
      } // if
      return $result;
    } // getFirstName
    
    /**
     * Parent company
     * 
     * @var Company
     */
    var $company = false;
    
    /**
     * Return parent company
     *
     * @param void
     * @return Company
     */
    function getCompany() {
      if($this->company === false) {
        $this->company = Companies::findById($this->getCompanyId());
      } // if
      return $this->company;
    } // getCompany
    
    /**
     * Return company name
     *
     * @param void
     * @return string
     */
    function getCompanyName() {
    	$company = $this->getCompany();
    	return instance_of($company, 'Company') ? $company->getName() : lang('-- Unknown --');
    } // getCompanyName
    
    /**
     * Cached user role
     *
     * @var Role
     */
    var $role = false;
    
    /**
     * Return user role
     *
     * @param void
     * @return Role
     */
    function getRole() {
      if($this->role === false) {
        $this->role = $this->getRoleId() > 0 ? Roles::findById($this->getRoleId()) : null;
      } // if
      return $this->role;
    } // getRole
    
    /**
     * Return language for given user
     *
     * @var Language
     */
    var $language = false;
    
    /**
     * Return users language
     *
     * @param void
     * @return Language
     */
    function getLanguage() {
      if(!LOCALIZATION_ENABLED) {
        return null;
      } // if
      
      if($this->language === false) {
        $language_id = UserConfigOptions::getValue('language', $this);
        $this->language = $language_id ? Languages::findById($language_id) : null;
      } // if
      
      return $this->language;
    } // getLanguage
    
    /**
     * Cached array of all user projects
     *
     * @var array
     */
    var $projects = false;
    
    /**
     * Return all projects this user can access
     *
     * @param void
     * @return array
     */
    function getProjects() {
      if($this->projects === false) {
        if($this->isAdministrator() || $this->isProjectManager()) {
          $this->projects = Projects::findAll();
        } else {
          $this->projects = Projects::findByUser($this);
        } // if
      } // if
      return $this->projects;
    } // getProjects
    
    /**
     * Cached array of active projects
     *
     * @var array
     */
    var $active_projects = false;
    
    /**
     * Return all active project this user is involved in
     *
     * @param boolean $pinned_first
     * @return array
     */
    function getActiveProjects($pinned_first = false) {
      if($this->active_projects === false) {
        $this->active_projects = Projects::findByUser($this, PROJECT_STATUS_ACTIVE);
      } // if
      
      if($pinned_first) {
        if(is_foreachable($this->active_projects)) {
          $pinned = array();
          $not_pinned = array();
          
          foreach($this->active_projects as $active_project) {
            if(PinnedProjects::isPinned($active_project, $this)) {
              $pinned[] = $active_project;
            } else {
              $not_pinned[] = $active_project;
            } // if
          } // foreach
          
          if(count($pinned) && count($not_pinned)) {
            return array_merge($pinned, $not_pinned);
          } elseif(count($pinned)) {
            return $pinned;
          } elseif(count($not_pinned)) {
            return $not_pinned;
          } else {
            return null;
          } // if
          
        } else {
          return null;
        } // if
      } else {
        return $this->active_projects;
      } // if
    } // getActiveProjects
    
    /**
     * Cached display name
     *
     * @var string
     */
    var $display_name = false;
    
    /**
     * Return display name (first name and last name)
     *
     * @param boolean $short
     * @return string
     */
    function getDisplayName($short = false) {
      if($this->display_name === false) {
        if($this->getFirstName() && $this->getLastName()) {
          if($short) {
            return $this->getFirstName() . ' ' . substr_utf($this->getLastName(), 0, 1) . '.';
          } // if
          
          $this->display_name = $this->getFirstName() . ' ' . $this->getLastName();
        } elseif($this->getFirstName()) {
          $this->display_name = $this->getFirstName();
        } elseif($this->getLastName()) {
          $this->display_name = $this->getLastName();
        } else {
          $this->display_name = $this->getEmail();
        } // if
      } // if
      
      return $this->display_name;
    } // getDisplayName
    
    /**
     * Cached list of user options (indexed by user)
     *
     * @var array
     */
    var $options = array();
    
    /**
     * Return array of this $user can do to this user account
     *
     * @param User $user
     * @return array
     */
    function getOptions($user) {
      if(!isset($this->options[$user->getId()])) {
        $options = new NamedList();
        
        if($this->canChangeRole($user)) {
          $options->add('edit_company_and_role', array(
            'text' => lang('Company and Role'),
            'url'  => $this->getEditCompanyAndRoleUrl(),
          ));
        } // if
        
      	if($this->canEdit($user)) {
          $options->add('edit_profile', array(
            'text' => lang('Update Profile'),
            'url'  => $this->getEditProfileUrl(),
          ));
          
          $options->add('edit_settings', array(
            'text' => lang('Change Settings'),
            'url'  => $this->getEditSettingsUrl(),
          ));
          
          $options->add('edit_password', array(
            'text' => lang('Change Password'),
            'url'  => $this->getEditPasswordUrl(),
          ));
          
          $options->add('edit_avatar', array(
            'text' => lang('Change Avatar'),
            'url'  => $this->getEditAvatarUrl(),
          ));
          
          $options->add('api', array(
            'text' => lang('API Settings'),
            'url'  => $this->getApiSettingsUrl(),
          ));
        } // if
        
        if($this->canDelete($user)) {
          $options->add('delete', array(
            'text'    => lang('Delete'),
            'url'     => $this->getDeleteUrl(),
            'method'  => 'post',
            'confirm' => lang('Are you sure that you want to delete this user account? There is no undo!'),
          ));
        } // if
        
        if($user->isProjectManager()) {
          $options->add('add_to_projects', array(
            'text' => lang('Add to Projects'),
            'url'  => $this->getAddToProjectsUrl(),
          ));
        } // if
        
        if($this->canSendWelcomeMessage($user)) {
          $options->add('send_welcome_message', array(
            'text' => lang('Send Welcome Message'),
            'url'  => $this->getSendWelcomeMessageUrl(),
          ));
        } // if
        
        if($this->canViewActivities($user)) {
        	$options->add('recent_activities', array(
            'text' => lang('Recent Activities'),
            'url'  => $this->getRecentActivitiesUrl(),
          ));
        } // if
        
        event_trigger('on_user_options', array(&$this, &$options, &$user));
        $this->options[$user->getId()] = $options;
      } // if
      return $this->options[$user->getId()];
    } // getOptions
    
    /**
     * Cached quick options
     *
     * @var array
     */
    var $quick_options = array();
    
    /**
     * Return quick user options
     *
     * @param User $user
     * @return array
     */
    function getQuickOptions($user) {
      if(!isset($this->quick_options[$user->getId()])) {
        $options = new NamedList();
        
      	if($this->canEdit($user)) {
          $options->add('edit_profile', array(
            'text' => lang('Update Profile'),
            'url'  => $this->getEditProfileUrl(),
          ));
          
          $options->add('edit_settings', array(
            'text' => lang('Change Settings'),
            'url'  => $this->getEditSettingsUrl(),
          ));
          
          $options->add('edit_password', array(
            'text' => lang('Change Password'),
            'url'  => $this->getEditPasswordUrl(),
          ));
          
          $options->add('api', array(
            'text' => lang('API Settings'),
            'url'  => $this->getApiSettingsUrl(),
          ));
        } // if
        
        event_trigger('on_user_quick_options', array(&$this, &$options, &$user));
        $this->quick_options[$user->getId()] = $options;
      } // if
      return $this->quick_options[$user->getId()];
    } // getQuickOptions
    
    /**
     * Raw password value before it is encoded
     *
     * @var string
     */
    var $raw_password = false;
    
    /**
     * Set field value
     *
     * @param string $field
     * @param mixed $value
     * @return mixed
     */
    function setFieldValue($field, $value) {
      if($field == 'password' && !$this->is_loading) {
        $this->raw_password = $value;
        $this->resetToken();
        
        $value = sha1($value);
      } // if
      return parent::setFieldValue($field, $value);
    } // setFieldValue
    
    /**
     * Returns true if we have a valid password
     *
     * @param string $password
     * @return boolean
     */
    function isCurrentPassword($password) {
      return $this->getPassword() === sha1($password);
    } // isCurrentPassword
    
    /**
     * Generate new token for this user
     *
     * @param void
     * @return null
     */
    function resetToken() {
      $this->setToken(make_string(40));
    } // resetToken
    
    /**
     * Cached array of visible user ID-s
     *
     * @var array
     */
    var $visible_user_ids = false;
    
    /**
     * Returns an array of visible users
     *
     * @param void
     * @return array
     */
    function visibleUserIds() {
      if($this->visible_user_ids === false) {
        $this->visible_user_ids = Users::findVisibleUserIds($this);
      } // if
      return $this->visible_user_ids;
    } // visibleUserIds
    
    /**
     * Cached array of visible company ID-s
     *
     * @var array
     */
    var $visible_company_ids = false;
    
    /**
     * Returns array of companies this user can see
     *
     * @param void
     * @return array
     */
    function visibleCompanyIds() {
      if($this->visible_company_ids === false) {
        $this->visible_company_ids = Users::findVisibleCompanyIds($this);
      } // if
      return $this->visible_company_ids;
    } // visibleCompanyIds
    
    /**
     * Describe user
     *
     * @param User $user
     * @param array $additional
     * @return array
     */
    function describe($user, $additional = null) {
      $result = array(
        'id'                 => $this->getId(),
        'first_name'         => $this->getFirstName(),
        'last_name'          => $this->getLastName(),
        'email'              => $this->getEmail(),
        'last_visit_on'      => $this->getLastVisitOn(),
        'permalink'          => $this->getViewUrl(),
        'role_id'            => $this->getRoleId(),
        'is_administrator'   => $this->isAdministrator(),
        'is_project_manager' => $this->isProjectManager(),
        'is_people_manager'  => $this->isPeopleManager(),
      );
      
      if($user->isAdministrator() || $user->isPeopleManager()) {
        $result['token'] = $this->getToken();
      } // if
      
      if(array_var($additional, 'describe_company')) {
        $company = $this->getCompany();
        if(instance_of($company, 'Company')) {
          $result['company'] = $company->describe($user);
        } // if
      } // if
      
      if(!isset($result['company'])) {
        $result['company_id'] = $this->getCompanyId();
      } // if
      
      if(array_var($additional, 'describe_avatar')) {
        $result['avatar_url'] = $this->getAvatarUrl(true);
      } // if
      
      return $result;
    } // describe
    
    /**
     * Prefered locale
     *
     * @var string
     */
    var $locale = false;
    
    /**
     * Return prefered locale
     *
     * @param string $default
     * @return string
     */
    function getLocale($default = null) {
    	if($this->locale === false) {
    	  $language_id = UserConfigOptions::getValue('language', $this);
    	  if($language_id) {
    	    $language = Languages::findById($language_id);
    	    if(instance_of($language, 'Language')) {
    	      $this->locale = $language->getLocale();
    	    } // if
    	  } // if
    	  
    	  if($this->locale === false) {
    	    $this->locale = $default === null ? BUILT_IN_LOCALE : $default;
    	  } // if
    	} // if
    	
    	return $this->locale;
    } // getLocale
    
    /**
     * Cached last visit on value
     *
     * @var DateTimeValue
     */
    var $last_visit_on = false;
    
    /**
     * Return users last visit
     *
     * @param void
     * @return DateTimeValue
     */
    function getLastVisitOn() {
      if($this->last_visit_on === false) {
      	$last_visit = parent::getLastVisitOn();
      	$this->last_visit_on = instance_of($last_visit, 'DateTimeValue') ? $last_visit : new DateTimeValue(filectime(ENVIRONMENT_PATH . '/config/config.php'));
      } // if
      return $this->last_visit_on;
    } // getLastVisitOn
    
    /**
     * Return token
     *
     * @param boolean $include_user_id
     * @return string
     */
    function getToken($include_user_id = false) {
      return $include_user_id ? $this->getId() . '-' . parent::getToken() : parent::getToken();
    } // getToken
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Returns true if this user have permissions to see private objects
     *
     * @param void
     * @return boolean
     */
    function canSeePrivate() {
    	return $this->isProjectManager() || (boolean) $this->getSystemPermission('can_see_private_objects');
    } // canSeePrivate
    
    /**
     * Cached values of can see milestones permissions
     *
     * @var array
     */
    var $can_see_milestones = array();
    
    /**
     * Returns true if user can see milestones in $project
     *
     * @param Project $project
     * @return boolean
     */
    function canSeeMilestones($project) {
      $project_id = $project->getId();
    	if(!isset($this->can_see_milestones[$project_id])) {
    	  $this->can_see_milestones[$project_id] = $this->getProjectPermission('milestone', $project) >= PROJECT_PERMISSION_ACCESS;
    	} // if
    	return $this->can_see_milestones[$project_id];
    } // canSeeMilestones
    
    /**
     * Is this user member of owner company
     *
     * @var boolean
     */
    var $is_owner = null;
    
    /**
     * Returns true if this user is member of owner company
     *
     * @param void
     * @return boolean
     */
    function isOwner() {
      if($this->is_owner === null) {
        $company = $this->getCompany();
        $this->is_owner = instance_of($company, 'Company') ? $company->getIsOwner() : false;
      } // if
      return $this->is_owner;
    } // isOwner
    
    /**
     * Does this user have administration permissions
     *
     * @var boolean
     */
    var $is_administrator = null;
    
    /**
     * Returns true only if this person has administration permissions
     *
     * @param void
     * @return boolean
     */
    function isAdministrator() {
      if($this->is_administrator === null) {
        $this->is_administrator = $this->getSystemPermission('admin_access');
      } // if
      return $this->is_administrator;
    } // isAdministrator
    
    /**
     * Check if this user is the only administrator
     *
     * @param void
     * @return boolean
     */
    function isOnlyAdministrator() {
      return $this->isAdministrator() && (Users::countAdministrators() == 1);
    } // isOnlyAdministrator
    
    /**
     * Cached is people manager permission value
     *
     * @var boolean
     */
    var $is_people_manager = null;
    
    /**
     * Returns true if this user has management permissions in People section
     *
     * @param void
     * @return boolean
     */
    function isPeopleManager() {
      if($this->is_people_manager === null) {
        if($this->isAdministrator()) {
          $this->is_people_manager = true;
        } else {
          $this->is_people_manager = $this->getSystemPermission('people_management');
        } // if
      } // if
      return $this->is_people_manager;
    } // isPeopleManager
    
    /**
     * Cached value of is project manager permissions
     *
     * @var boolean
     */
    var $is_project_manager = null;
    
    /**
     * Returns true if this user has global project management permissions
     *
     * @param void
     * @return boolean
     */
    function isProjectManager() {
      if($this->is_project_manager === null) {
        if($this->isAdministrator()) {
          $this->is_project_manager = true;
        } else {
          $this->is_project_manager = $this->getSystemPermission('project_management');
        } // if
      } // if
      return $this->is_project_manager;
    } // isProjectManager
    
    /**
     * Returns true if this user is part of a specific project
     *
     * @param Project $project
     * @param boolean $use_cache
     * @return boolean
     */
    function isProjectMember($project, $use_cache = true) {
      return ProjectUsers::isProjectMember($this, $project, $use_cache);
    } // isProjectMember
    
    /**
     * Check if this user is manager of a given company
     * 
     * If $company is missing user will be checked agains his own company
     *
     * @param Company $company
     * @return boolean
     */
    function isCompanyManager($company) {
      if($this->isAdministrator() || $this->isPeopleManager()) {
        return true;
      } // if
      
      return $this->getCompanyId() == $company->getId() && $this->getSystemPermission('manage_company_details');
    } // isCompanyManager
    
    /**
     * Returns true if this person is leader of specified project
     *
     * @param Project $project
     * @return boolean
     */
    function isProjectLeader($project) {
      return $this->getId() == $project->getLeaderId();
    } // isProjectLeader
    
    /**
     * Cached visibility
     *
     * @var boolean
     */
    var $visibility = false;
    
    /**
     * Returns optimal visibility for this user
     * 
     * If this user is member of owner company he will be able to see private 
     * objects. If not he will be able to see only normal and public objects
     *
     * @param void
     * @return boolean
     */
    function getVisibility() {
      if($this->visibility === false) {
        $this->visibility = $this->canSeePrivate() ? VISIBILITY_PRIVATE : VISIBILITY_NORMAL;
      } // if
      return $this->visibility;
    } // getVisibility
    
    /**
     * Return system permission value
     *
     * @param string $name
     * @return boolean
     */
    function getSystemPermission($name) {
    	$role = $this->getRole();
      if(instance_of($role, 'Role')) {
        return (boolean) $role->getPermissionValue($name);
      } else {
        return false;
      } // if
    } // getSystemPermission
    
    // ---------------------------------------------------
    //  Project roles and permissions
    // ---------------------------------------------------
    
    /**
     * Cached project user instances
     *
     * @var array
     */
    var $project_users = array();
    
    /**
     * Return project user instance for this user and $project
     *
     * @param Project $project
     * @return ProjectUser
     */
    function getProjectUserInstance($project) {
      $project_id = $project->getId();
      if(!array_key_exists($project_id, $this->project_users)) {
        $this->project_users[$project->getId()] = ProjectUsers::findById(array(
      	  'user_id' => $this->getId(),
      	  'project_id' => $project->getId(),
      	));
      } // if
      return $this->project_users[$project->getId()];
    } // getProjectUserInstance
    
    /**
     * Return role this user has on a project
     * 
     * If project is administrator, project manager or project leader NULL is 
     * returned
     *
     * @param Project $project
     * @return Role
     */
    function getProjectRole($project) {
      $project_user = $this->getProjectUserInstance($project);
      return instance_of($project_user, 'ProjectUser') ? $project_user->getRole() : null;
    } // getProjectRole
    
    /**
     * Return verbose project role that this user have on $project
     *
     * @param Project $project
     * @return string
     */
    function getVerboseProjectRole($project) {
      if($this->isProjectLeader($project)) {
        return lang('Project Leader');
      } else if($this->isAdministrator()) {
        return lang('System Administrator');
      } elseif($this->isProjectManager()) {
        return lang('Project Manager');
      } else {
        $role = $this->getProjectRole($project);
        if(instance_of($role, 'Role')) {
          return $role->getname();
        } else {
          return lang('Custom');
        } // if
      } // if
    } // getVerboseProjectRole
    
    /**
     * Return project value
     *
     * @param string $name
     * @param Project $project
     * @return integer
     */
    function getProjectPermission($name, $project) {
      if($this->isAdministrator() || $this->isProjectManager() || $this->isProjectLeader($project)) {
        return PROJECT_PERMISSION_MANAGE;
      } // if
    	
    	$project_user = $this->getProjectUserInstance($project);
    	return instance_of($project_user, 'ProjectUser') ? $project_user->getPermissionValue($name) : PROJECT_PERMISSION_NONE;
    } // getProjectPermission
    
    /**
     * Return config option value
     *
     * @param string $name
     * @return mixed
     */
    function getConfigValue($name) {
      return UserConfigOptions::getValue($name, $this);
    } // getConfigValue
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Check if $user can view recent activities of the selected user
     *
     * @param User $user
     * @return boolean
     */
    function canViewActivities($user) {
    	return $user->isAdministrator() || $user->isProjectManager();
    } // canViewActivities
    
    /**
     * Can a specific user create a new user account in given company
     *
     * @param User $user
     * @param Company $to_company
     * @return boolean
     */
    function canAdd($user, $to_company) {
      return $user->isAdministrator() || $user->isPeopleManager() || $user->isCompanyManager($to_company);
    } // canAdd
    
    /**
     * Check if $user can update this profile
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
      if($user->getId() == $this->getId()) {
        return true; // user can change his own account
      } // if
      
      return $user->isCompanyManager($this->getCompany());
    } // canEdit
    
    /**
     * Returns true if $user can change this users role
     *
     * @param User $user
     * @return boolean
     */
    function canChangeRole($user) {
    	return $user->isAdministrator() || $user->isPeopleManager();
    } // canChangeRole
    
    /**
     * Check if $user can delete this profile
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      if($this->getId() == $user->getId()) {
        return false; // user cannot delete himself
      } // if
      
      if($this->isAdministrator() && !$user->isAdministrator()) {
        return false; // only administrators can delete administrators
      } // if
      
      return $user->isPeopleManager($this->getCompany());
    } // canDelete
    
    /**
     * Returns true if $user can change this users permissions on a $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canChangeProjectPermissions($user, $project) {
      if($user->isProjectLeader($project) || $user->isProjectManager() || $user->isAdministrator()) {
        return false;
      } // if
      
      return $this->isProjectLeader($project) || $this->isPeopleManager() || $this->isAdministrator();
    } // canChangeProjectPermissions
    
    /**
     * Check if $user can remove this user from $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canRemoveFromProject($user, $project) {
      if($user->isProjectLeader($project)) {
        return false;
      } // if
      
      return $this->isProjectLeader($project) || $this->isPeopleManager() || $this->isAdministrator();
    } // canRemoveFromProject
    
    /**
     * Returns true if $user can (re)send welcome message
     *
     * @param User $user
     * @return boolean
     */
    function canSendWelcomeMessage($user) {
      return $user->isPeopleManager() || $user->isCompanyManager($this->getCompany());
    } // canSendWelcomeMessage
    
    // ---------------------------------------------------
    //  Avatars
    // ---------------------------------------------------
    
    /**
     * Get Avatar URL
     *
     * @param boolean $large
     * @return string
     */
    function getAvatarUrl($large = false) {
      $size = $large ? '40x40' : '16x16';
      $mtime = filemtime($this->getAvatarPath($size));
      
      if($mtime === false) {
        return ROOT_URL . "/avatars/default.$size.gif";
      } else {
        return ROOT_URL . '/avatars/' . $this->getId() . ".$size.jpg?updated_on=$mtime";
      } // if
    } // getAvatarUrl
    
    /**
     * Get Avatar Path 
     *
     * @param boolean $large
     * @return string
     */
    function getAvatarPath($large = false) {
      $size = $large ? '40x40' : '16x16';
      return ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/avatars/' . $this->getId() . ".$size.jpg";      	
    } // getAvatarPath
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return View URL
     *
     * @param void
     * @return null
     */
    function getViewUrl() {
    	return assemble_url('people_company_user', array(
    	  'company_id' => $this->getCompanyId(),
    	  'user_id'    => $this->getId(),
    	));
    } // getViewUrl
    
    /**
     * Return Recent Activities URL
     *
     * @param void
     * @return null
     */
    function getRecentActivitiesUrl() {
    	return assemble_url('people_company_user_recent_activities', array(
    	  'company_id' => $this->getCompanyId(),
    	  'user_id'    => $this->getId(),
    	));
    } // getRecentActivitiesUrl
    
    /**
     * Get edit user profile URL
     *
     * @param void
     * @return string
     */
    function getEditProfileUrl() {
      return assemble_url('people_company_user_edit_profile', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getEditProfileUrl
    
    /**
     * Get edit user settings URL
     *
     * @param void
     * @return string
     */
    function getEditSettingsUrl() {
      return assemble_url('people_company_user_edit_settings', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getEditSettingsUrl
    
    /**
     * Return edit company and role URL
     *
     * @param void
     * @return string
     */
    function getEditCompanyAndRoleUrl() {
      return assemble_url('people_company_user_edit_company_and_role', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getEditCompanyAndRoleUrl
    
    /**
     * Get edit password URL
     *
     * @param void
     * @return string
     */
    function getEditPasswordUrl() {
      return assemble_url('people_company_user_edit_password', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getEditPasswordUrl
    
    /**
     * get edit user avatar URL
     *
     * @param void
     * @return string
     */
    function getEditAvatarUrl() {
      return assemble_url('people_company_user_edit_avatar', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getEditAvatarUrl
    
    /**
     * Get Delete Avatar URL
     *
     * @param void
     * @return string
     */
    function getDeleteAvatarUrl() {
    	return assemble_url('people_company_user_delete_avatar', array(
    	 'company_id' => $this->getCompanyId(),
    	 'user_id' => $this->getId(),
    	));
    } // getDeleteAvatarUrl
    
    /**
     * Return delete user URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('people_company_user_delete', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getDeleteUrl
    
    /**
     * Return unsubscribe from object URL
     *
     * @param ProjectObject $object
     * @return string
     */
    function getUnsubscribeUrl($object) {
      return assemble_url('project_object_unsubscribe_user', array(
        'project_id' => $object->getProjectId(),
        'object_id' => $object->getId(),
        'user_id' => $this->getId(),
      ));
    } // getUnsubscribeUrl
    
    /**
     * Return API settings URL
     *
     * @param void
     * @return string
     */
    function getApiSettingsUrl() {
      return assemble_url('people_company_user_api', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getApiSettingsUrl
    
    /**
     * Return reset API key URL
     *
     * @param void
     * @return string
     */
    function getResetApiKeyUrl() {
      return assemble_url('people_company_user_api_reset_key', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getResetApiKeyUrl
    
    /**
     * Return reset password URL
     *
     * @param void
     * @return string
     */
    function getResetPasswordUrl() {
    	return assemble_url('reset_password', array(
    	  'user_id' => $this->getId(),
    	  'code' => $this->getPasswordResetKey(),
    	));
    } // getResetPasswordUrl
    
    /**
     * Return add to projects URL
     *
     * @param void
     * @return string
     */
    function getAddToProjectsUrl() {
      return assemble_url('people_company_user_add_to_projects', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getAddToProjectsUrl
    
    /**
     * Return send welcome message URL
     *
     * @param void
     * @return string
     */
    function getSendWelcomeMessageUrl() {
      return assemble_url('people_company_user_send_welcome_message', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getSendWelcomeMessageUrl
    
    // ---------------------------------------------------
    //  Getters and Setters
    // ---------------------------------------------------
    
    /**
     * Set auto-assign data
     *
     * @param boolean $enabled
     * @param integer $role_id
     * @param array $permissions
     * @return null
     */
    function setAutoAssignData($enabled, $role_id, $permissions) {
    	if($enabled) {
    	  $this->setAutoAssign(true);
  	    if($role_id) {
  	      $this->setAutoAssignRoleId($role_id);
  	      $this->setAutoAssignPermissions(null);
  	    } else {
  	      $this->setAutoAssignRoleId(0);
  	      $this->setAutoAssignPermissions($permissions);
  	    } // if
  	  } else {
  	    $this->setAutoAssign(false);
  	    $this->setAutoAssignRoleId(0);
  	    $this->setAutoAssignPermissions(null);
  	  } // if
    } // setAutoAssignData
    
    /**
     * Return auto assign role based on auto assign role ID
     *
     * @param void
     * @return Role
     */
    function getAutoAssignRole() {
    	$role_id = $this->getAutoAssignRoleId();
    	return $role_id ? Roles::findById($role_id) : null;
    } // getAutoAssignRole
    
    /**
     * Return auto assign permissions
     *
     * @param void
     * @return mixed
     */
    function getAutoAssignPermissions() {
    	$raw = parent::getAutoAssignPermissions();
    	return $raw ? unserialize($raw) : null;
    } // getAutoAssignPermissions
    
    /**
     * Set auto assign permissions
     *
     * @param mixed $value
     * @return mixed
     */
    function setAutoAssignPermissions($value) {
    	return parent::setAutoAssignPermissions(serialize($value));
    } // setAutoAssignPermissions
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if($this->validatePresenceOf('email', 5)) {
        if(is_valid_email($this->getEmail())) {
          if(!$this->validateUniquenessOf('email')) {
            $errors->addError(lang('Email address you provided is already in use'), 'email');
          } // if
        } else {
          $errors->addError(lang('Email value is not valid'), 'email');
        } // if
      } else {
        $errors->addError(lang('Email value is required'), 'email');
      } // if
      
      if($this->isNew()) {
        if(strlen(trim($this->raw_password)) < 3) {
          $errors->addError(lang('Minimal password length is 3 characters'), 'password');
        } // if
      } else {
        if($this->raw_password !== false && strlen(trim($this->raw_password)) < 3) {
          $errors->addError(lang('Minimal password length is 3 characters'), 'password');
        } // if
      } // if
      
      $company_id = $this->getCompanyId();
      if($company_id) {
        $company = Companies::findById($company_id);
        if(!instance_of($company, 'Company')) {
          $errors->addError(lang('Selected company does not exist'), 'company_id');
        } // if
      } else {
        $errors->addError(lang('Please select company'), 'company_id');
      } // if
      
      if(!$this->validatePresenceOf('role_id')) {
        $errors->addError(lang('Role is required'), 'role_id');
      } // if
    } // validate
    
    /**
     * Save user into the database
     *
     * @param void
     * @return boolean
     */
    function save() {
      $modified_fields = $this->modified_fields;
      $is_new = $this->isNew();
      
      if($is_new && ($this->getToken() == '')) {
        $this->resetToken();
      } // if
      
      $save = parent::save();
      if($save && !is_error($save)) {
        if($is_new || in_array('email', $modified_fields) || in_array('first_name', $modified_fields) || in_array('last_name', $modified_fields)) {
          $content = $this->getEmail();
          if($this->getFirstName() || $this->getLastName()) {
            $content .= "\n\n" . trim($this->getFirstName() . ' ' . $this->getLastName());
          } // if
          
          search_index_set($this->getId(), 'User', $content);
          cache_remove_by_pattern('object_assignments_*_rendered');
        } // if
        
        // Role changed?
        if(in_array('role_id', $modified_fields)) {
          clean_user_permissions_cache($this);
        } // if
      } // if
      
      return $save;
    } // save
    
    /**
     * Delete from database
     *
     * @param void
     * @return boolean
     */
    function delete() {
      db_begin_work();
      $delete = parent::delete();
      if($delete && !is_error($delete)) {
        unlink($this->getAvatarPath());
      	unlink($this->getAvatarPath(true));
        
        ProjectUsers::deleteByUser($this);
        Assignments::deleteByUser($this);
        Subscriptions::deleteByUser($this);
        StarredObjects::deleteByUser($this);
        PinnedProjects::deleteByUser($this);
        UserConfigOptions::deleteByUser($this);
        Reminders::deleteByUser($this);
        
        search_index_remove($this->getId(), 'User');
        
        $cleanup = array();
        event_trigger('on_user_cleanup', array(&$cleanup));
        
        if(is_foreachable($cleanup)) {
          foreach($cleanup as $table_name => $fields) {
            foreach($fields as $field) {
              $condition = '';
              if(is_array($field)) {
                $id_field    = array_var($field, 'id');
                $name_field  = array_var($field, 'name');
                $email_field = array_var($field, 'email');
                $condition   = array_var($field, 'condition');
              } else {
                $id_field    = $field . '_id';
                $name_field  = $field . '_name';
                $email_field = $field . '_email';
              } // if
              
              if($condition) {
                db_execute('UPDATE ' . TABLE_PREFIX . "$table_name SET $id_field = 0, $name_field = ?, $email_field = ? WHERE $id_field = ? AND $condition", $this->getName(), $this->getEmail(), $this->getId());
              } else {
                db_execute('UPDATE ' . TABLE_PREFIX . "$table_name SET $id_field = 0, $name_field = ?, $email_field = ? WHERE $id_field = ?", $this->getName(), $this->getEmail(), $this->getId());
              } // if
            } // foreach
          } // foreach
        } // if
        
        db_commit();
        return true;
      } else {
        db_rollback();
        return $delete;
      } // if
    } // delete
  
  }

?>