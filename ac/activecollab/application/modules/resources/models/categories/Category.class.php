<?php

  /**
   * Category class
   *
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class Category extends ProjectObject {
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    var $fields = array(
      'id', 
      'type', 
      'module', 
      'project_id', 
      'parent_id', 'parent_type', 
      'name', 
      'state', 'visibility', 
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id',
      'varchar_field_1', // for controller
      'version',
    );
    
    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'controller' => 'varchar_field_1'
    );
    
    /**
     * Paginate category object based on preferences
     *
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_state
     * @param integer $min_visiblity
     * @param string $relation_field
     * @return array
     */
    function paginateObjects($page, $per_page, $min_state = STATE_VISIBLE, $min_visiblity = VISIBILITY_NORMAL, $relation_field = 'parent_id') {
      return ProjectObjects::paginate(array(
        'conditions' => array("$relation_field = ? AND state >= ? AND visibility >= ?", $this->getId(), $min_state, $min_visiblity),
        'order' => 'name'
      ), $page, $per_page);
    } // paginateObjects
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get controller
     *
     * @param null
     * @return string
     */
    function getController() {
      return $this->getVarcharField1();
    } // getController
    
    /**
     * Set controller value
     * 
     * $value can be controller name or instance of Controller class
     *
     * @param Controller $value
     * @return null
     */
    function setController($value) {
      if(is_string($value)) {
        return $this->setVarcharField1($value);
      } elseif(instance_of($value, 'Controller')) {
        return $this->setVarcharField1($value->getControllerName());
      } else {
        return new InvalidParamError('value', $value, '$value is expected to be string or instance of Controller class', true);
      } // if
    } // setController
  
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return section URL
     *
     * @param Project $project
     * @param string $controller_name
     * @return string
     */
    function getSectionUrl($project, $controller_name, $module = SYSTEM_MODULE) {
      return assemble_url('project_categories', array(
        'project_id' => $project->getId(),
        'controller' => $controller_name,
        'module' => $module
      ));
    } // getSectionUrl
    
    /**
     * Return add page URL
     *
     * @param Project $project
     * @param string $controller_name
     * @param string $module
     * @return string
     */
    function getAddUrl($project, $controller_name, $module = SYSTEM_MODULE) {
      return assemble_url('project_category_add', array(
        'project_id' => $project->getId(),
        'controller' => $controller_name,
        'module' => $module,
      ));
    } // getAddUrl
    
    /**
     * Return quick add category URL
     *
     * @param Project $project
     * @param string $controller_name
     * @param string $module
     * @return string
     */
    function getQuickAddUrl($project, $controller_name, $module = SYSTEM_MODULE) {
      return assemble_url('project_category_quick_add', array(
        'project_id' => $project->getId(),
        'controller' => $controller_name,
        'module' => $module,
      ));
    } // getQuickAddUrl
    
    /**
     * Return view category URL
     *
     * @param integer $page
     * @return string
     */
    function getViewUrl($page = null) {      
      $params = array(
        'project_id'  => $this->getProjectId(), 
        'controller'  => $this->getController(), 
        'category_id' => $this->getId(), 
        'module'      => $this->getModule(),
      );
      
      if($page !== null) {
        $params['page'] = $page;
      } // if
      return assemble_url('project_category', $params);
    } // getViewUrl
    
    /**
     * Return portal view category URL
     *
     * @param Portal $portal
     * @param integer $page
     * @return string
     */
    function getPortalViewUrl($portal, $page = null) {
    	$params = array(
    		'portal_name' => $portal->getSlug(),
    		'controller'  => 'portal_' . $this->getController(),
    		'category_id' => $this->getId(),
    		'module'      => PORTALS_MODULE
    	);
    	
    	if($page !== null) {
    		$params['page'] = $page;
    	} // if
    	
    	return assemble_url('portal_category', $params);
    } // getPortalViewUrl
    
    /**
     * Return edit category URL
     *
     * @param void
     * @return string
     */
    function getEditUrl($controller_name = null) {
      return assemble_url('project_category_edit', array(
        'project_id'  => $this->getProjectId(),
        'controller'  => $this->getController(),
        'category_id' => $this->getId(),
        'module'      => $this->getModule(),
      ));
    } // getEditUrl
    
    /**
     * Return delete category URL
     *
     * @param string $controller_name
     * @return string
     */
    function getDeleteUrl($controller_name = null) {
      return assemble_url('project_category_delete', array(
        'project_id'  => $this->getProjectId(),
        'controller'  => $this->getController(),
        'category_id' => $this->getId(),
        'module'      => $this->getModule(),
      ));
    } // getDeleteUrl
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Return add permissions
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canAdd($user, $project) {
      return $user->isProjectLeader($project) || $user->isProjectManager();
    } // canAdd
    
    /**
     * Returns true if $user can rename this category
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
      return $user->isProjectLeader($this->getProject()) || $user->isProjectManager();
    } // canEdit
    
    /**
     * Returns true if user can delete this category
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      return $user->isProjectLeader($this->getProject()) || $user->isProjectManager();
    } // canDelete
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $error
     * @return null
     */
    function validate(&$error) {
      if($this->validatePresenceOf('name', 3)) {
        if(!$this->validateUniquenessOf('name', 'project_id', 'module', 'varchar_field_1')) {
          $error->addError(lang('Category :category_name already exists', array('category_name' => $this->getName())), 'name');
        } // if
      } else {
        $error->addError(lang('Category name is required. Min length is 3 letters'), 'name');
      } // if
      
      // Controller flag. Because it's a flag and we won't have any control 
      // in the interface we are skipping field name in error report
      if(!$this->validatePresenceOf('varchar_field_1', 3)) {
        $error->addError(lang('Controller name is required. Min length is 3 letters'));
      } // if
      
      parent::validate($error, true);
    } // validate
    
    /**
     * Delete category
     *
     * @param void
     * @return boolean
     */
    function delete() {
      return parent::delete(false);
    } // delete
    
  }

?>