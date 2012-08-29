<?php

  /**
   * Discussion class
   *
   * @package activeCollab.modules.activeCollab
   * @subpackage models 
   */
  class Discussion extends ProjectObject {
    
    /**
     * Project tab
     *
     * @var string
     */
    var $project_tab = 'discussions';
    
    /**
     * Name of the route used for view URL
     *
     * @var string
     */
    var $view_route_name = 'project_discussion';
    
    /**
     * Name of the route used for portal view URL
     *
     * @var string
     */
    var $portal_view_route_name = 'portal_discussion';
    
    /**
     * Name of the route used for delete URL
     *
     * @var string
     */
    var $edit_route_name = 'project_discussion_edit';
    
    /**
     * Name of the object ID variable used alongside project_id when URL-s are 
     * generated (eg. comment_id, task_id, message_category_id etc)
     *
     * @var string
     */
    var $object_id_param_name = 'discussion_id';
    
    /**
     * Permission name
     * 
     * @var string
     */
    var $permission_name = 'discussion';
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    var $fields = array(
      'id', 
      'type', 'source', 'module', 
      'project_id', 'milestone_id', 'parent_id', 'parent_type', 
      'name', 'body', 'tags', 'comments_count',
      'state', 'visibility', 'is_locked', 
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id',
      'datetime_field_1', // cached value of last comment date...
      'boolean_field_1', // flag that indicates whether this discussion is pinned or not
      'version',
    );
    
    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'last_comment_on' => 'datetime_field_1',
      'is_pinned' => 'boolean_field_1',
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
      * Discussions can have subscribers
      *
      * @var boolean
      */
    var $can_have_subscribers = true;
    
    /**
     * Tickets can have attachments
     *
     * @var boolean
     */
    var $can_have_attachments = true;
    
    /**
     * Discussions are taggable
     *
     * @var boolean
     */
    var $can_be_tagged = true;
    
    /**
     * Discussions can be copied
     *
     * @var boolean
     */
    var $can_be_copied = true;
    
    /**
     * Discussions can be moved
     *
     * @var boolean
     */
    var $can_be_moved = true;
    
    /**
     * Discussions can use reminders
     *
     * @var boolean
     */
    var $can_send_reminders = true;
    
    /**
     * Construct a new discussion
     *
     * @param mixed $id
     * @return Discussion
     */
    function __construct($id = null) {
      $this->setModule(DISCUSSIONS_MODULE);
      parent::__construct($id);
    } // __construct
    
    /**
     * Return last comment
     *
     * @param integer $min_state
     * @param integer $min_visiblity
     * @return Comment
     */
    function getLastComment($min_state = STATE_VISIBLE, $min_visiblity = VISIBILITY_NORMAL) {
      return Comments::findLastCommentByObject($this, $min_state, $min_visiblity);
    } // getLastComment
    
    /**
     * Returns true if this comment is read by $user
     * 
     * Returns true if $user viewed this discussion since last comment was 
     * posted or if last comment is posted more than 30 days ago
     * 
     * @param User $user
     * @return boolean
     */
    function isRead($user) {
      $reference = new DateTimeValue('-30 days');
      $last_comment_on = $this->getLastCommentOn();
      
      if(!instance_of($last_comment_on, 'DateTimeValue')) {
        $last_comment_on = $this->getCreatedOn();
      } // if
      
      if($reference->getTimestamp() > $last_comment_on->getTimestamp()) {
        return true; // last comment posted more than 30 days ago
      } // if
      
      return ProjectObjectViews::isViewed($this, $user);
    } // isRead
    
    /**
     * Return discussion icon URL
     *
     * @param User $user
     * @return string
     */
    function getIconUrl($user) {
      if($this->isRead($user)) {
        return $this->getIsPinned() ? get_image_url('disscusion_read_pinned.gif', DISCUSSIONS_MODULE) : get_image_url('disscusion_read.gif', DISCUSSIONS_MODULE);
      } else {
        return $this->getIsPinned() ? get_image_url('disscusion_unread_pinned.gif', DISCUSSIONS_MODULE) : get_image_url('disscusion_unread.gif', DISCUSSIONS_MODULE);
      } // if
    } // getIconUrl
    
    /**
     * Return portal discussion icon URL
     *
     * @param void
     * @return string
     */
    function getPortalIconUrl() {
    	if($this->getIsPinned()) {
    		return get_image_url('disscusion_read_pinned.gif', DISCUSSIONS_MODULE);
    	} else {
    		return get_image_url('disscusion_read.gif', DISCUSSIONS_MODULE);
    	} // if
    } // getPortalIconUrl
    
    /**
     * Prepare project section breadcrumb when this object is accessed directly 
     * and not through module controller
     *
     * @param Wireframe $wireframe
     * @return null
     */
    function prepareProjectSectionBreadcrumb(&$wireframe) {
      $wireframe->addBreadCrumb(lang('Discussions'), assemble_url('project_discussions', array('project_id' => $this->getProjectId())));
    } // prepareProjectSectionBreadcrumb
    
    /**
     * Prepare portal project section breadcrumb when this object is accessed
     * directly and not through module controller
     *
     * @param Portal $portal
     * @param Wireframe $wireframe
     * @return null
     */
    function preparePortalProjectSectionBreadcrumb($portal, &$wireframe) {
    	$wireframe->addBreadCrumb(lang('Discussions'), assemble_url('portal_discussions', array('portal_name' => $portal->getSlug())));
    } // preparePortalProjectSectionBreadcrumb
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view discussion URL
     *
     * @param integer $page
     * @return string
     */
    function getViewUrl($page = null) {
      $params = $page === null ? null : array('page' => $page);
      return parent::getViewUrl($params);
    } // getViewUrl
    
    /**
     * Return portal view discussion URL
     *
     * @param Portal $portal
     * @param integer $page
     * @return string
     */
    function getPortalViewUrl($portal, $page = null) {
    	$params = $page === null ? null : array('page' => $page);
    	return parent::getPortalViewUrl($portal, $params);
    } // getPortalViewUrl
    
    /**
     * Return pin discussion url
     *
     * @param void
     * @return string
     */
    function getPinUrl() {
      return assemble_url('project_discussion_pin', array(
        'project_id' => $this->getProjectId(),
        'discussion_id' => $this->getId(),
      ));
    } // getPinUrl
    
    /**
     * Return unpin discussion url
     *
     * @param void
     * @return string
     */
    function getUnpinUrl() {
      return assemble_url('project_discussion_unpin', array(
        'project_id' => $this->getProjectId(),
        'discussion_id' => $this->getId(),
      ));
    } // getPinUrl
    
    /**
     * Last comment instance
     *
     * @var User
     */
    var $last_comment_by = false;
    
    /**
     * Return user who posted a last comment on this discussion
     * 
     * This function may return user or anonymous user
     *
     * @param void
     * @return mixed
     */
    function getLastCommentBy() {
      if($this->last_comment_by === false) {
        $last_comment = $this->getLastComment($this->getState(), $this->getVisibility());
        $this->last_comment_by = instance_of($last_comment, 'Comment') ? $last_comment->getCreatedBy() : null;
      } // if
      
      return $this->last_comment_by;
    } // getLastCommentBy
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get last_comment_on
     *
     * @param null
     * @return DateTimeValue
     */
    function getLastCommentOn() {
      return $this->getDatetimeField1();
    } // getLastCommentOn
    
    /**
     * Set last_comment_on value
     *
     * @param DateTimeValue $value
     * @return null
     */
    function setLastCommentOn($value) {
      return $this->setDatetimeField1($value);
    } // setLastCommentOn
    
    /**
     * Get is_pinned
     *
     * @param null
     * @return boolean
     */
    function getIsPinned() {
      return $this->getBooleanField1();
    } // getIsPinned
    
    /**
     * Set is_pinned value
     *
     * @param boolean $value
     * @return null
     */
    function setIsPinned($value) {
      return $this->setBooleanField1($value);
    } // setIsPinned
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can create a new discussion in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canAdd($user, $project) {
      return ProjectObject::canAdd($user, $project, 'discussion');
    } // canAdd
    
    /**
     * Returns true if discussions can be created through $portal
     *
     * @param Portal $portal
     * @return boolean
     */
    function canAddViaPortal($portal) {
    	return parent::canAddViaPortal($portal, 'discussion');
    } // canAddViaPortal
    
    /**
     * Returns true if $user can manage discussions in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canManage($user, $project) {
      return ProjectObject::canManage($user, $project, 'discussion');
    } // canManage
    
    /**
     * Returns true if $user can change pin state of discussion
     *
     * @param User $user
     * @param Project $project
     */
    function canChangePinedState($user) {
      return $this->canEdit($user);      
    } // canChangePinState
    
    // ---------------------------------------------------
    //  System
    // --------------------------------------------------
    
    /**
     * Move object to trash
     * 
     * If $silent is set to true subobject will not add Moved to Trash info into 
     * activity log
     *
     * @param boolean $silent
     * @return boolean
     */
    function moveToTrash($silent = false) {
      $trash = parent::moveToTrash($silent);
      $this->refreshCommentsCount();
      return $trash;
    } // moveToTrash
    
    /**
     * Restore object and subitems from trash
     *
     * @param boolean $check_parent_state
     * @return boolean
     */
    function restoreFromTrash($check_parent_state = true) {
      $restore = parent::restoreFromTrash($check_parent_state);
      $this->refreshCommentsCount();
      return $restore;
    } // restoreFromTrash
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if(!$this->validatePresenceOf('name', 3)) {
        $errors->addError(lang('Summary is required (min 3 letters)'), 'name');
      } // if
      
      if(!$this->validatePresenceOf('body', 3)) {
        $errors->addError(lang('Message is required (min 3 letters)'), 'body');
      } // if
      
      parent::validate($errors, true);
    } // validate
    
    /**
     * Save this discussion into database
     *
     * @param void
     * @return boolean
     */
    function save() {
      if($this->isNew()) {
        $this->setIsPinned((boolean) $this->getIsPinned()); // Make sure we have 0 or 1 instead of NULL
        $this->setLastCommentOn(new DateTimeValue());
      } // if
      
      return parent::save();
    } // save
    
    /**
     * Return pagination info for current discussion
     *
     * @param integer $current_page
     * @param User $user
     * @param integer $per_page
     * @return Pager
     */
    function getPagination($current_page, $user) {
      return new Pager($current_page, $this->getCommentsCount($user), $this->comments_per_page);
    } // getPagination
    
    /**
     * Return portal pagination info for current discussion
     *
     * @param integer $current_page
     * @param integer $per_page
     * @return Pager
     */
    function getPortalPagination($current_page = null, $per_page = 10) {
    	return new Pager($current_page, $this->getCommentsCount(), $per_page);
    } // getPortalPagination
    
  }

?>