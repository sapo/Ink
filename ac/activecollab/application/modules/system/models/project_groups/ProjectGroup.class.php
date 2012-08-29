<?php

  /**
   * ProjectGroup class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectGroup extends BaseProjectGroup {
    
    /**
     * Cached projects
     *
     * @var array
     */
    var $projects = false;
    
    /**
     * Cached projects count
     *
     * @var integer
     */
    var $projects_count = false;
    
    /**
     * Return projects that belong to this group
     *
     * @param void
     * @return array
     */
    function getProjects() {
      if($this->projects === false) {
        $this->projects = Projects::findByGroup($this);
      } // if
      return $this->projects;
    } // getProjects
    
    /**
     * Return number of projects that are in this group
     *
     * @param void
     * @return integer
     */
    function getProjectsCount() {
      if($this->projects_count === false) {
        $this->projects_count = Projects::countByGroup($this);
      } // if
      return $this->projects_count;
    } // getProjectsCount
    
    /**
     * Returns true if this group can be deleted (must be empty)
     *
     * @param void
     * @return boolean
     */
    function canBeDeleted() {
      return $this->getProjectsCount() < 1;
    } // canBeDeleted
    
    /**
     * Describe project group
     *
     * @param User $user
     * @param array $additional
     * @return array
     */
    function describe($user, $additional = null) {
      $result = array(
        'id' => $this->getId(),
        'name' => $this->getName(),
      );
      
      if(array_var($additional, 'describe_projects')) {
        $projects = $this->getProjects();
        if(is_foreachable($projects)) {
          $result['projects'] = array();
          foreach($projects as $project) {
            $result['projects'][] = $project->describe($user);
          } // foreach
        } else {
          $result['projects'] = null;
        } // if
      } // if
      
      return $result;
    } // describe
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can create a new project group
     *
     * @param User $user
     * @return boolean
     */
    function canAdd($user) {
      return $user->isProjectManager() || $user->isAdministrator();
    } // canAdd
    
    /**
     * Check if $user can update this project group
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
      return $user->isProjectManager() || $user->isAdministrator();
    } // canEdit
    
    /**
     * Return true if $user can delete this group
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      return ($user->isProjectManager() || $user->isAdministrator()) && ($this->getProjectsCount() == 0) && (ProjectGroups::count() > 1);
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
  
    /**
     * Return view route URL
     *
     * @param integer $page
     * @return string
     */
    function getViewUrl($page = null) {
      $params = array(
        'group_id' => $this->getId(),
        'group_by' => 'group',
      );
      if($page !== null) {
        $params['page'] = $page;
      } // if
      
      return assemble_url('projects', $params);
    } // getViewUrl
    
    /**
     * Return edit route URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('project_group_edit', array(
        'project_group_id' => $this->getId(),
      ));
    } // getEditUrl
    
    /**
     * Return delete route URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('project_group_delete', array(
        'project_group_id' => $this->getId(),
      ));
    } // getDeleteUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate($errors) {
      if($this->validatePresenceOf('name', 3)) {
        if(!$this->validateUniquenessOf('name')) {
          $errors->addError(lang('Group name is already in use'), 'name');
        } // if
      } else {
        $errors->addError(lang('Group name needs to be at least 3 characters long'), 'name');
      } // if
    } // validate
    
    /**
     * Delete this object from database
     *
     * @param void
     * @return boolean
     */
    function delete() {
      if(!$this->canBeDeleted()) {
        return new Error('Only empty groups can be deleted');
      } // if
      return parent::delete();
    } // delete
  
  }

?>