<?php

  /**
   * Foundation of every project object
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectObject extends BaseProjectObject {
    
    /**
     * Project that needs to be selected when we are managing this project
     * 
     * This is used mostly when working with resources (they don't know what tab 
     * to select unless they can get that information from parent object)
     *
     * @var string
     */
    var $project_tab = 'overview';
    
    /**
     * Name of the route used for view URL
     *
     * @var string
     */
    var $view_route_name;
    
    /**
     * Name of the route used for portal view URL
     *
     * @var string
     */
    var $portal_view_route_name;
    
    /**
     * Name of the route used for delete URL
     *
     * @var string
     */
    var $edit_route_name;
    
    /**
     * Name of the object ID variable used alongside project_id when URL-s are 
     * generated (eg. comment_id, task_id, message_category_id etc)
     *
     * @var string
     */
    var $object_id_param_name;
    
    /**
     * Permission name
     *
     * @var string
     */
    var $permission_name = null;
    
    /**
     * Cached projet objec
     *
     * @var Project
     */
    var $project = false;
    
    /**
     * Cached parent milestone
     *
     * @var Milestone
     */
    var $milestone = false;
    
    /**
     * Cached parent object
     *
     * @var ProjectObject
     */
    var $parent = false;
    
    /**
     * Cached array of subitems
     *
     * @var array
     */
    var $subitems = false;
    
    /**
     * Cached author object
     *
     * @var User
     */
    var $created_by = false;
    
    /**
     * Cached object of user who did the last update to this object
     *
     * @var User
     */
    var $updated_by = false;
    
    /**
     * Cached object of person who completed this object
     *
     * @var User
     */
    var $completed_by = false;
    
    /**
     * Searchable Fields
     *
     * @var array
     */
    var $searchable_fields = array('name', 'body', 'tags');
    
    /**
     * List of protected fields (can't be set using setAttributes() method)
     *
     * @var array
     */
  	var $protect = array(
  	  'id', 
  	  'type',
  	  'module',
  	  'parent_type',
  	  'state',
  	  'created_on', 
  	  'created_by_id', 
  	  'created_by_name', 
  	  'created_by_email',
  	  'updated_on', 
  	  'updated_by_id', 
  	  'updated_by_name', 
  	  'updated_by_email',
  	  'completed_on', 
  	  'completed_by_id', 
  	  'completed_by_name', 
  	  'completed_by_email', 
  	  'has_time',
  	  'position',
  	  'version'
  	);
    
    /**
     * Return type name
     *
     * @param void
     * @return string
     */
    function getTypeName() {
      return strtolower(get_class($this));
    } // getTypeName
    
    /**
     * Return proper type name in users language
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      $type = strtolower(get_class($this));
      return $lowercase ? lang($type, null, true, $language) : lang(Inflector::humanize($type), null, true, $language);
    } // getVerboseType
    
    /**
     * Return project tab
     *
     * @param void
     * @return string
     */
    function getProjectTab() {
    	return $this->project_tab;
    } // getProjectTab
    
    /**
     * Prepare project section breadcrumb when this object is accessed directly 
     * and not through module controller
     *
     * @param Wireframe $wireframe
     * @return null
     */
    function prepareProjectSectionBreadcrumb(&$wireframe) {
      // This method is implemented by child classes
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
    	// this method is implemented by child classes
    } // preparePortalProjectSectionBreadcrumb
    
    // ---------------------------------------------------
    //  Attribute manipulation
    // ---------------------------------------------------
    
    /**
     * Override default set attributes method
     *
     * @param array $attributes
     * @return null
     */
    function setAttributes($attributes) {
      if(isset($attributes['assignees'])) {
        $this->new_assignees = $attributes['assignees'];
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
    /**
     * Run HTML purifier when body is set
     * 
     * This property is here so objects that just need plain body instead of 
     * HTML does not have it escaped twice (tasks for example)
     *
     * @var boolean
     */
    var $purify_body = true;
    
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
      if(!$this->is_loading && ($field == 'body')) {
        $value = prepare_html($value, $this->purify_body);
      } // if
      
      return parent::setFieldValue($field, $value);
    } // setFieldValue
    
    /**
     * Prepare body for display
     *
     * @param boolean $make_clickable
     * @param boolean $skip_blockquotes - to remove quoted elements from body, only if object is created via email
     * @return string
     */
    function getFormattedBody($make_clickable = true, $skip_blockquotes = false) {
      require_once(SMARTY_PATH . '/plugins/modifier.clickable.php');
      $body = $this->getBody();
      if ($skip_blockquotes) {
        $body = preg_replace('@<blockquote[^>]*?>.*?</blockquote>@si', '', $body);
      } // if
      return $make_clickable ? nl2br_pre(smarty_modifier_clickable($body)) : nl2br_pre($body);
    } // getFormattedBody
    
    /**
     * Return formatted priority
     *
     * @param Language $language
     * @return string
     */
    function getFormattedPriority($language = null) {
      switch($this->getPriority()) {
        case PRIORITY_LOWEST:
          return lang('Lowest', null, true, $language);
        case PRIORITY_LOW:
          return lang('Low', null, true, $language);
        case PRIORITY_NORMAL:
          return lang('Normal', null, true, $language);
        case PRIORITY_HIGH:
          return lang('High', null, true, $language);
        case PRIORITY_HIGHEST:
          return lang('Highest', null, true, $language);
        default:
          return '<span class="unknown">' . lang('Unknown', null, true, $language) . '</span>';
      } // switch
    } // getFormattedPriority
    
    /**
     * Describe this object
     *
     * @param User $user
     * @param array $additional
     * @return array
     */
    function describe($user, $additional = null) {
      $fields = array(
        'id' => 'getId',
        'type' => 'getType',
        'name' => 'getName',
        'body' => 'getBody',
        'state' => 'getState',
        'visibility' => 'getVisibility',
        'created_on' => 'getCreatedOn',
        'created_by_id' => 'getCreatedById',
        'updated_on' => 'getUpdatedOn',
        'updated_by_id' => 'getUpdatedById',
        'version' => 'getVersion',
        'permalink' => 'getViewUrl',
      );
      
      if($this->can_be_completed) {
        $fields['priority'] = 'getPriority';
        $fields['due_on'] = 'getDueOn';
        $fields['completed_on'] = 'getCompletedOn';
        $fields['completed_by_id'] = 'getCompletedById';
      } // if
      
      if($this->can_be_tagged) {
        $fields['tags'] = 'getTags';
      } // if
      
      $result = array();
      $methods = get_class_methods(get_class($this));
      foreach($fields as $field => $getter) {
        if(in_array($getter, $methods) || in_array(strtolower($getter), $methods)) { // second check is for PHP4 compatibility
          $result[$field] = $this->$getter();
        } // if
      } // if
      
      // Project
      if(array_var($additional, 'describe_project')) {
        $project = $this->getProject();
        if(instance_of($project, 'Project')) {
          $result['project'] = $project->describe($user);
        } // if
      } // if
      
      if(!array_key_exists('project', $result)) {
        $result['project_id'] = $this->getProjectId();
      } // if
      
      // Parent
      if(array_var($additional, 'describe_parent')) {
        $parent = $this->getParent();
        if(instance_of($parent, 'ProjectObject')) {
          $result['parent'] = $parent->describe($user);
        } // if
      } // if
      
      if(!array_key_exists('parent', $result)) {
        $result['parent_id'] = $this->getParentId();
      } // if
      
      // Milestone
      if(array_var($additional, 'describe_milestone')) {
        $milestone = $this->getMilestone();
        if(instance_of($milestone, 'Milestone')) {
          $result['milestone'] = $milestone->describe($user);
        } // if
      } // if
      
      if(!array_key_exists('milestone', $result)) {
        $result['milestone_id'] = $this->getMilestoneId();
      } // if
      
      // Comments
      if(array_var($additional, 'describe_comments') && $this->can_have_comments) {
        $result['comments'] = array();
        
        $comments = $this->getComments($user->getVisibility());
        if(is_foreachable($comments)) {
          foreach($comments as $comment) {
            $result['comments'][] = $comment->describe($user);
          } // foreach
        } // if
      } // if
      
      if(array_var($additional, 'describe_tasks') && $this->can_have_tasks) {
        $result['tasks'] = array();
        
        $tasks = $this->getTasks();
        if(is_foreachable($tasks)) {
          foreach($tasks as $task) {
            $result['tasks'][] = $task->describe($user, array(
              'describe_assignees' => true
            ));
          } // foreach
        } // if
      } // if
      
      if(array_var($additional, 'describe_attachments') && $this->can_have_attachments) {
        $result['attachments'] = array();
        
        $attachments = $this->getAttachments();
        if(is_foreachable($attachments)) {
          foreach($attachments as $attachment) {
            $result['attachments'][] = $attachment->describe($user);
          } // foreach
        } // if
      } // if
      
      if($this->can_have_assignees && array_var($additional, 'describe_assignees')) {
        list($assignees, $owner_id) = $this->getAssignmentData();
        if(is_foreachable($assignees)) {
          $result['assignees'] = array();
          foreach($assignees as $assignee) {
            $result['assignees'][] = array(
              'user_id' => $assignee,
              'is_owner' => $assignee == $owner_id,
            );
          } // foreach
        } else {
          $result['assignees'] = null;
        } // if
      } // if
      
      $result['permissions'] = array(
        'can_edit' => $this->canEdit($user),
        'can_delete' => $this->canDelete($user),
        'can_change_visibility' => $this->canChangeVisibility($user),
        'can_move' => $this->canMove($user),
        'can_copy' => $this->canCopy($user),
      );
      
      if($this->can_be_completed) {
        $result['permissions']['can_change_complete_status'] = $this->canChangeCompleteStatus($user);
      } // if
      
      return $result;
    } // describe
    
    // ---------------------------------------------------
    //  Relations
    // ---------------------------------------------------
    
    /**
     * Return parent project
     *
     * @param void
     * @return Project
     */
    function &getProject() {
      if($this->project === false) {
        $this->project = Projects::findById($this->getProjectId());
      } // if
      return $this->project;
    } // getProject
    
    /**
     * Return parent milestone
     *
     * @param void
     * @return Milestone
     */
    function &getMilestone() {
      if($this->milestone === false) {
        $this->milestone = $this->getMilestoneId() ? Milestones::findById($this->getMilestoneId()) : null;
      } // if
      return $this->milestone;
    } // getMilestone
    
    /**
     * Return parent object
     *
     * @param void
     * @return ProjectObject
     */
    function &getParent() {
      if($this->parent === false) {
        $this->parent = $this->getParentId() ? ProjectObjects::findById($this->getParentId()) : null;
      } // if
      return $this->parent;
    } // getParent
    
    /**
     * Set object parent
     * 
     * If $inherit_milestone is TRUE, this object will inherit milestone from 
     * new parent. This step was made optional for situations when we set 
     * category for an object using this method
     *
     * @param ProjectObject $parent
     * @param boolean $inherit_milestone
     * @param boolean $save
     * @return boolean
     */
    function setParent($parent, $inherit_milestone = true, $save = false) {
      if($parent === null || instance_of($parent, 'ProjectObject')) {
        if($parent) {
          $this->setParentId($parent->getId());
    	    $this->setParentType($parent->getType());
    	    if($inherit_milestone) {
    	      $this->setMilestoneId($parent->getMilestoneId());
    	    } // if
        } else {
          $this->setParentId(null);
      	  $this->setParentType(null);
      	  if($inherit_milestone) {
      	    $this->setMilestoneId(null);
      	  } // if
        } // if
        
        return $save ? $this->save() : true;
      } else {
        return false;
      } // if
    } // setParent
    
    /**
     * Return items that have this project object as parent (first level only)
     * 
     * This function will order subitems by position and ID by default, but it 
     * can be overriden in subclasses
     * 
     * If $type is used ProjectObject type will be used
     *
     * @param void
     * @return array
     */
    function &getSubitems() {
      if($this->subitems === false) {
        $this->subitems = ProjectObjects::findByParent($this, null, 'position, id');
      } // if
      return $this->subitems;
    } // getSubitems
    
    // ---------------------------------------------------
    //  Comments resource implementation
    // ---------------------------------------------------
    
    /**
     * Is this object commentable or not
     * 
     * @var boolean
     */
    var $can_have_comments = false;
    
    /**
     * Number of comments per page
     * 
     * If this value is present it means that this object is paginating comments 
     * and that we need to take that into account when redirecting to any 
     * specific comment. Number of comments per page is used to calculate the 
     * proper page
     *
     * @var integer
     */
    var $comments_per_page = null;
    
    /**
     * Return comment submitted for this project object
     *
     * @param integer $min_visiblity
     * @return array
     */
    function getComments($min_visiblity = VISIBILITY_NORMAL) {
      return Comments::findByObject($this, $this->getState(), $min_visiblity);
    } // getComments
    
    /**
     * Return comments for a given page (paginate commetns)
     *
     * @param integer $page
     * @param integer $per_page
     * @param integer $min_visiblity
     * @return array
     */
    function paginateComments($page = 1, $per_page = 10, $min_visiblity = VISIBILITY_NORMAL) {
      return Comments::paginateByObject($this, $page, $per_page, $this->getState(), $min_visiblity);
    } // paginateComments
    
    /**
     * Return post comment URL
     *
     * @param void
     * @return string
     */
    function getPostCommentUrl() {
      return assemble_url('project_comments_add', array(
        'project_id' => $this->getProjectId(),
        'parent_id' => $this->getId(),
      ));
    } // getPostCommentUrl
    
    /**
     * Return post comment via portal URL
     *
     * @param Portal $portal
     * @return string
     */
    function getPostCommentViaPortalUrl($portal) {
    	return assemble_url('portal_comments_add', array(
    		'portal_name' => $portal->getSlug(),
    		'parent_id'   => $this->getId()
    	));
    } // getPostCommentViaPortalUrl
    
    /**
     * Returns true if $user can post a comment to this object
     *
     * @param User $user
     * @return boolean
     */
    function canComment($user) {
      if(!$this->can_have_comments || $this->getIsLocked() || ($this->getState() < STATE_VISIBLE))  {
        return false;
      } // if
      
      return $this->canView($user);
    } // canComment
    
    /**
     * Returns true if users from portal can post a comment to this object
     *
     * @param Portal $portal
     * @return boolean
     */
    function canCommentByPortal($portal) {
    	if(!$this->can_have_comments || !$portal->getIsCommentable() || $this->getIsLocked() || ($this->getState() < STATE_VISIBLE)) {
    		return false;
    	} // if
    	return $this->canViewByPortal($portal);
    } // canCommentByPortal
    
    /**
     * Returns true if user can change locked state of the object
     *
     * @param User $user
     * @return boolean
     */
    function canChangeLockedState($user) {
      return $this->canEdit($user);
    } // canChangeLockedState
    
    /**
     * Refresh value of comments count field
     *
     * @param void
     * @return boolean
     */
    function refreshCommentsCount() {
      $this->setCommentsCount(Comments::countByObject($this, $this->getState(), $this->getVisibility()));
      return $this->save();
    } // refreshCommentsCount
    
    // ---------------------------------------------------
    //  Subscriptions resouce implementation
    // ---------------------------------------------------
    
    /**
     * Can people subscribe to this object
     * 
     * @var boolean
     */
    var $can_have_subscribers = false;
    
    /**
     * Cached array of subscribers
     *
     * @var array
     */
    var $subscribers = false;
    
    /**
     * Return array of subscribed users
     *
     * @param void
     * @return array
     */
    function getSubscribers() {
      if($this->subscribers === false) {
        $this->subscribers = $this->can_have_subscribers ? Subscriptions::findSubscribersByParent($this) : null;
      } // if
      return $this->subscribers;
    } // getSubscribers
    
    /**
     * Returns true if this object has people subscribed to it
     *
     * @param void
     * @return boolean
     */
    function hasSubscribers() {
      return (boolean) Subscriptions::countByParent($this);
    } // hasSubscribers
    
    /**
     * Subscribe $user to this object
     *
     * @param User $user
     * @return boolean
     */
    function subscribe($user) {
      return Subscriptions::subscribe($user, $this);
    } // subscribe
    
    /**
     * Unsubscribe $user from this object
     *
     * @param User $user
     * @return boolean
     */
    function unsubscribe($user) {
      return Subscriptions::unsubscribe($user, $this);
    } // unsubscribe
    
    /**
     * Check if $user is subscribed to this object
     *
     * @param User $user
     * @param boolean $use_cache
     * @return boolean
     */
    function isSubscribed($user, $use_cache = true) {
      return Subscriptions::isSubscribed($user, $this, $use_cache);
    } // isSubscribed
    
    /**
     * Send emails to subscribers
     * 
     * - $additional is assoc array of additional variables that will be 
     *   included in email content generation
     * - $exclude is an array of user ID-s that need to be excuded (if any)
     * - $context - if this object is not context (maybe it's a task or comment 
     *   attached to parent)
     *
     * @param EmailTemplate $template
     * @param array $additional
     * @param array $exclude
     * @param ProjectObject $context
     * @return boolean
     */
    function sendToSubscribers($template, $additional = null, $exclude = null, $context = null) {
      if(!is_array($exclude)) {
        $exclude = $exclude ? array($exclude) : array();
      } // if
      
      $subscribers = $this->getSubscribers();
      if(is_foreachable($subscribers)) {
        $languages = array();
        
        foreach($subscribers as $k => $v) {
          if(in_array($v->getId(), $exclude) || ($v->getVisibility() > $this->getVisibility())) { // exclude or not visible
            unset($subscribers[$k]);
          } else {
            if(LOCALIZATION_ENABLED) {
              $language = $v->getLanguage();
              if(instance_of($language, 'Language') && !isset($languages[$language->getId()])) {
                $languages[$language->getId()] = $language;
              } // if
            } // if
          } // if
        } // foreach
        
        if(is_foreachable($subscribers)) {
          $owner_company = get_owner_company();
          $project = $this->getProject();
          
          // Prepare object type translations
          if(is_foreachable($languages)) {
            $object_type = array();
            foreach($languages as $language) {
              $object_type[$language->getLocale()] = $this->getVerboseType(false, $language);
            } // foreach
          } else {
            $object_type = $this->getVerboseType();
          } // if
          
          $variables = array(
            'details_body'       => EmailTemplates::renderProjectObjectDetails($this, $languages),
            'project_name'       => $project->getName(),
            'project_url'        => $project->getOverviewUrl(),
            'object_type'        => $object_type,
            'object_name'        => $this->getName(),
            'object_body'        => $this->getFormattedBody(),
            'object_url'         => $this->getViewUrl(),
            'owner_company_name' => $owner_company->getName(),
          );
          
          if(is_foreachable($additional)) {
            $variables = array_merge($variables, $additional);
          } // if
          
          if($context === null) {
            $context = $this->getNotificationContext();
          } // if
          
          return ApplicationMailer::send($subscribers, $template, $variables, $context);
        } // if
      } // if
      
      return true;
    } // sendToSubscribers
    
    /**
     * Return content in which notifications are sent
     * 
     * Reply to notification will submit comment for context object, if context 
     * is commentable
     *
     * @param void
     * @return ProjectObject
     */
    function getNotificationContext() {
      return $this->can_have_comments ? $this : null;
    } // getNotificationContext
    
    /**
     * Manage subscriptions URL
     *
     * @param void
     * @return string
     */
    function getSubscriptionsUrl() {
      return assemble_url('project_object_subscriptions', array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      ));
    } // getSubscriptionsUrl
    
    /**
     * Return subscribe to object URL
     *
     * @param User $user
     * @return string
     */
    function getSubscribeUrl($user = null) {
      $params = array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      );
      
      if(instance_of($user, 'User')) {
        $params['user_id'] = $user->getId();
      } // if
      
      return assemble_url('project_object_subscribe', $params);
    } // getSubscribeUrl
    
    /**
     * Return unsubscribe URL
     *
     * @param User $user
     * @return string
     */
    function getUnsubscribeUrl($user = null) {
      $params = array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      );
      
      if(instance_of($user, 'User')) {
        $params['user_id'] = $user->getId();
      } // if
      
      return assemble_url('project_object_unsubscribe', $params);
    } // getUnsubscribeUrl
    
    /**
     * Returns true if $user can subscribe to this object
     *
     * @param User $user
     * @return boolean
     */
    function canSubscribe($user) {
      if(!$this->can_have_subscribers) {
        return false;
      } // if
      
      if($this->getState() < STATE_VISIBLE) {
        return false;
      } // if
      
      return $user->isProjectMember($this->getProject());
    } // canSubscribe
    
    // ---------------------------------------------------
    //  Assignees resource implementation
    // ---------------------------------------------------
    
    /**
     * Can this object have assignees
     * 
     * @var boolean
     */
    var $can_have_assignees = false;
    
    /**
     * Cached array of object assignees
     *
     * @var array
     */
    var $assignees = false;
    
    /**
     * Cached has assignees value
     *
     * @var boolean
     */
    var $has_assignees = null;
    
    /**
     * This value is set when assignees are modified
     *
     * @var array
     */
    var $old_assignees = false;
    
    /**
     * This value will be set when we update assignees list
     * 
     * @var array
     */
    var $new_assignees = false;
    
    /**
     * Returns true if this object has assignees
     *
     * If $load_assignees is set to true assignees will be loaded instead of 
     * counted. Loaded assignees are cached so we save one database call when we 
     * want to display them if we have them preloaded this way
     * 
     * @param boolean $load_assignees
     * @return boolean
     */
    function hasAssignees($load_assignees = false) {
      if($this->has_assignees === null) {
        if($this->assignees === false) {
          if($load_assignees) {
            $this->has_assignees = (boolean) count($this->getAssignees());
          } else {
            $this->has_assignees = (boolean) Assignments::countAssigneesByObject($this);
          } // if
        } else {
          $this->has_assignees = is_foreachable($this->assignees);
        } // if
      } // if
      return $this->has_assignees;
    } // hasAssignees
    
    /**
     * Return all assignees
     *
     * @param void
     * @return array
     */
    function getAssignees() {
      if($this->assignees === false) {
        $this->assignees = $this->can_have_assignees ? Assignments::findAssigneesByObject($this) : null;
      } // if
      return $this->assignees;
    } // getAssignees
    
    /**
     * Cached value of responsible assignee object
     *
     * @var User
     */
    var $responsible_assignee = false;
    
    /**
     * Return user who is responsible for this object
     *
     * @param void
     * @return User
     */
    function getResponsibleAssignee() {
      if($this->responsible_assignee === false) {
        $this->responsible_assignee = Assignments::findOwnerByObject($this);
      } // if
      return $this->responsible_assignee;
    } // getResponsibleAssignee
    
    /**
     * Return assignment data
     * 
     * This function returns aggregated assignment data as array. First element 
     * is list of assignee ID-s and second is owner ID
     *
     * @param void
     * @return array
     */
    function getAssignmentData() {
      if($this->isLoaded()) {
        return Assignments::findAssignmentDataByObject($this);
      } else {
        return array(array(), 0);
      } // if
    } // getAssignmentData
    
    // ---------------------------------------------------
    //  Tasks
    // ---------------------------------------------------
    
    /**
     * Can this object has tasks
     *
     * @var boolean
     */
    var $can_have_tasks = false;
    
    /**
     * Cached array of all object tasks
     *
     * @var boolean
     */
    var $tasks = false;
    
    /**
     * Return all tasks that belong to this object
     *
     * @param void
     * @return array
     */
    function getTasks() {
      if($this->tasks === false) {
        $this->tasks = Tasks::findByObject($this, $this->getState());
      } // if
      return $this->tasks;
    } // getTasks
    
    /**
     * Cached value of tasks count
     *
     * @var integer
     */
    var $tasks_count = false;
    
    /**
     * Return total number of tasks in this object
     *
     * @param void
     * @return integer
     */
    function countTasks() {
      if($this->tasks_count === false) {
        if($this->tasks === false) {
          $this->tasks_count = Tasks::countByObject($this, $this->getState());
        } else {
          $this->tasks_count = is_array($this->tasks) ? count($this->tasks) : 0;
        } // if
      } // if
      return $this->tasks_count;
    } // countTasks
    
    /**
     * Cached array of open tasks
     *
     * @var array
     */
    var $open_tasks = false;
    
    /**
     * Return array of open object tasks
     *
     * @param void
     * @return array
     */
    function getOpenTasks() {
      if($this->open_tasks === false) {
        $this->open_tasks = Tasks::findOpenByObject($this, $this->getState());
      } // if
      return $this->open_tasks;
    } // getOpenTasks
    
    /**
     * Cached value of open tasks count
     *
     * @var integer
     */
    var $open_tasks_count = false;
    
    /**
     * Return number of open tasks in this object
     *
     * @param void
     * @return integer
     */
    function countOpenTasks() {
      if($this->open_tasks_count === false) {
        if($this->open_tasks === false) {
          $this->open_tasks_count = Tasks::countOpenByObject($this, $this->getState());
        } else {
          $this->open_tasks_count = is_array($this->open_tasks) ? count($this->open_tasks) : 0;
        } // if
      } // if
      return $this->open_tasks_count;
    } // countOpenTasks
    
    /**
     * Cached array of completed tasks
     *
     * @var boolean
     */
    var $completed_tasks = false;
    
    /**
     * Return array of completed object tasks (if there is no limit, it will cache tasks)
     *
     * @param integer $limit
     * @return array
     */
    function getCompletedTasks($limit=null) {
      if($this->completed_tasks === false && $limit===null) {
        $this->completed_tasks = Tasks::findCompletedByObject($this, null, $this->getState());
      } else if ($limit!==null) {
        return Tasks::findCompletedByObject($this, $limit, $this->getState());
      } // if
      return $this->completed_tasks;
    } // getCompletedTasks
    
    /**
     * Cached value of completed tasks count
     *
     * @var integer
     */
    var $completed_tasks_count = false;
    
    /**
     * Return number of completed tasks in this object
     *
     * @param void
     * @return integer
     */
    function countCompletedTasks() {
      if($this->completed_tasks_count === false) {
        if($this->completed_tasks === false) {
          $this->completed_tasks_count = Tasks::countCompletedByObject($this, $this->getState());
        } else {
          $this->completed_tasks_count = is_array($this->completed_tasks) ? count($this->completed_tasks) : 0;
        } // if
      } // if
      return $this->completed_tasks_count;
    } // countCompletedTasks
    
    /**
     * Return post task URL
     *
     * @param void
     * @return string
     */
    function getPostTaskUrl() {
      $params = array(
        'project_id' => $this->getProjectId(),
        'parent_id' => $this->getId(),
      );
      
      return assemble_url('project_tasks_add', $params);
    } // getPostTaskUrl
    
    /**
     * Return post task through portal URL
     *
     * @param Portal $portal
     * @param boolean $for_async_request
     * @return string
     */
    function getPortalPostTaskUrl($portal, $for_async_request = false) {
    	$params = array(
    		'portal_name' => $portal->getSlug(),
    		'parent_id'   => $this->getId()
    	);
    	
    	if($for_async_request) {
    		$params['refresh_object_tasks'] = true;
    	} // if
    	
    	return assemble_url('portal_tasks_add', $params);
    } // getPortalPostTaskUrl
    
    /**
     * Return URL for tasks reordering
     *
     * @param boolean $async_request
     * @return string
     */
    function getReorderTasksUrl($async_request = false) {
      $params = array(
        'project_id' => $this->getProjectId(),
        'parent_id' => $this->getId(),
        'async' => $async_request,
      ); 
      
      return assemble_url('project_tasks_reorder', $params);
    } // getReorderTasksUrl
    
    /**
     * Can $user post subtasks
     *
     * @param User $user
     * @return boolean
     */
    function canSubtask($user) {
      if(!$this->can_have_tasks) {
        return false;
      } // if
      
      if($this->getState() < STATE_VISIBLE) {
        return false;
      } // if
      
      return $this->canEdit($user); // Only people with edit permissions can create new tasks
    } // canSubtask
    
    /**
     * Can anonymous users post subtasks through portal
     *
     * @param Portal $portal
     * @return boolean
     */
    function canPortalSubtask($portal) {
    	if(!$this->can_have_tasks) {
    		return false;
    	} // if
    	
    	if($this->permission_name && ($portal->getProjectPermissionValue($this->permission_name) >= PROJECT_PERMISSION_CREATE)) {
        return true;
      } // if
      
      return false;
    } // canPortalSubtask
    
    // ---------------------------------------------------
    //  Attachments
    // ---------------------------------------------------
    
    /**
     * Does this object has attachments
     *
     * @var boolean
     */
    var $can_have_attachments = false;
    
    /**
     * Array of files that are panding to be attached to this object
     * 
     * Pending files are attached on save(), not before even if we have loaded 
     * object (object ID is known)
     *
     * @var array
     */
    var $pending_files = array();
    
    /**
     * Is pending files cleanup function registered
     *
     * @var boolean
     */
    var $pending_files_cleanup_registered = false;
    
    /**
     * Returns true if there are files attached to this object
     *
     * @param void
     * @return boolean
     */
    function hasAttachments() {
      return (boolean) Attachments::countByObject($this, $this->getState(), $this->getVisibility());
    } // hasAttachments
    
    /**
     * Return file attachments
     *
     * @param void
     * @return array
     */
    function getAttachments() {
      return Attachments::findByObject($this, $this->getState(), $this->getVisibility());
    } // getAttachments
    
    /**
     * Return Manage Attachments URL
     *
     * @param boolean $show_details
     * @return string
     */
    function getAttachmentsUrl($show_details = false) {
    	return assemble_url('attachments', array(
    	  'project_id' => $this->getProjectId(),
    	  'object_id'  => $this->getId(),
    	));
    } // getAttachmentsUrl
    
    /**
     * Return attachments mass update URL
     *
     * @param void
     * @return string
     */
    function getAttachmentsMassUpdateUrl() {
    	return assemble_url('attachments_mass_update', array(
    	  'project_id' => $this->getProjectId(),
    	  'object_id'  => $this->getId(),
    	));
    } // getAttachmentsMassUpdateUrl
    
    /**
     * Attach file from file system
     * 
     * If $name and/or $type are missing they will be extracted from real file
     *
     * @param string $path
     * @param string $name
     * @param string $type
     * @param User $user
     * @return boolean
     */
    function attachFile($path, $name, $type, $user = null) {
      if(is_file($path)) {
        $destination_file = get_available_uploads_filename();
        if(copy($path, $destination_file)) {
          return $this->addPendingFile($destination_file, $name, $type, filesize($path), $user);
        } // if
      } // if
      return false;
    } // attachFile
    
    /**
     * Attach uploaded file
     * 
     * $file is a single element of $_FILES auto global array
     *
     * @param array $file
     * @param User $user
     * @return boolean
     */
    function attachUploadedFile($file, $user) {
      if(is_array($file)) {
        if(isset($file['error']) && $file['error'] > 0) {
          use_error('UploadError');
          return new UploadError($file['error']);
        } // if
        
        $destination_file = get_available_uploads_filename();
        if(move_uploaded_file($file['tmp_name'], $destination_file)) {
          return $this->addPendingFile($destination_file, array_var($file, 'name'), array_var($file, 'type'), array_var($file, 'size'), $user);
        } // if
      } // if
      
      return false;
    } // attachUploadedFile
    
    /**
     * Add pending file to the list of pending files
     *
     * @param string $location
     * @param string $name
     * @param string $type
     * @param integer $size
     * @param User $user
     * @return null
     */
    function addPendingFile($location, $name, $type, $size, $user = null) {
      $this->pending_files[] = array(
        'location'   => $location, 
        'name'       => $name, 
        'type'       => $type, 
        'size'       => $size,
        'created_by' => $user,
      ); // array
      
      if(!$this->pending_files_cleanup_registered) {
        $this->pending_files_cleanup_registered = true;
        register_shutdown_function(array(&$this, 'clearPendingFiles'));
      } // if
      
      return true;
    } // addPendingFile
    
    /**
     * Clean up pending files
     *
     * @param void
     * @return null
     */
    function clearPendingFiles() {
      if($this->can_have_attachments && is_foreachable($this->pending_files)) {
        foreach($this->pending_files as $pending_file) {
          unlink($pending_file['location']);
        } // foreach
      } // if
      
      $this->pending_files = array(); // and reset
    } // clearPendingFiles
    
    // ---------------------------------------------------
    //  Poking
    // ---------------------------------------------------
    
    /**
     * Indicator wether this object supports reminders or not
     *
     * @var boolean
     */
    var $can_send_reminders = false;
    
    /**
     * Return send reminder URL for this object
     *
     * @param void
     * @return string
     */
    function getSendReminderUrl() {
    	return assemble_url('reminders_add', array('parent_id' => $this->getId()));
    } // getSendReminderUrl
    
    /**
     * Returns true if $user can send reminders in context of this object
     *
     * @param User $user
     * @return boolean
     */
    function canSendReminder($user) {
    	return $this->can_send_reminders;
    } // canSendReminder
    
    // ---------------------------------------------------
    //  Complete and reopene
    // ---------------------------------------------------
    
    /**
     * Is this object completable
     *
     * @var boolean
     */
    var $can_be_completed = false;
    
    /**
     * Returns true if this object is marked as completed
     *
     * @param void
     * @return boolean
     */
    function isCompleted() {
      return instance_of($this->getCompletedOn(), 'DateValue');
    } // isCompleted
    
    /**
     * Returns true if this object is open (not completed)
     *
     * @param void
     * @return boolean
     */
    function isOpen() {
      $completed_on = $this->getCompletedOn();
      return empty($completed_on);
    } // isOpen
    
    /**
     * Mark this object as completed
     *
     * @param User $by
     * @param string $comment
     * @return boolean
     */
    function complete($by, $comment = null) {
      if($this->isCompleted()) {
        return true; // already completed
      } // if
      
      if(!instance_of($by, 'User') && !instance_of($by, 'AnonymousUser')) {
        return new InvalidParamError('by', $by, '$by is expected to be an User or AnonymousUser instance', true);
      } // if
      
      $this->setCompletedBy($by);
      $this->setCompletedOn(new DateTimeValue());
      $this->setResolution($resolution);
      
      $save = $this->save();
      if($save && !is_error($save)) {
        event_trigger('on_project_object_completed', array(&$this, &$by, $comment));
        
        $activity_log = new TaskCompletedActivityLog();
        $activity_log->log($this, $by);
      } // if
      return $save;
    } // complete
    
    /**
     * Mark this item as opened
     *
     * @param User $by
     * @return boolean
     */
    function open($by) {
      if($this->isOpen()) {
        return true; // already open
      } // if
      
      $this->setCompletedBy(null);
      $this->setCompletedOn(null);
      $this->setResolution(null);
      
      $save = $this->save();
      if($save && !is_error($save)) {
        event_trigger('on_project_object_opened', array(&$this, &$by));
        
        $activity_log = new TaskReopenedActivityLog();
        $activity_log->log($this, $by);
      } // if
      return $save;
    } // open
    
    /**
     * Mark this object as closed for comments
     * 
     * @param void
     * @return boolean
     */
    function lock($by) {
      if ($this->getIsLocked()) {
        return true; // already locked
      } // if
      
      $this->setIsLocked(true);
      
      $save = $this->save();
      if ($save && !is_error($save)) {
        event_trigger('on_project_object_locked', array(&$this, &$by));
        
        $activity_log = new CommentsLockedActivityLog();
        $activity_log->log($this, $by);
      } // if
      return $save;
    } // lock
    
    /**
     * Mark this object as opened for comments
     * 
     * @param void
     * @return boolean
     */
    function unlock($by) {
      if (!$this->getIsLocked()) {
        return true; // already locked
      } // if
      
      $this->setIsLocked(false);
      
      $save = $this->save();
      if ($save && !is_error($save)) {
        event_trigger('on_project_object_unlocked', array(&$this, &$by));
        
        $activity_log = new CommentsUnlockedActivityLog();
        $activity_log->log($this, $by);
      } // if
      return $save;
    } // unlock
    
    /**
     * Return complete object URL
     *
     * @param boolean $async
     * @return string
     */
    function getCompleteUrl($async = false) {
      $params = array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      );
      
      if($async) {
        $params['ajax_complete_reopen'] = true;
      } // if
      
      return assemble_url('project_object_complete', $params);
    } // getCompleteUrl
    
    /**
     * Return open object URL
     *
     * @param boolea $async
     * @return string
     */
    function getOpenUrl($async = false) {
      $params = array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      );
      
      if($async) {
        $params['ajax_complete_reopen'] = true;
      } // if
      
      return assemble_url('project_object_open', $params);
    } // getOpenUrl
    
    /**
     * Return due on date
     *
     * @param void
     * @return DateTimeValue
     */
    function getDueOn() {
      return parent::getDueOn();
    } // getDueOn
    
    /**
     * Set due on value
     *
     * @param DateTimeValue $value
     * @return DateTimeValue
     */
    function setDueOn($value) {
      return parent::setDueOn($value);
    } // setDueOn
    
    /**
     * Return user who completed this object
     *
     * @param void
     * @return User
     */
    function getCompletedBy() {
      if($this->completed_by === false) {
        $completed_by_id = $this->getCompletedById();
        
        if($completed_by_id) {
          $this->completed_by = Users::findById($completed_by_id);
        } else {
          $this->completed_by = new AnonymousUser($this->getCompletedByName(), $this->getCompletedByEmail());
        } // if
      } // if
      return $this->completed_by;
    } // getCompletedBy
    
    /**
     * Returns true if this object is late
     *
     * @param void
     * @return boolean
     */
    function isLate() {
      $now = DateTimeValue::now();
      
      $due_on = $this->getDueOn();
      if(instance_of($due_on, 'DateTimeValue')) {
        return ($due_on->getTimestamp() < $now->getTimestamp()) && !$this->isToday();
      } // if
      return false;
    } // isLate
    
    /**
     * Returns true if this object is due today
     *
     * @param void
     * @return boolean
     */
    function isToday() {
      $due_on = $this->getDueOn();
      if(instance_of($due_on, 'DateTimeValue')) {
        return $due_on->isToday();
      } // if
      return false;
    } // isToday
    
    /**
     * Returns true if this object is due in future
     *
     * @param void
     * @return boolean
     */
    function isUpcoming() {
      $now = DateTimeValue::now();
      
      $due_on = $this->getDueOn();
      if(instance_of($due_on, 'DateTimeValue')) {
        return ($due_on->getTimestamp() > $now->getTimestamp()) && !$this->isToday();
      } // if
      return false;
    } // isUpcoming
    
    /**
     * Set person who completed this object
     *
     * @param mixed $completed_by
     * @return null
     */
    function setCompletedBy($completed_by) {
      if($completed_by === null) {
        $this->setCompletedById(0);
        $this->setCompletedByName('');
        $this->setCompletedByEmail('');
      } elseif(instance_of($completed_by, 'User')) {
        $this->setCompletedById($completed_by->getId());
        $this->setCompletedByName($completed_by->getDisplayName());
        $this->setCompletedByEmail($completed_by->getEmail());
      } elseif(instance_of($completed_by, 'AnonymousUser')) {
        $this->setCompletedById(0);
        $this->setCompletedByName($completed_by->getName());
        $this->setCompletedByEmail($completed_by->getEmail());
      } // if
    } // setCompletedBy
    
    /**
     * Check if $user can change completion status
     *
     * @param User $user
     * @return null
     */
    function canChangeCompleteStatus($user) {
      if(!$this->can_be_completed) {
        return false;
      } // if
      
      if($this->getState() < STATE_VISIBLE) {
        return false;
      } // if
      
      return $this->canEdit($user);
    } // canChangeCompleteStatus
    
    // ---------------------------------------------------
    //  Timetracking related functions
    // ---------------------------------------------------
    
    /**
     * Return timetracking URL for this object
     *
     * @param void
     * @return string
     */
    function getTimeUrl() {
      return timetracking_module_url($this->getProject(), $this);
    } // getTimeUrl
    
    // ---------------------------------------------------
    //  Taggable
    // ---------------------------------------------------
    
    /**
     * Can this object have tags
     *
     * @var boolean
     */
    var $can_be_tagged = false;
    
    /**
     * Returns true if this object is tagged
     *
     * @param void
     * @return boolean
     */
    function hasTags() {
      return parent::getTags() != '';
    } // hasTags
    
    /**
     * Return array of object tags
     *
     * @param void
     * @return array
     */
    function getTags() {
      return Tags::toTags(parent::getTags());
    } // getTags
    
    /**
     * Set object tags
     * 
     * $value can be comma separated list of tags or array of tags
     *
     * @param mixed $value
     * @return string
     */
    function setTags($value) {
      return parent::setTags(Tags::toString($value));
    } // setTags
    
    // ---------------------------------------------------
    //  Star
    // ---------------------------------------------------
    
    /**
     * This object can be starred
     *
     * @var boolean
     */
    var $can_be_starred = true;
    
    /**
     * Returns true if this object is starred by $user
     *
     * @param User $user
     * @return boolean
     */
    function isStarred($user) {
      return StarredObjects::isStarred($this, $user);
    } // isStarred
    
    /**
     * Star this object
     *
     * @param User $user
     * @return boolean
     */
    function star($user) {
      return StarredObjects::starObject($this, $user);
    } // star
    
    /**
     * Unstar this object for a given user
     *
     * @param User $user
     * @return boolean
     */
    function unstar($user) {
      return StarredObjects::unstarObject($this, $user);
    } // unstar
    
    /**
     * Return star URL
     *
     * @param void
     * @return string
     */
    function getStarUrl() {
      return assemble_url('project_object_star', array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      ));
    } // getStarUrl
    
    /**
     * Return unstar URL
     *
     * @param void
     * @return string
     */
    function getUnstarUrl() {
      return assemble_url('project_object_unstar', array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      ));
    } // getUnstarUrl
    
    /**
     * Return lock URL
     *
     * @param void
     * @return string
     */
    function getLockUrl() {
      return assemble_url('project_object_lock', array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      ));      
    } // getLockUrl
    
    /**
     * Return unlock URL
     *
     * @param void
     * @return string
     */
    function getUnlockUrl() {
      return assemble_url('project_object_unlock', array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      ));      
    } // getLockUrl
    
    // ---------------------------------------------------
    //  Activity Log
    // ---------------------------------------------------
    
    /**
     * Master log activities flag
     * 
     * If this flag is set to false no activities will be logged
     *
     * @var boolean
     */
    var $log_activities = true;
    
    /**
     * Log creation of this object
     *
     * @var boolean
     */
    var $log_creation = true;
    
    /**
     * Log update of this object
     *
     * @var false
     */
    var $log_update = false;
    
    /**
     * Log when this object is mode to trash
     *
     * @var boolean
     */
    var $log_move_to_trash = true;
    
    /**
     * Log when this object is restored from trash
     *
     * @var boolean
     */
    var $log_restore_from_trash = true;
    
    /**
     * Return comment that need to be stored in activity log
     * 
     * By default this function will extract data from body field (if present). 
     * If you need a bit different behavior override this function in subclasses
     *
     * @param void
     * @return string
     */
    function getActivityLogComment() {
      $body = strip_tags($this->getBody());
      if($body) {
        $beginning = substr_utf($body, 0, 120);
        if(strlen_utf($beginning) < strlen_utf($body)) {
          $beginning .= '...';
        } // if
        
        return $beginning;
      } // if
      
      return null;
    } // getActivityLogComment

    // ---------------------------------------------------
    //  Copy and move
    // ---------------------------------------------------
    
    /**
     * Can we copy this object into another project
     * 
     * Any object can be moved. This flags are introduced so system can offer 
     * copy options to the user in object options list
     *
     * @var boolean
     */
    var $can_be_copied = false;
    
    /**
     * Can we move this object into another project
     * 
     * Any object can be moved. This flags are introduced so system can offer 
     * move options to the user in object options list
     *
     * @var boolean
     */
    var $can_be_moved = false;
    
    /**
     * Copy this object to $project
     *
     * $milestone can be an instance of Milestone class or milestone ID
     * 
     * @param Project $project
     * @param Milestone $milestone
     * @param ProjectObject $parent
     * @param boolean $cascade
     * @return boolean
     */
    function copyToProject($project, $milestone = null, $parent = null, $cascade = true) {
      db_begin_work();
      
      $copy = $this->copy();
      
      $copy->log_activities = false;
      
      $copy->setProjectId($project->getId());
      $copy->setParent($parent);
      
      if(instance_of($milestone, 'Milestone')) {
        $copy->setMilestoneId($milestone->getId());
      } elseif($milestone) {
        $copy->setMilestoneId($milestone);
      } else {
        $copy->setMilestoneId(null);
      } // if
      
      $save = $copy->save();
      if($save && !is_error($save)) {
        event_trigger('on_project_object_copied', array(&$this, &$copy, &$project, $cascade));
        db_commit();
      } else {
        db_rollback();
        return false;
      } // if
      
      return $copy;
    } // copyToProject
    
    /**
     * Move this object to $project
     *
     * @param Project $project
     * @return boolean
     */
    function moveToProject($project) {
      if($this->getProjectId() == $project->getId()) {
        return true; // already in $project
      } // if
      
      db_begin_work();
      
      $old_project = $this->getProject();
      $this->setProjectId($project->getId());
      $this->setParent(null);
      $this->setMilestoneId(0);
      
      $save = $this->save();
      if($save && !is_error($save)) {
        event_trigger('on_project_object_moved', array(&$this, &$old_project, &$project));
        
        db_commit();
      } else {
        db_rollback();
      } // if
      
      return $save;
    } // moveToProject
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can access this object
     *
     * @param User $user
     * @return boolean
     */
    function canView($user) {
      $project = $this->getProject();
      if(!instance_of($project, 'Project')) {
        return false;
      } // if
      
      // User needs to have lower minimal visibility and needs to have access 
      // permissions in order to access this particular object
      return ($user->getVisibility() <= $this->getVisibility()) && ($user->getProjectPermission($this->permission_name, $project) >= PROJECT_PERMISSION_ACCESS);
    } // canView
    
    /**
     * Returns true if anonymous users can access this object via portal
     *
     * @param Portal $portal
     * @return boolean
     */
    function canViewByPortal($portal) {
    	$project = $this->getProject();
    	if(!instance_of($project, 'Project') || $project->getId() != $portal->getProjectId()) {
    		return false;
    	} // if
    	
    	return (VISIBILITY_NORMAL <= $this->getVisibility()) && ($portal->getProjectPermissionValue($this->permission_name) >= PROJECT_PERMISSION_ACCESS);
    } // canViewByPortal
    
    /**
     * Returns true if $user can add a new object of this class to $project
     *
     * @param User $user
     * @param Project $project
     * @param string $add_permission_name
     * @return boolean
     */
    function canAdd($user, $project, $add_permission_name = null) {
      return $user->getProjectPermission($add_permission_name, $project) >= PROJECT_PERMISSION_CREATE;
    } // canAdd
    
    /**
     * Returns true if objects of this class can be created through portal
     *
     * @param Portal $portal
     * @param string $add_permission_name
     * @return boolean
     */
    function canAddViaPortal($portal, $add_permission_name = null) {
    	return $portal->getProjectPermissionValue($add_permission_name) >= PROJECT_PERMISSION_CREATE;
    } // canAddViaPortal
    
    /**
     * Returns true if $user can manage a new object of this class to $project
     *
     * @param User $user
     * @param Project $project
     * @param string $add_permission_name
     * @return boolean
     */
    function canManage($user, $project, $add_permission_name = null) {
      return $user->getProjectPermission($add_permission_name, $project) >= PROJECT_PERMISSION_MANAGE;
    } // canManage
    
    /**
     * Returns true if $user can update this object
     *
     * @param User $user
     * @param string $manage_permission_name
     * @return boolean
     */
    function canEdit($user) {
      if($this->getState() < STATE_VISIBLE) {
        return false;
      } // if
      
      $project = $this->getProject();
      if(!instance_of($project, 'Project')) {
        return false;
      } // if
      
      if($user->isProjectManager() || $user->isProjectLeader($project)) {
        return true; // administrators and project managers have all permissions
      } // if
      
      if(($this->getVisibility() < VISIBILITY_NORMAL) && !$user->canSeePrivate()) {
        return false;
      } // if
      
      if($this->permission_name && ($user->getProjectPermission($this->permission_name, $project) >= PROJECT_PERMISSION_MANAGE)) {
        return true; // Management permissions
      } // if
      
      if($this->getCreatedById() == $user->getId()) {
        return true; // Author
      } // if
      
      // Assingments
      //
      // If this object is not assigned to anyone in particular than anyone can 
      // update it. If we have assignees than $user needs to be assigned to this 
      // object in order to be able to update it
      if($this->can_have_assignees) {
        if($this->hasAssignees()) {
          return Assignments::isAssignee($user, $this);
        } else {
          return true;
        } // if
      } // if
      
      return false;
    } // canEdit
    
    /**
     * Returns true if $user can change objects visibility
     *
     * @param User $user
     * @return boolean
     */
    function canChangeVisibility($user) {
      return $user->canSeePrivate();
    } // canChangeVisibility
    
    /**
     * Returns true if $user can delete this object
     *
     * @param User $user
     * @param string $manage_permission_name
     * @return boolean
     */
    function canDelete($user) {
      $project = $this->getProject();
      if(!instance_of($project, 'Project')) {
        return false;
      } // if
      
      if($user->isProjectManager() || $user->isProjectLeader($this->getProject())) {
        return true; // administrators and project managers have all permissions
      } // if
      
      if(($this->getVisibility() < VISIBILITY_NORMAL) && !$user->canSeePrivate()) {
        return false;
      } // if
      
      if($this->permission_name && $user->getProjectPermission($this->permission_name, $project) >= PROJECT_PERMISSION_MANAGE) {
        return true;
      } // if
      
      // Author in the next three hours
      if($this->getCreatedById() == $user->getId()) {
        $created_on = $this->getCreatedOn();
        return time() < ($created_on->getTimestamp() + 10800);
      } // if
      
      return false;
    } // canDelete
    
    /**
     * Check if specific user can move this object
     *
     * @param User $user
     * @return boolean
     */
    function canMove($user) {
      if(!$this->can_be_moved) {
        return false;
      } // if
      
      return $user->isProjectManager() || $user->isProjectLeader($this->getProject());
    } // canMove
    
    /**
     * Return true if specific user can copy this object
     *
     * @param User $user
     * @return boolean
     */
    function canCopy($user) {
      if(!$this->can_be_copied) {
        return false;
      } // if
      
      return $user->isProjectManager() || $user->isProjectLeader($this->getProject());
    } // canCopy
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return object view URL
     * 
     * Generic object view URL. This function is usually overriden
     *
     * @param array $additional_params
     * @return string
     */
    function getViewUrl($additional_params = null) {
      $params = array(
        'project_id' => $this->getProjectId(),
        $this->object_id_param_name => $this->getId(),
      );
      
      if($additional_params !== null) {
        $params = array_merge($params, $additional_params);
      } // if
      
      return assemble_url($this->view_route_name, $params);
    } // getViewUrl
    
    /**
     * Return portal object view URL
     *
     * @param Portal $portal
     * @param array $additional_params
     * @return string
     */
    function getPortalViewUrl($portal, $additional_params = null) {
    	$params = array(
    		'portal_name'               => $portal->getSlug(),
    		$this->object_id_param_name => $this->getId()
    	);
    	
    	if($additional_params !== null) {
    		$params = array_merge($params, $additional_params);
    	} // if
    	
    	return assemble_url($this->portal_view_route_name, $params);
    } // getPortalViewUrl
    
    /**
     * Return edit URL
     *
     * @param array $additional_params
     * @return string
     */
    function getEditUrl($additional_params = null) {
      $params = array(
        'project_id' => $this->getProjectId(),
        $this->object_id_param_name => $this->getId(),
      );
      
      if($additional_params !== null) {
        $params = array_merge($params, $additional_params);
      } // if
      
      return assemble_url($this->edit_route_name, $params);
    } // getEditUrl
    
    /**
     * Return trash URL
     *
     * @param void
     * @return string
     */
    function getTrashUrl() {
      return assemble_url('project_object_trash', array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      ));
    } // getTrashUrl
    
    /**
     * Return un-trash URL
     *
     * @param array $additional_params
     * @return string
     */
    function getUntrashUrl($additional_params = null) {
      return assemble_url('project_object_untrash', array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      ));
    } // getUntrashUrl
    
    /**
     * Return chagne visibility URL
     *
     * @param string $to
     * @return string
     */
    function getChangeVisibilityUrl($to) {
      return assemble_url('project_object_change_visibility', array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
        'to' => $to,
      ));
    } // getChangeVisibilityUrl
    
    /**
     * Return move object URL
     *
     * @param void
     * @return string
     */
    function getMoveUrl() {
      return assemble_url('project_object_move', array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      ));
    } // getMoveUrl
    
    /**
     * Return copy object URL
     *
     * @param void
     * @return string
     */
    function getCopyUrl() {
      return assemble_url('project_object_copy', array(
        'project_id' => $this->getProjectId(),
        'object_id' => $this->getId(),
      ));
    } // getCopyUrl
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Cached value of object options
     *
     * @var array
     */
    var $options = array();
    
    /**
     * Return array of object options
     *
     * @param User $user
     * @return NamedList
     */
    function getOptions($user) {
      if(!isset($this->options[$user->getId()])) {
        $options = new NamedList();
        
        if($this->canEdit($user)) {
          $options->add('edit', array(
            'url' => $this->getEditUrl(),
            'text' => lang('Edit'),
          ));
        } // if
        
        if($this->can_have_comments && $this->canChangeLockedState($user)) {
          if ($this->getIsLocked()) {
            $options->addAfter('unlock', array(
              'url'  => $this->getUnlockUrl(),
              'text' => lang('Unlock Comments'),
              'method' => 'post',
            ), 'star');        
          } else {
            $options->addAfter('lock', array(
              'url'  => $this->getLockUrl(),
              'text' => lang('Lock Comments'),
              'method' => 'post',
            ), 'star');
          } // if
        } // if
        
        if(StarredObjects::isStarred($this, $user)) {
          $options->add('unstar', array(
            'text' => lang('Unstar'),
            'url' => $this->getUnstarUrl(),
            'method' => 'post',
          ));
        } else {
          $options->add('star', array(
            'text' => lang('Star'),
            'url' => $this->getStarUrl(),
            'method' => 'post',
          ));
        } // if
        
        if($this->canMove($user)) {
          $options->add('move_to_project', array(
            'text' => lang('Move to Project'),
            'url' => $this->getMoveUrl(),
          ));
        } // if
        
        if($this->canCopy($user)) {
          $options->add('copy_to_project', array(
            'text' => lang('Copy to Project'),
            'url' => $this->getCopyUrl(),
          ));
        } // if
        
        if($this->canDelete($user)) {
          if($this->getState() == STATE_DELETED) {
            $options->add('restore_from_trash', array(
              'text' => lang('Restore from Trash'),
              'url' => $this->getUntrashUrl(),
              'method' => 'post',
            ));
          } else {
            $options->add('move_to_trash', array(
              'text' => lang('Move to Trash'),
              'url' => $this->getTrashUrl(),
              'method' => 'post',
            ));
          } // if
        } // if
        
        if($this->canChangeCompleteStatus($user)) {
          if($this->isCompleted()) {
            $options->add('reopen', array(
              'text' => lang('Reopen'),
              'url' => $this->getOpenUrl(),
              'method' => 'post',
            ));
          } else {
            $options->add('complete', array(
              'text' => lang('Complete'),
              'url' => $this->getCompleteUrl(),
              'method' => 'post',
            ));
          } // if
        } // if
        
        if($this->canSubscribe($user)) {
          if(Subscriptions::isSubscribed($user, $this)) {
            $options->add('unsubscribe', array(
              'text' => lang('Unsubscribe'),
              'url' => $this->getUnsubscribeUrl(),
              'method' => 'post',
            ));
          } else {
            $options->add('subscribe', array(
              'text' => lang('Subscribe'),
              'url' => $this->getSubscribeUrl(),
              'method' => 'post',
            ));
          } // if
        } // if
        
        if($this->canEdit($user)) {
          if($this->can_have_subscribers) {
            $options->add('manage_subscriptions', array(
              'text' => lang('Manage Subscriptions'),
              'url' => $this->getSubscriptionsUrl(),
            ));
          } // if
          if($this->can_have_attachments) {
            $options->add('manage_attachments', array(
              'text' => lang('Manage Attachments'),
              'url' => $this->getAttachmentsUrl(),
            ));
          } // if
        } // if
        
        event_trigger('on_project_object_options', array(&$options, $this, $user));
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
     * Return array of quick options
     *
     * @param User $user
     * @return NamedList
     */
    function getQuickOptions($user) {
      if(!isset($this->quick_options[$user->getId()])) {
        $options = new NamedList();
        
        if($this->canEdit($user)) {
          $options->add('edit', array(
            'text' => lang('Edit'),
            'url' => $this->getEditUrl(),
          ));
        } // if
        
        if($this->canChangeCompleteStatus($user)) {
          if($this->isCompleted()) {
            $options->add('reopen', array(
              'text' => lang('Reopen'),
              'url' => $this->getOpenUrl(),
              'method' => 'post',
            ));
          } else {
            $options->add('complete', array(
              'text' => lang('Complete'),
              'url' => $this->getCompleteUrl(),
              'method' => 'post',
            ));
          } // if
        } // if
        
        if($this->canSubscribe($user)) {
          if(Subscriptions::isSubscribed($user, $this)) {
            $options->add('unsubscribe', array(
              'text' => lang('Unsubscribe'),
              'url' => $this->getUnsubscribeUrl(),
              'method' => 'post',
            ));
          }  else {
            $options->add('subscribe', array(
              'text' => lang('Subscribe'),
              'url' => $this->getSubscribeUrl(),
              'method' => 'post',
            ));
          } // if
        } // if
        
        if($this->canSubtask($user)) {
          $options->add('new_task', array(
            'text' => lang('New Task'),
            'url' => $this->getPostTaskUrl(),
          ));
        } // if
        
        if($this->canSendReminder($user)) {
          $options->add('send_reminder', array(
            'text' => lang('Send Reminder'),
            'url' => $this->getSendReminderUrl(),
            'class' => 'send_reminder'
          ));
        } // if
        
        event_trigger('on_project_object_quick_options', array(&$options, $this, $user));
        $this->quick_options[$user->getId()] = $options;
      } // if
      
      return $this->quick_options[$user->getId()];
    } // getQuickOptions
    
    /**
     * Cached array of portal object quick options
     *
     * @var array
     */
    var $portal_quick_options = array();
    
    /**
     * Return array of portal object quick options
     *
     * @param Portal $portal
     * @param Commit $commit
     * @param string $file
     * @return array
     */
    function getPortalQuickOptions($portal, $commit = null, $file = null) {
    	if(!isset($this->portal_quick_options[$portal->getId()])) {
    		$options = new NamedList();
    		
    		if($this->canPortalSubtask($portal)) {
    			$options->add('new_task', array(
    				'text' => lang('New Task'),
    				'url'  => $this->getPortalPostTaskUrl($portal)
    			));
    		} // if
    		
    		event_trigger('on_portal_object_quick_options', array(&$options, $this, $portal, $commit, $file));
    		$this->portal_quick_options[$portal->getId()] = $options;
    	} // if
    	
    	return $this->portal_quick_options[$portal->getId()];
    } // getPortalQuickOptions
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Return object ID parameter name used in URL-s
     *
     * @param void
     * @return string
     */
    function getObjectIdParamName() {
      if($this->object_id_param_name === null) {
        $this->object_id_param_name = Inflector::underscore(get_class($this)) . '_id';
      } // if
      return $this->object_id_param_name;
    } // getObjectIdParamName
    
    /**
     * Validate before save
     * 
     * If $flags_only is true this function will only validate system flags: 
     * project_id, created_by_name, created_by_email and so on. Content fields 
     * will be skipped
     *
     * @param ValidationErrors $errors
     * @param boolean $flags_only
     * @return null
     */
    function validate(&$errors, $flags_only = false) {
      
      // ---------------------------------------------------
      //  Content
      // ---------------------------------------------------
      
      if(!$flags_only) {
        if(!$this->validatePresenceOf('name', 3)) {
          $errors->addError(lang('Minimal name value is 3 characters'), 'name');
        } // if
        if(!$this->validatePresenceOf('body', 3)) {
          $errors->addError(lang('Minimal content value is 3 characters'), 'body');
        } // if
      } // if
      
      // ---------------------------------------------------
      //  Flags
      // ---------------------------------------------------
      
      if(!$this->validatePresenceOf('project_id')) {
        $errors->addError(lang('Please select project'));
      } // if
      
      if(!$this->validatePresenceOf('type')) {
        $errors->addError(lang('Type flag value is required'));
      } // if
      
      if(!$this->validatePresenceOf('module')) {
        $errors->addError(lang('Module flag value is required'));
      } // if
      
      if(!$this->validatePresenceOf('created_by_name')) {
        $errors->addError(lang('Author name is required'));
      } // if
      
      if($this->validatePresenceOf('created_by_email')) {
        if(!is_valid_email($this->getCreatedByEmail())) {
          $errors->addError(lang('Authors email address is not valid'));
        } // if
      } else {
        $errors->addError(lang('Authors email address is required'));
      } // if
      
    } // validate
    
    /**
     * Save this object into the database
     *
     * @param void
     * @return boolean
     * @throws DBQueryError
     * @throws ValidationErrors
     */
    function save() {
      $is_new = $this->isNew();
      $modified_fields= $this->modified_fields;
      $old_values = $this->old_values;
      
      if($is_new) {
        $this->setType(get_class($this));
      } // if
      
      if($this->isModified()) {
        $this->setVersion($this->getVersion() + 1); // increment object version on save...
      } // if
      
      db_begin_work();
      $save = parent::save();
      
      if(!$save || is_error($save)) {
        db_rollback();
        return $save;
      } // if
      
      // Log activities...
      if($this->log_activities) {
        if($is_new) {
          if($this->log_creation) {
            if (instance_of($this, 'File')) {
              $activity_log = new NewFileActivityLog();
            } else {
              $activity_log = new ObjectCreatedActivityLog();
            } // if
            $activity_log->log($this, $this->getCreatedBy());
          } // if
        } else {
          if($this->log_update || $this->log_move_to_trash || $this->log_restore_from_trash) {
            $trashed = false;
            $restored = false;
            
            if(is_array($this->modified_fields) && in_array('state', $modified_fields)) {
              if(isset($old_values['state']) && ($old_values['state'] == STATE_DELETED) && ($this->getState() == STATE_VISIBLE)) {
                $restored = true;
              } // if
              
              if(isset($old_values['state']) && ($old_values['state'] == STATE_VISIBLE) && ($this->getState() == STATE_DELETED)) {
                $trashed = true;
              } // if
            } // if
            
            if($trashed) {
              if($this->log_move_to_trash) {
                $activity_log = new ObjectTrashedActivityLog();
                $activity_log->log($this);
              } // if
            } elseif($restored) {
              if($this->log_restore_from_trash) {
                $activity_log = new ObjectRestoredActivityLog();
                $activity_log->log($this);
              } // if
            } else {
              if($this->log_update) {
                $activity_log = new ObjectUpdatedActivityLog();
                $activity_log->log($this);
              } // if
            } // if
         } // if
        } // if
      } // if
      
      // Pending files
      if($this->can_have_attachments && is_foreachable($this->pending_files)) {
        foreach($this->pending_files as $pending_file) {
          $attachment = new Attachment();
    
          $attachment->setParent($this);
          
          if(isset($pending_file['created_by']) && (instance_of($pending_file['created_by'], 'User') || instance_of($pending_file['created_by'], 'AnonymousUser'))) {
            $attachment->setCreatedBy($pending_file['created_by']);
          } else {
            $attachment->setCreatedBy($this->getCreatedBy());
          } // if
          
          $attachment->setName($pending_file['name']);
          $attachment->setLocation(substr($pending_file['location'], strlen(UPLOAD_PATH) + 1));
          $attachment->setMimeType($pending_file['type']);
          $attachment->setSize($pending_file['size']);
          if (instance_of($this, 'File')) {
            $attachment->setAttachmentType(ATTACHMENT_TYPE_FILE_REVISION);
          } // if
          
          $save_attachment = $attachment->save();
          if(is_error($save_attachment)) {
            db_rollback();
            return $save_attachment;
          } // if
        } // foreach
        $this->pending_files = array(); // no more pending files
      } // if
      
      // Set assignees
      if($this->can_have_assignees && ($this->new_assignees !== false)) {
        $this->old_assignees = $is_new ? array(array(), 0) : Assignments::findAssignmentDataByObject($this);

        Assignments::deleteByObject($this);
        
        $object_id = $this->getId();
        if(is_array($this->new_assignees)) {
          list($assignees, $owner_id) = $this->new_assignees;
          if(is_foreachable($assignees)) {
            $user_ids = array();
            $to_insert = array();
            foreach($assignees as $user_id) {
              if(in_array($user_id, $user_ids)) {
                continue;
              } // if
              
              $is_owner = $user_id == $owner_id ? 1 : 0;
              $to_insert[] = "($user_id, $object_id, $is_owner)";
              
              $user_ids[] = $user_id;
            } // foreach
            
            // Insert assignments
            $insert = db_execute('INSERT INTO ' . TABLE_PREFIX . 'assignments VALUES ' . implode(', ', $to_insert));
            if(is_error($insert) && !$insert) {
              db_rollback();
              return $insert;
            } // if
            
          // Not array... Empty...
          } else {
            $assignees = array();
            $owner_id = 0;
          } // if
          
          // Clean up assignments cache
          clean_assignments_cache();
          
          // Make sure that all assignees are subscribed
          Subscriptions::subscribeUsers($assignees, $this, false);
          
          // Check if object is reassigned
          if(!$is_new) {
            $reassigned = false;
            
            if(is_array($this->old_assignees)) {
              list($old_assignees, $old_owner_id) = $this->old_assignees;
            } else {
              $old_assignees = array();
              $old_owner_id = 0;
            } // if
            
            if($owner_id != $old_owner_id) {
              $reassigned = true;
            } else {
              if(count($assignees) != count($old_assignees)) {
                $reassigned = true;
              } else {
                foreach($assignees as $assignee_id) {
                  if(!in_array($assignee_id, $old_assignees)) {
                    $reassigned = true;
                  } // if
                } // foerach
              } // if
            } // if
            
            if($reassigned) {
              event_trigger('on_project_object_reassigned', array(&$this, $this->old_assignees, array($assignees, $owner_id)));
            } else {
              $this->old_assignees = false;
            } // if
          } // if
        } // if
        
        $this->new_assignees = false; // reset
      } // if
      
      // Search index
      if(is_foreachable($this->searchable_fields)) {
        $update_search_index = false;
        
        // Do we need to update search index?
        foreach($this->searchable_fields as $field) {
          if(in_array($field, $modified_fields)) {
            $update_search_index = true;
            break;
          } // if
        } // foreach
        
        // We do... Prepare and if content is empty remove it from the index
        if($update_search_index) {
          $content = '';
          foreach($this->searchable_fields as $field) {
            $value = $this->getFieldValue($field);
            if($value) {
              $content .= $value . "\n\n";
            } // if
          } // foreach
          
          if($content) {
            search_index_set($this->getId(), 'ProjectObject', $content);
          } else {
            search_index_remove($this->getId(), 'ProjectObject');
          } // if
        } // if
      } // if
      
      // Update properties of child elements
      if(!$is_new) {
        $properties = array();
        if(in_array('visibility', $modified_fields)) {
          $properties['setVisibility'] = $this->getVisibility();
        } // if
        if(in_array('milestone_id', $modified_fields)) {
          $properties['setMilestoneId'] = $this->getMilestoneId();
        } // if
        
        $types = array();
        if($this->can_have_comments) {
          $types[] = 'Comment';
        } // if
        if($this->can_have_tasks) {
          $types[] = 'Task';
        } // if
        if($this->getHasTime()) {
          $types[] = 'TimeRecord';
        } // if
          
        ProjectObjects::updatePropertiesByParent($this, $properties, $types);
      } // if
      
      if(!$is_new && in_array('project_id', $modified_fields)) {
        ActivityLogs::updateProjectIdCache($this);
      } // if
      
      // Commit and done!
      db_commit();
      return true;
    } // save
    
    /**
     * Delete this object
     * 
     * If $drop_subitems is TRUE subitems will be delete from the database. If it 
     * is false relation will be nullified
     *
     * @param boolean $drop_subitems
     * @return boolean
     * @throws DBQueryError
     */
    function delete($drop_subitems = true) {
      db_begin_work();
      
      $delete = parent::delete();
      if(is_error($delete) || !$delete) {
        db_rollback();
        return $delete;
      } // if
      
      $subitems = $this->getSubitems();
      if(is_foreachable($subitems)) {
        foreach($subitems as $subitem) {
          
          if($drop_subitems) {
            $delete = $subitem->delete();
            if(is_error($delete)) {
              db_rollback();
              return $delete;
            } // if
            
          } else {
            $subitem->setParent(null, false);
            $save = $subitem->save();
            if(is_error($save)) {
              db_rollback();
              return $save;
            } // if
          } // if
          
        } // foreach
      } // if
      
      StarredObjects::deleteByObject($this);
      
      // Attachments
      if($this->can_have_attachments) {
        Attachments::deleteByObject($this);
      } // if
      
      // Subscriptions
      if($this->can_have_subscribers) {
        Subscriptions::deleteByParent($this);
      } // if
      
      // Asignments
      if($this->can_have_assignees) {
        Assignments::deleteByObject($this);
      } // if
      
      // Activity log
      if($this->log_activities) {
        ActivityLogs::deleteByObject($this);
      } // if
      
      // Reminders
      if($this->can_send_reminders) {
        Reminders::deleteByObject($this);
      } // if
      
      search_index_remove($this->getId(), 'ProjectObject');
      
      db_commit();
      return true;
    } // delete
    
    /**
     * Create a copy of this object and optionally save it
     *
     * @param boolean $save
     * @return ProjectObject
     */
    function copy($save = false) {
      $object_class = get_class($this);
      
      $copy = new $object_class();
      foreach($this->fields as $field) {
        if(!in_array($field, $this->primary_key)) {
          $copy->setFieldValue($field, $this->getFieldValue($field));
        } // if
      } // foreach
      
      if($save) {
        $copy->save();
      } // if
      
      return $copy;
    } // cop
    
    /**
     * Let this object know that its creation process is complete
     *
     * This method should be manually called every time we create a new object 
     * and finish everything we need to do on its creation (attach files and 
     * tasks, subscribe people and so on)
     * 
     * @param void
     * @return null
     */
    function ready() {
      event_trigger('on_project_object_ready', array(&$this));
    } // ready
    
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
      if($this->getState() <= STATE_DELETED) {
        return true;
      } // if
      
      $old_log_activities = $this->log_activities;
      if($silent) {
        $this->log_activities = false;
      } // if
      
      db_begin_work();
      $this->setState(STATE_DELETED);
      $save = $this->save();
      if(is_error($save) || !$save) {
        db_rollback();
        return $save;
      } // if
      
      $subitems = $this->getSubitems();
      if(is_foreachable($subitems)) {
        foreach($subitems as $subitem) {
          $subitem->moveToTrash(true);
        } // foreach
      } // if
      
      $this->log_activities = $old_log_activities;
      
      db_commit();
      event_trigger('on_project_object_trashed', array(&$this));
      
      return true;
    } // moveToTrash
    
    /**
     * Restore object and subitems from trash
     * 
     * If $check_parent_state is set to true, this object will not be restored 
     * if it has parent that is already in trash
     *
     * @param boolean $check_parent_state
     * @return boolean
     */
    function restoreFromTrash($check_parent_state = true) {
      if($this->getState() != STATE_DELETED) {
        return true;
      } // if
      
      if($check_parent_state) {
        $parent = $this->getParent();
        if(instance_of($parent, 'ProjectObject') && $parent->getState() == STATE_DELETED) {
          return new Error('Please restore parent object before restoring this object');
        } // if
      } // if
      
      db_begin_work();
      $this->setState(STATE_VISIBLE);
      $save = $this->save();
      if(is_error($save) || !$save) {
        db_rollback();
        return $save;
      } // if
      
      $subitems = $this->getSubitems();
      if(is_foreachable($subitems)) {
        foreach($subitems as $subitem) {
          $subitem->restoreFromTrash(false);
        } // foreach
      } // if
      
      db_commit();
      event_trigger('on_project_object_restored', array(&$this));
      
      return true;
    } // restoreFromTrash
  
  }

?>