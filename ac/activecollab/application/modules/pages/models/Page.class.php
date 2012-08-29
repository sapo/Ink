<?php

  /**
   * Page class
   * 
   * @package activeCollab.modules.pages
   * @subpackage models
   */
  class Page extends ProjectObject {
    
    /**
     * Project tab
     *
     * @var string
     */
    var $project_tab = 'pages';
    
    /**
     * Name of the route used for view URL
     *
     * @var string
     */
    var $view_route_name = 'project_page';
    
    /**
     * Name of the route used for delete URL
     *
     * @var string
     */
    var $edit_route_name = 'project_page_edit';
    
    /**
     * Name of the object ID variable used alongside project_id when URL-s are 
     * generated (eg. comment_id, task_id, message_category_id etc)
     *
     * @var string
     */
    var $object_id_param_name = 'page_id';
    
    /**
     * Permission name
     * 
     * @var string
     */
    var $permission_name = 'page';
    
    /**
     * Saved original name value if setAttributes() is called
     *
     * @var string
     */
    var $old_name = false;
    
    /**
     * Saved original body value if setAttributes() is called
     *
     * @var string
     */
    var $old_body = false;
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    var $fields = array(
      'id', 
      'type', 
      'module', 
      'project_id', 'parent_id', 'milestone_id', 'parent_type', 
      'name', 'body', 'tags', 'comments_count', 
      'state', 'visibility', 'is_locked', 
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id', 
      'integer_field_1', // revision number
      'boolean_field_1', // is_archived
      'position', 'version'
    );
    
    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'revision_num' => 'integer_field_1',
      'is_archived' => 'boolean_field_1',
    );
    
    /**
     * We can have comments for this object
     * 
     * @var boolean
     */
    var $can_have_comments = true;
    
    /**
     * Number of comments per page
     *
     * @var integer
     */
    var $comments_per_page = 25;
    
    /**
     * Tickets can have subtasks
     *
     * @var boolean
     */
    var $can_have_tasks = true;
    
    /**
     * People can subscribe to pages
     * 
     * @var boolean
     */
    var $can_have_subscribers = true;
    
    /**
     * Pages can have attachments
     *
     * @var boolean
     */
    var $can_have_attachments = true;
    
    /**
     * Pages are taggable
     *
     * @var boolean
     */
    var $can_be_tagged = true;
    
    /**
     * Pages can use reminders
     *
     * @var boolean
     */
    var $can_send_reminders = true;
      
    /**
     * Construct page
     *
     * @param mixed $id
     * @return Page
     */
    function __construct($id = null) {
      $this->setModule(PAGES_MODULE);
      parent::__construct($id);
    } // __construct
    
    /**
     * Return subpages
     *
     * @param integer $min_visibility
     * @return array
     */
    function getSubpages($min_visibility = VISIBILITY_PRIVATE) {
      return Pages::findSubpages($this, $this->getState(), $min_visibility);
    } // getSubpages
    
    /**
     * Cached page versions
     *
     * @var array
     */
    var $versions = false;
    
    /**
     * Return all page version objects
     *
     * @param void
     * @return array
     */
    function getVersions() {
      if($this->versions === false) {
        $this->versions = PageVersions::findByPage($this);
        $this->versions_count = is_array($this->versions) ? count($this->versions) : 0;
      } // if
      return $this->versions;
    } // getVersions
    
    /**
     * Cached number of page versions
     *
     * @var integer
     */
    var $versions_count = false;
    
    /**
     * Returns number of versions
     *
     * @param boolean $load
     * @return integer
     */
    function countVersions($load = false) {
      if($this->versions_count === false) {
        if($this->versions === false) {
          if($load) {
            $this->getVersions();
          } else {
            $this->versions_count = PageVersions::countByPage($this);
          } // if
        } // if
      } // if
      return $this->revisions_count;
    } // countVersions
    
    /**
     * Set attributes
     *
     * @param array $attributes
     * @return null
     */
    function setAttributes($attributes) {
      if($this->old_name === false) {
        $this->old_name = $this->getName();
      } // if
      
      if($this->old_body === false) {
        $this->old_body = $this->getBody();
      } // if
      
      return parent::setAttributes($attributes);
    } // setAttributes
    
    /**
     * Create a new page version
     * 
     * @param User $by
     * @return PageVersion
     */
    function createVersion($by) {
      db_begin_work();
      
      $version = new PageVersion();
      
      $version->setPage($this);
      $version->setCreatedBy($this->getUpdatedBy());
      $version->setCreatedOn($this->getUpdatedOn());
      
      $save = $version->save();
      if($save && !is_error($save)) {
        db_commit();
        
        // Update this page with version properties
        $this->setRevisionNum($this->getRevisionNum() + 1);
        $this->setUpdatedBy($by);
        $this->setUpdatedOn(new DateTimeValue());
        
        return $version;
      } // if
      
      db_rollback();
      return $save;
    } // createVersion
    
    /**
     * Revert to version
     *
     * @param PageVersion $version
     * @return boolean
     */
    function revertToVersion($version) {
      $this->setName($version->getName());
      $this->setBody($version->getBody());
      $this->setUpdatedBy($version->getCreatedBy());
      $this->setRevisionNum($this->getRevisionNum() + 1);
      return $this->save();
    } // revertToVersion
    
    /**
     * Describe page
     *
     * @param User $user
     * @param array $additional
     * @return array
     */
    function describe($user, $additional = null) {
      $result = parent::describe($user, array(
        'describe_project'     => array_var($additional, 'describe_project'), 
        'describe_parent'      => array_var($additional, 'describe_parent'), 
        'describe_milestone'   => array_var($additional, 'describe_milestone'), 
        'describe_comments'    => array_var($additional, 'describe_comments'), 
        'describe_tasks'       => array_var($additional, 'describe_tasks'), 
        'describe_attachments' => array_var($additional, 'describe_attachments'), 
      ));
      $result['revision_num'] = $this->getRevisionNum();
      $result['is_archived'] = $this->getIsArchived();
      
      if(array_var($additional, 'describe_subpages')) {
        $result['subpages'] = array();
        
        $subpages = $this->getSubpages();
        if(is_foreachable($subpages)) {
          foreach($subpages as $subpage) {
            $result['subpages'][] = $subpage->describe($user);
          } // foreach
        } // if
      } // if
      
      if(array_var($additional, 'describe_revisions')) {
        $result['revisions'] = array();
        
        $revisions = $this->getVersions();
        if(is_foreachable($revisions)) {
          foreach($revisions as $revision) {
            $result['revisions'][] = $revision->describe($user);
          } // foreach
        } // if
      } // if
      
      return $result;
    } // describe
    
    /**
     * Prepare project section breadcrumb when this object is accessed directly 
     * and not through module controller
     *
     * @param Wireframe $wireframe
     * @return null
     */
    function prepareProjectSectionBreadcrumb(&$wireframe) {
      $wireframe->addBreadCrumb(lang('Pages'), assemble_url('project_pages', array('project_id' => $this->getProjectId())));
    } // prepareProjectSectionBreadcrumb
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can create a new page in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canAdd($user, $project) {
      return ProjectObject::canAdd($user, $project, 'page');
    } // canAdd
    
    /**
     * Returns true if $user can manage pages in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canManage($user, $project) {
      return ProjectObject::canManage($user, $project, 'page');
    } // canManage
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get revision number
     *
     * @param null
     * @return integer
     */
    function getRevisionNum() {
      return $this->getFieldValue('integer_field_1');
    } // getRevisionNum
    
    /**
     * Set revision number value
     *
     * @param integer $value
     * @return null
     */
    function setRevisionNum($value) {
      return $this->setFieldValue('integer_field_1', $value);
    } // setRevisionNum
    
    /**
     * Return value of is archived flag
     *
     * @param void
     * @return boolean
     */
    function getIsArchived() {
      return $this->getFieldValue('boolean_field_1');
    } // getIsArchived
    
    /**
     * Set value of is archived flag
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsArchived($value) {
      return $this->setFieldValue('boolean_field_1', $value);
    } // setIsArchived
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view page URL
     *
     * @param integer $page
     * @return string
     */
    function getViewUrl($page = null) {
      $params = array(
        'project_id' => $this->getProjectId(),
        'page_id' => $this->getId(),
      );
      
      if($page) {
        $params['page'] = $page;
      } // if
      
      return assemble_url('project_page', $params);
    } // getViewUrl
    
    /**
     * Return compare versions URL
     *
     * @param PageVersion $version
     * @return string
     */
    function getCompareVersionsUrl($version = null) {
      $params = array('project_id' => $this->getProjectId(), 'page_id' => $this->getId());
      if(instance_of($version, 'PageVersion')) {
        $params['new'] = 'latest';
        $params['old'] = $version->getVersion();
      } // if
      return assemble_url('project_page_compare_versions', $params);
    } // getCompareVersionsUrl
    
    /**
     * Get revert to URL
     *
     * @param PageVersion $version
     * @return string
     */
    function getRevertUrl($version) {
      return assemble_url('project_page_revert', array('project_id' => $this->getProjectId(), 'page_id' => $this->getId(), 'to' => $version->getVersion()));
    } // getRevertUrl
    
    /**
     * Return archive page URL
     *
     * @param void
     * @return strng
     */
    function getArchiveUrl() {
      return assemble_url('project_page_archive', array(
        'project_id' => $this->getProjectId(),
        'page_id' => $this->getId(),
      ));
    } // getArchiveUrl
    
    /**
     * Return unarchive page URL
     *
     * @param void
     * @return strng
     */
    function getUnarchiveUrl() {
      return assemble_url('project_page_unarchive', array(
        'project_id' => $this->getProjectId(),
        'page_id' => $this->getId(),
      ));
    } // getUnarchiveUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Save changed to DB
     *
     * @param void
     * @return boolean
     */
    function save() {
      $is_new = $this->isNew();
      if($is_new) {
        $this->setRevisionNum(1); // initial revision number
      } // if
      
      if($this->isModifiedField('parent_id')) {
        $parent = $this->getParent();
        if(instance_of($parent, 'Page') && ($parent->getVisibility() == VISIBILITY_PRIVATE)) {
          $this->setVisibility($parent->getVisibility());
        } // if
      } // if
      
      $modified_fields = $this->modified_fields;
      $old_values = $this->old_values;
      
      $save = parent::save();
      if($save && !is_error($save)) {
        if(!$is_new && in_array('visibility', $modified_fields)) {
          $subpages = Pages::findSubpages($this, $this->getState(), $old_values['visibility']);
          
          if(is_foreachable($subpages)) {
            foreach($subpages as $subpage) {
              $subpage->setVisibility($this->getVisibility());
              $subpage->save();
            } // foreach
          } // if
        } // if
      } // if
      return $save;
    } // save
    
  }

?>