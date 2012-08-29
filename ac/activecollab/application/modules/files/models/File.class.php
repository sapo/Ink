<?php

  /**
   * File record class
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class File extends ProjectObject {
    
    /**
     * Project tab
     *
     * @var string
     */
    var $project_tab = 'files';
  
    /**
     * Name of the route used for view URL
     *
     * @var string
     */
    var $view_route_name = 'project_file';
    
    /**
     * Name of the route used for delete URL
     *
     * @var string
     */
    var $edit_route_name = 'project_file_edit';
    
    /**
     * Name of the object ID variable used alongside project_id when URL-s are 
     * generated (eg. comment_id, task_id, message_category_id etc)
     *
     * @var string
     */
    var $object_id_param_name = 'file_id';
    
    /**
     * Permission name
     * 
     * @var string
     */
    var $permission_name = 'file';
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    var $fields = array(
      'id', 
      'type', 'module', 
      'project_id', 'parent_id', 'milestone_id', 'parent_type', 
      'name', 'body', 'tags', 'comments_count', 
      'state', 'visibility', 'is_locked', 
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id',
      'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email', 
      'integer_field_1', // revision number...
      'version', 'position',
    );
    
    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'revision' => 'integer_field_1',
    );
    
    /**
     * Files support comments
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
     * Files can have subscribers
     * 
     * @var boolean
     */
    var $can_have_subscribers = true;
    
    /**
     * Checklists are taggable
     *
     * @var boolean
     */
    var $can_be_tagged = true;
    
    /**
     * Does this object has attachments
     *
     * @var boolean
     */
    var $can_have_attachments = true;
    
    /**
     * Files can use reminders
     *
     * @var boolean
     */
    var $can_send_reminders = true;
    
    /**
     * Files can be copied
     *
     * @var boolean
     */
    var $can_be_copied = true;
    
    /**
     * Files can be moved
     *
     * @var boolean
     */
    var $can_be_moved = true;
    
    /**
     * Cached array of all revisions
     *
     * @var array
     */
    var $revisions = false;
    
    /**
     * Last revision file
     *
     * @var Attachment
     */
    var $last_revision = false;
    
    /**
     * Construct file
     *
     * @param mixed $id
     * @return File
     */
    function __construct($id = null) {
      $this->setModule(FILES_MODULE);
      parent::__construct($id);
    } // __constructs
    
    /**
     * Return last revision
     *
     * @param void
     * @return Attachment
     */
    function getLastRevision() {
      if($this->last_revision === false) {
        if($this->revisions === false) {
          $this->last_revision = Attachments::find(array(
            'conditions' => array('parent_id = ? AND parent_type = ? AND attachment_type = ?', $this->getId(), get_class($this), ATTACHMENT_TYPE_FILE_REVISION),
            'order' => 'created_on DESC',
            'offset' => 0, 'limit' => 1,
            'one' => true,
          ));
        } else {
          $this->last_revision = is_array($this->revisions) && isset($this->revisions[0]) ? $this->revisions[0] : null;
        } // if
      } // if
      return $this->last_revision;
    } // getLastRevision
    
    /**
     * Return all revisions
     *
     * @param void
     * @return array
     */
    function getRevisions() {
      if($this->revisions === false) {
        $this->revisions = Files::findRevisions($this, $this->getState(), $this->getVisibility());
      } // if
      return $this->revisions;
    } // getRevisions
    
    /**
     * Cached number of revisions
     *
     * @var integer
     */
    var $revisions_count = false;
    
    /**
     * Return number of revisions
     *
     * @param void
     * @return integer
     */
    function countRevisions() {
      if($this->revisions_count === false) {
        $this->revisions_count = Files::countRevisions($this, $this->getState());
      } // if
      return $this->revisions_count;
    } // countRevisions
    
    /**
     * Return file size
     *
     * @param void
     * @return integer
     */
    function getSize() {
      $last_revision = $this->getLastRevision();
      return instance_of($last_revision, 'Attachment') ? $last_revision->getSize() : 0;
    } // getSize
    
    /**
     * Return file MIME type
     *
     * @param void
     * @return string
     */
    function getMimeType() {
      $last_revision = $this->getLastRevision();
      return instance_of($last_revision, 'Attachment') ? $last_revision->getMimeType() : 'unknown';
    } // getMimeType
    
    /**
     * Return time of last revision creation
     *
     * @param void
     * @return DateTimeValue
     */
    function getLastRevisionOn() {
      $last_revision = $this->getLastRevision();
      return instance_of($last_revision, 'Attachment') && instance_of($last_revision->getCreatedOn(), 'DateValue') ? $last_revision->getCreatedOn() : $this->getCreatedOn();
    } // getLastRevisionOn
    
    /**
     * Return user who posted last revision
     *
     * @param void
     * @return User
     */
    function getLastRevisionBy() {
      $last_revision = $this->getLastRevision();
      return instance_of($last_revision, 'Attachment') ? $last_revision->getCreatedBy() : null;
    } // getLastRevisionBy
    
    /**
     * Describe this file
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
      
      if(array_var($additional, 'describe_revisions')) {
        $result['revisions'] = array();
        
        $revisions = $this->getRevisions();
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
      $wireframe->addBreadCrumb(lang('Files'), assemble_url('project_files', array('project_id' => $this->getProjectId())));
    } // prepareProjectSectionBreadcrumb
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view file URL
     *
     * @param integer $page
     * @return string
     */
    function getViewUrl($page = null) {
      $params = array(
        'project_id' => $this->getProjectId(),
        'file_id' => $this->getId(),
      );
      
      if($page) {
        $params['page'] = $page;
      } // if
      
      return assemble_url('project_file', $params);
    } // getViewUrl
    
    /**
     * Return download file URL
     *
     * @param boolean $force_download
     * @return string
     */
    function getDownloadUrl($force_download = false) {
      $last_revision = $this->getLastRevision();
      return instance_of($last_revision, 'Attachment') ? $last_revision->getViewUrl(null, $force_download) : '';
    } // getDownloadUrl
    
    /**
     * Return thumbnail URL
     *
     * @param void
     * @return string
     */
    function getThumbnailUrl() {
      $last_revision = $this->getLastRevision();
      return instance_of($last_revision, 'Attachment') ? $last_revision->getThumbnailUrl() : '';
    } // getThumbnailUrl
    
    /**
     * Returns true if last revision is an image
     *
     * @param void
     * @return boolean
     */
    function isImage() {
      $last_revision = $this->getLastRevision();
      return instance_of($last_revision, 'Attachment') ? $last_revision->isImage() : false;
    } // isImage
    
    /**
     * Return new revision URL
     *
     * @param void
     * @return string
     */
    function getNewVersionUrl() {
    	return assemble_url('project_file_new_version', array('project_id' => $this->getProjectId(), 'file_id' => $this->getId()));
    } // getNewVersionUrl
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get revision
     *
     * @param null
     * @return integer
     */
    function getRevision() {
      return $this->getIntegerField1();
    } // getRevision
    
    /**
     * Set revision value
     *
     * @param integer $value
     * @return null
     */
    function setRevision($value) {
      return $this->setIntegerField1($value);
    } // setRevision
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can upload a new file in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canAdd($user, $project) {
      return ProjectObject::canAdd($user, $project, 'file');
    } // canAdd
    
    /**
     * Returns true if $user can manage files in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canManage($user, $project) {
      return ProjectObject::canManage($user, $project, 'file');
    } // canManage
    
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
      if(!$this->validatePresenceOf('name', 1)) {
        $errors->addError(lang('File name is required'), 'name');
      } // if
      
      parent::validate($errors, true);
    } // validate
  
  } // File

?>