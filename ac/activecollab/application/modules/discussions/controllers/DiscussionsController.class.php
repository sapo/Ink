<?php

  // We need ProjectsController
  use_controller('project', SYSTEM_MODULE);

  /**
   * Discussions controller
   *
   * @package activeCollab.modules.dicussions
   * @subpackage controllers
   */
  class DiscussionsController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = DISCUSSIONS_MODULE;
    
    /**
     * Selected discussion
     *
     * @var Discussion
     */
    var $active_discussion;
    
    /**
     * Enable categories support for this controller
     *
     * @var boolean
     */
    var $enable_categories = true;
    
    /**
     * Actions that are available through API
     *
     * @var array
     */
    var $api_actions = array('index', 'view', 'add', 'edit');
    
    /**
     * Construct discussions controller
     *
     * @param Request $request
     * @return DiscussionsController
     */
    function __construct($request) {
      parent::__construct($request);

      if($this->logged_user->getProjectPermission('discussion', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $discussions_url = discussions_module_url($this->active_project);
      
      $this->wireframe->addBreadCrumb(lang('Discussions'), $discussions_url);
      
      $add_discussion_url = false;
      if(Discussion::canAdd($this->logged_user, $this->active_project)) {
        if($this->active_category->isLoaded()) {
          $add_discussion_url = discussions_module_add_discussion_url($this->active_project, array(
            'category_id' => $this->active_category->getId(),
          ));
        } else {
          $add_discussion_url = discussions_module_add_discussion_url($this->active_project);
        } // if
        
        $this->wireframe->addPageAction(lang('New Discussion'), $add_discussion_url);
      } // if
      
      $discussion_id = $this->request->getId('discussion_id');
      if($discussion_id) {
        $this->active_discussion = ProjectObjects::findById($discussion_id);
      } // if
      
      if(instance_of($this->active_discussion, 'Discussion')) {
        $this->wireframe->addBreadCrumb($this->active_discussion->getName(), $this->active_discussion->getViewUrl());
      } else {
        $this->active_discussion = new Discussion();
      } // if
      
      $this->smarty->assign(array(
        'active_discussion'  => $this->active_discussion,
        'discussions_url'    => $discussions_url,
        'add_discussion_url' => $add_discussion_url,
        'page_tab'           => 'discussions',
      ));
    } // __construct
  
    /**
     * Show discussions module homepage
     *
     * @param void
     * @return null
     */
    function index() {
      if($this->request->isApiCall()) {
        $this->serveData(Discussions::findByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility(), 0, 25), 'discussions');
      } else {
        $category = null;
        $category_id = (integer) $this->request->get('category_id');
        if($category_id) {
          $category = Categories::findById($category_id);
        } // if
        
        $per_page = 15; // discussions per page
        $page = (integer) $this->request->get('page');
        if($page < 1) {
          $page = 1;
        } // if
        
        if(instance_of($category, 'Category')) {
          list($discussions, $pagination) = Discussions::paginateByCategory($category, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
        } else {
          list($discussions, $pagination) = Discussions::paginateByProject($this->active_project, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
        } // if
        
        $this->smarty->assign(array(
          'discussions' => $discussions,
          'pagination' => $pagination,
          'categories' => Categories::findByModuleSection($this->active_project, 'discussions', 'discussions'),
          'can_manage_categories' => $this->logged_user->isProjectLeader($this->active_project) || $this->logged_user->isProjectManager(),
        ));
      } // if
    } // index
    
    /**
     * Override view category page
     *
     * @param void
     * @return null
     */
    function view_category() {
      $this->redirectTo('project_discussions', array(
        'project_id' => $this->active_project->getId(),
        'category_id' => $this->request->getId('category_id')
      ));
    } // view_category
    
    /**
     * View specific discussion
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_discussion->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_discussion->canView($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if ($this->active_discussion->getIsPinned()) {
        $this->wireframe->addPageMessage(lang('<strong>Pinned</strong> - This discussion is pinned'), 'pinned');
      } // if
      
      if($this->request->isApiCall()) {
        $this->serveData($this->active_discussion, 'discussion', array(
          'describe_comments' => true,
        ));
      } else {
        ProjectObjectViews::log($this->active_discussion, $this->logged_user);
        
        $parent = $this->active_discussion->getParent();
        if(instance_of($parent, 'Category')) {
          $this->active_category = $parent;
          $this->smarty->assign('active_category', $parent);
        } // if
        
        $page = (integer) $this->request->get('page');
        if($page < 1) {
          $page = 1;
        } // if
        
        list($comments, $pagination) = $this->active_discussion->paginateComments($page, $this->active_discussion->comments_per_page, $this->logged_user->getVisibility());
        
        $this->smarty->assign(array(
          'category' => $this->active_discussion->getParent(),
          'comments' => $comments,
          'pagination' => $pagination,
          'counter' => ($page - 1) * $this->active_discussion->comments_per_page,
        ));
      } // if
    } // view
    
    /**
     * Create a new discussion
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      if(!Discussion::canAdd($this->logged_user, $this->active_project)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $discussion_data = $this->request->post('discussion');
      if(!is_array($discussion_data)) {
        $discussion_data = array(
          'milestone_id' => $this->request->get('milestone_id'),
          'visibility' => $this->active_project->getDefaultVisibility()
        );
        if($this->active_category->isLoaded()) {
          $discussion_data['parent_id'] = $this->active_category->getId();
        } // if
      } // if
      $this->smarty->assign('discussion_data', $discussion_data);
      
      if($this->request->isSubmitted()) {       
        db_begin_work();
        
        $this->active_discussion = new Discussion();
        
        attach_from_files($this->active_discussion, $this->logged_user);
        
        $this->active_discussion->setAttributes($discussion_data);
        $this->active_discussion->setProjectId($this->active_project->getId());
        $this->active_discussion->setCreatedBy($this->logged_user);
        $this->active_discussion->setState(STATE_VISIBLE);
        
        $save = $this->active_discussion->save();
        if($save && !is_error($save)) {
          $subscribers = array($this->logged_user->getId());
          if(is_foreachable($this->request->post('notify_users'))) {
            $subscribers = array_merge($subscribers, $this->request->post('notify_users'));
          } else {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          
          if(!in_array($this->active_project->getLeaderId(), $subscribers)) {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          
          Subscriptions::subscribeUsers($subscribers, $this->active_discussion);
          
          db_commit();
          $this->active_discussion->ready();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success('Discussion :discussion_name has been started', array('discussion_name' => $this->active_discussion->getName()));
            $this->redirectToUrl($this->active_discussion->getViewUrl());
          } else {
            $this->serveData($this->active_discussion, 'discussion');
          } // if
        } else {
          db_rollback();

          if($this->request->getFormat() == FORMAT_HTML) {
            $this->smarty->assign('errors', $save);
          } else {
            $this->serveData($save);
          } // if
        } // if
      } // if
    } // add
    
    /**
     * Quick add checklist
     *
     * @param void
     * @return null
     */
    function quick_add() {
      if(!Discussion::canAdd($this->logged_user, $this->active_project)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN, lang("You don't have permission for this action"), true, true);
      } // if
      
      $this->skip_layout = true;
      
      $discussion_data = $this->request->post('discussion');
      if (!is_array($discussion_data)) {
        $discussion_data = array(
          'visibility' => $this->active_project->getDefaultVisibility()
        );
      } // if
      $this->smarty->assign(array(
        'discussion_data' => $discussion_data,
        'quick_add_url' => assemble_url('project_discussions_quick_add', array('project_id' => $this->active_project->getId())),
      ));
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_discussion = new Discussion();
        
        if (count($_FILES > 0)) {
          attach_from_files($this->active_discussion, $this->logged_user);  
        } // if
        
        $this->active_discussion->setAttributes($discussion_data);
        $this->active_discussion->setBody(clean(array_var($discussion_data, 'body', null)));
        $this->active_discussion->setProjectId($this->active_project->getId());
        $this->active_discussion->setCreatedBy($this->logged_user);
        $this->active_discussion->setState(STATE_VISIBLE);
        
        $save = $this->active_discussion->save();
        if($save && !is_error($save)) {
          $subscribers = array($this->logged_user->getId());
          if(is_foreachable($this->request->post('notify_users'))) {
            $subscribers = array_merge($subscribers, $this->request->post('notify_users'));
          } else {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          if(!in_array($this->active_project->getLeaderId(), $subscribers)) {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          Subscriptions::subscribeUsers($subscribers, $this->active_discussion);
          
          db_commit();
          $this->active_discussion->ready();
          
          $this->smarty->assign(array(
            'active_discussion' => $this->active_discussion,
            'discussion_data' => array('visibility' => $this->active_project->getDefaultVisibility()),
            'project_id' => $this->active_project->getId()
          ));
        } else {
          db_rollback();
          $this->httpError(HTTP_ERR_OPERATION_FAILED, $save->getErrorsAsString(), true, true);
        } // if
      } // if
    } // quick_add
    
    /**
     * Upate discussion
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // ifs
      
      if($this->active_discussion->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_discussion->canEdit($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $discussion_data = $this->request->post('discussion');
      if(!is_array($discussion_data)) {
        $discussion_data = array(
          'name' => $this->active_discussion->getName(),
          'body' => $this->active_discussion->getBody(),
          'parent_id' => $this->active_discussion->getParentId(),
          'milestone_id' => $this->active_discussion->getMilestoneId(),
          'visibility' => $this->active_discussion->getVisibility(),
          'tags' => $this->active_discussion->getTags(),
        );
      } // if
      
      $this->smarty->assign('discussion_data', $discussion_data);
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $old_name = $this->active_discussion->getName();
        $this->active_discussion->setAttributes($discussion_data);
        
        $save = $this->active_discussion->save();
        if($save && !is_error($save)) {
          db_commit();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success('Discussion ":name" has been updated', array('name' => $old_name));
            $this->redirectToUrl($this->active_discussion->getViewUrl());
          } else {
            $this->serveData($this->active_discussion, 'discussion');;
          } // if
        } else {
          db_rollback();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            $this->smarty->assign('errors', $save);
          } else {
            $this->serveData($save);
          } // if
        } // if
      } // if
    } // edit
    
    /**
     * Export discussions
     *
     * @param void
     * @return null
     */
    function export() {
      $object_visibility = array_var($_GET, 'visibility', VISIBILITY_NORMAL);
      $exportable_modules = explode(',', array_var($_GET,'modules', null));
      if (!is_foreachable($exportable_modules)) {
        $exportable_modules = null;
      } // if
      
      require_once(PROJECT_EXPORTER_MODULE_PATH.'/models/ProjectExporterOutputBuilder.class.php');
      $output_builder = new ProjectExporterOutputBuilder($this->active_project, $this->smarty, $this->active_module, $exportable_modules);
      if (!$output_builder->createOutputFolder()) {
        $this->serveData($output_builder->execution_log, 'execution_log', null, FORMAT_JSON);
      } // if
      $output_builder->createAttachmentsFolder();
      
      $module_objects = Discussions::findByProject($this->active_project, STATE_VISIBLE, $object_visibility);
      $module_categories = Categories::findByModuleSection($this->active_project,$this->active_module,$this->active_module);
      
      $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'index');
      $output_builder->smarty->assign('categories',$module_categories);
      $output_builder->smarty->assign('objects', $module_objects);
      $output_builder->outputToFile('index');
                  
      // export files by categories
      if (is_foreachable($module_categories)) {
        foreach ($module_categories as $module_category) {
          if (instance_of($module_category,'Category')) {
            $objects = ProjectObjects::find(array(
              'conditions' => array('parent_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?',$module_category->getId() ,$this->active_project->getId(), 'Discussion', STATE_VISIBLE, $object_visibility),
              'order'      => 'boolean_field_1, datetime_field_1 DESC',
            ));
            $output_builder->smarty->assign(array(
              'current_category' => $module_category,
              'objects' => $objects,
            ));
            $output_builder->outputToFile('category_'.$module_category->getId());
          } // if
        } // foreach
      } // if
            
      // export discussions
      if (is_foreachable($module_objects)) {
        $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'object');
        foreach ($module_objects as $module_object) {
          if (instance_of($module_object,'Discussion')) {
            $comments = $module_object->getComments($object_visibility);
          	$output_builder->smarty->assign(array(
          	'object' => $module_object,
          	));
          	$output_builder->smarty->assign('comments',$comments);
            $output_builder->outputToFile('discussion_'.$module_object->getId());
            $output_builder->outputObjectsAttachments($comments);
            $output_builder->outputAttachments($module_object->getAttachments());
          } // if
        } // foreach
      } // if
      
      $this->serveData($output_builder->execution_log, 'execution_log', null, FORMAT_JSON);
    } // export
    
    /**
     * Pin specific discussion
     * 
     * @param void
     * @return null
     *
     */
    function pin() {
      if (!$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST, null, true, $this->request->isAsyncCall());
      } // if
      
      if (!$this->active_discussion->canChangePinedState($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isAsyncCall());
      } // if
      
      $this->active_discussion->setIsPinned(true);
      
      $save = $this->active_discussion->save();
      if($save && !is_error($save)) {
        flash_success('Discussion has been successfully pinned');
        
        $activity_log = new DiscussionPinnedActivityLog();
        $activity_log->log($this->active_discussion, $this->logged_user);
      } else {
        flash_error('Failed to pin selected discussion');
      } // if
      $this->redirectToReferer($this->active_discussion->getViewUrl());
    } // pin
    
    /**
     * Unpin specific discussion
     * 
     * @param void
     * @return null
     *
     */
    function unpin() {
      if (!$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST, null, true, $this->request->isAsyncCall());
      } // if
      
      if (!$this->active_discussion->canChangePinedState($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isAsyncCall());
      } // if
      
      $this->active_discussion->setIsPinned(false);
      
      $save = $this->active_discussion->save();
      if($save && !is_error($save)) {
        flash_success('Discussion has been successfully unpinned');
        
        $activity_log = new DiscussionUnpinnedActivityLog();
        $activity_log->log($this->active_discussion, $this->logged_user);
      } else {
        flash_error('Failed to unpin selected discussion');
      } // if
      $this->redirectToReferer($this->active_discussion->getViewUrl());
    } // pin
  
  } // DiscussionsController

?>