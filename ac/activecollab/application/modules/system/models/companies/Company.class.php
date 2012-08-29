<?php

  /**
   * Company class
   * 
   * @package activeCollab.modules.system
   * @subpackage model
   */
  class Company extends BaseCompany {
    
    /**
     * Protected company fields
     *
     * @var aray
     */
    var $protect = array('is_owner');
    
    /**
     * Cached array of users
     *
     * @var array
     */
    var $users = false;
    
    /**
     * Returns Company Users
     *
     * @param array $ids
     * @return array
     */
    function getUsers($ids = null) {
      if($ids === null) {
        if($this->users === false) {
          $this->users = Users::findByCompany($this);
        } // if
        return $this->users;
      } else {
        return Users::findByCompanyAndIds($this, $ids);
      } // if
    } // getUsers
    
    /**
     * Return number of users in company
     *
     * @param void
     * @return null
     */
    function getUsersCount() {
      $company_id = $this->getId();    
    	return Users::count("company_id LIKE $company_id");
    } // getUsersCount
    
    /**
     * Return config option value
     *
     * @param string $name
     * @return mixed
     */
    function getConfigValue($name) {
      return CompanyConfigOptions::getValue($name, $this);
    } // getConfigValue
    
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
     * @return NamedList
     */
    function getOptions($user) {
      if(!isset($this->options[$user->getId()])) {
        $options = new NamedList();
        
        if(User::canAdd($user, $this)) {
          $options->add('add_user', array(
            'text' => lang('New User'),
            'url'  => $this->getAddUserUrl(),
          ));
        } // if
        
        if($this->canEdit($user)) {
          $options->add('edit', array(
            'text' => lang('Change Details'),
            'url'  => $this->getEditUrl(),
          ));
          
          $options->add('edit_logo', array(
            'text' => lang('Change Logo'),
            'url'  => $this->getEditLogoUrl(),
          ));
        } // if
        
        if($this->canArchive($user)) {
          if($this->getIsArchived()) {
            $options->add('unarchive', array(
              'text'    => lang('Unarchive'),
              'url'     => $this->getUnarchiveUrl(),
              'method'  => 'post',
              'confirm' => lang('Are you sure that you want to move this company from list of archive into list of active companies?'),
            ));
          } else {
            $options->add('archive', array(
              'text'    => lang('Archive'),
              'url'     => $this->getArchiveUrl(),
              'method'  => 'post',
              'confirm' => lang('Are you sure that you want to move this company to the archive?'),
            ));
          } // if
        } // if
        
        if($this->canDelete($user)) {
          $options->add('delete', array(
            'text'    => lang('Delete'),
            'url'     => $this->getDeleteUrl(),
            'method'  => 'post',
            'confirm' => lang('Are you sure that you want to delete this company and all of its users? This cannot be undone!'),
          ));
        } // if
        
        // Additional
        event_trigger('on_company_options', array(&$this, &$options, &$user));
        $this->options[$user->getId()] = $options;
      } // if
      return $this->options[$user->getId()];
    } // getOptions
    
    /**
     * Cached array of quick options
     *
     * @var array
     */
    var $quick_options = array();
    
    /**
     * Return quick options
     *
     * @param User $user
     * @return NamedList
     */
    function getQuickOptions($user) {
      if(!isset($this->quick_options[$user->getId()])) {
        $options = new NamedList();
        
        if($this->canEdit($user)) {
          $options->add('edit', array(
            'text' => lang('Change Details'),
            'url'  => $this->getEditUrl(),
          ));
          
          $options->add('edit_logo', array(
            'text' => lang('Change Logo'),
            'url'  => $this->getEditLogoUrl(),
          ));
        } // if
        
        // Additional
        event_trigger('on_company_quick_options', array(&$this, &$options, &$user));
        $this->quick_options[$user->getId()] = $options;
      } // if
      return $this->quick_options[$user->getId()];
    } // getQuickOptions
    
    /**
     * Describe company
     *
     * @param User $user
     * @param boolean $describe_manager
     * @param array $additional
     * @return array
     */
    function describe($user, $additional = null) {
      $result = array(
        'id'              => $this->getId(),
        'name'            => $this->getName(),
        'created_on'      => $this->getCreatedOn(),
        'permalink'       => $this->getViewUrl(),
        'office_address'  => $this->getConfigValue('office_address'),
        'office_phone'    => $this->getConfigValue('office_phone'),
        'office_fax'      => $this->getConfigValue('office_fax'),
        'office_homepage' => $this->getConfigValue('office_homepage'),
      );
      
      if(array_var($additional, 'describe_users')) {
        $company_users = $this->getUsers();
        
        $described_users = null;
        if(is_foreachable($company_users)) {
          $described_users = array();
          foreach($company_users as $company_user) {
            $described_users[] = $company_user->describe($user);
          } // foreach
        } // if
        
        $result['users'] = $described_users;
      } // if
      
      if(array_var($additional, 'describe_logo')) {
        $result['logo_url'] = $this->getLogoUrl(true);
      } // if
      
      return $result;
    } // describe
    
    /**
     * Returns true if this company is owner
     *
     * @param void
     * @return boolean
     */
    function isOwner() {
      return $this->getIsOwner();
    } // isOwner
    
    /**
     * Returns true if this company has projects
     *
     * @param User $user
     * @param array $statuses
     * @return boolean
     */
    function hasProjects($user, $statuses = null) {
      return (boolean) Projects::countByUserAndCompany($user, $this, $statuses);
    } // hasProjects
    
    // ---------------------------------------------------
    //  Logo
    // ---------------------------------------------------
    
    /**
     * Get Logo URL
     *
     * @param boolean $large
     * @return string
     */
    function getLogoUrl($large = false) {      
      $size = $large ? '40x40' : '16x16';
      $mtime = filemtime($this->getLogoPath($size));
      
      if($mtime === false) {
        return ROOT_URL . "/logos/default.$size.gif";
      } else {
        return ROOT_URL . '/logos/' . $this->getId() . ".$size.jpg?updated_on=$mtime";
      } // if
    } // getLogoUrl
    
    /**
     * Get Logo Path 
     *
     * @param boolean $large
     * @return string
     */
    function getLogoPath($large = false) {
      $size = $large ? '40x40' : '16x16';
      return ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/logos/' . $this->getId() . ".$size.jpg";
    } // getLogoPath
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Can $user create a new company
     *
     * @param User $user
     * @return boolean
     */
    function canAdd($user) {
      return $user->isPeopleManager();
    } // canAdd
    
    /**
     * Returns true if $user can see this company
     *
     * @param User $user
     * @return boolean
     */
    function canView($user) {
      if($this->isOwner()) {
        return true;
      } // if
      
      return in_array($this->getId(), $user->visibleCompanyIds());
    } // canView
    
    /**
     * Can this user update company information
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
      return $user->isCompanyManager($this);
    } // canEdit
    
    /**
     * Can $user delete this company
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      if($this->isOwner() || $user->getCompanyId() == $this->getId()) {
        return false;  // Owner company cannot be deleted. Also, user cannot delete company he belongs to
      } // if
      return $user->isPeopleManager();
    } // canDelete
    
    /**
     * Returns true if $user can archive this company
     *
     * @param User $user
     * @return boolean
     */
    function canArchive($user) {
      if($this->isOwner()) {
        return false;
      } else {
        return $user->isPeopleManager();
      } // if
    } // canArchive
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return company view URL
     *
     * @param void
     * @return string
     */
    function getViewUrl() {
      return assemble_url('people_company', array('company_id' => $this->getId()));
    } // getViewUrl
    
    /**
     * Get Edit URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('people_company_edit', array('company_id' => $this->getId())); 
    } // getEditUrl
    
    /**
     * Get Edit Logo URL
     *
     * @param void
     * @return string
     */
    function getEditLogoUrl() {
      return assemble_url('people_company_edit_logo', array('company_id' => $this->getId())); 
    } // getEditLogoUrl
    
    /**
     * Get Delete Logo URL
     *
     * @param void
     * @return null
     */
    function getDeleteLogoUrl() {
      return assemble_url('people_company_delete_logo', array('company_id' => $this->getId())); 
    } // getDeleteLogoUrl
    
    /**
     * Get Delete URL
     *
     * @param void
     * @return null
     */
    function getDeleteUrl() {
    	return assemble_url('people_company_delete', array('company_id' => $this->getId()));
    } // getDeleteUrl
    
    /**
     * Get Edit Logo URL
     *
     * @param void
     * @return string
     */
    function getAddUserUrl(){
      return assemble_url('people_company_user_add', array('company_id' => $this->getId())); 
    } // getAddUserUrl
    
    /**
     * Return archive URL
     *
     * @param void
     * @return string
     */
    function getArchiveUrl() {
      return assemble_url('people_company_archive', array('company_id' => $this->getId(), 'set_value' => true));
    } // getArchiveUrl
    
    /**
     * Return unarchive URL
     *
     * @param void
     * @return string
     */
    function getUnarchiveUrl() {
      return assemble_url('people_company_archive', array('company_id' => $this->getId(), 'set_value' => false));
    } // getUnarchiveUrl
    
    // ---------------------------------------------------
    //  SYSTEM
    // ---------------------------------------------------
    
    /**
     * Returns true if this company is archived
     * 
     * Owner company cannot be archived
     *
     * @param void
     * @return boolean
     */
    function getIsArchived() {
      return $this->getIsOwner() ? false : parent::getIsArchived();
    } // getIsArchived
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
    	if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name')) {
          $errors->addError(lang('Company name needs to be unique'), 'name');
        } // if
      } else {
        $errors->addError(lang('Company name is required'), 'name');
      } // if
      
      if($this->getIsOwner() && $this->getIsArchived()) {
        $errors->addError(lang("Owner company can't be archived"));
      } // if
    } // validate
    
    /**
     * Clear cache on save
     *
     * @param void
     * @return boolean
     */
    function save() {
      $name_changed = in_array('name', $this->modified_fields);
      
    	$save = parent::save();
    	if($name_changed && $save && !is_error($save)) {
    	  cache_remove('companies_id_name'); // remove ID - name map from cache
    	} // if
    	return $save;
    } // save
    
    /**
     * Delete this company from database
     *
     * @param void
     * @return boolean
     */
    function delete() {
      db_begin_work();
      
      $delete = parent::delete();
      if($delete && !is_error($delete)) {
        cache_remove('companies_id_name'); // remove ID - name map from cache
        
        $users = $this->getUsers();
        if(is_foreachable($users)) {
          foreach($users as $user) {
            $user->delete();
          } // foreach
        } // if
        
        Projects::resetByCompany($this);
        
        db_commit();
      } else {
        db_rollback();
      } // if
      return $delete;
    } // delete
        
  }
?>